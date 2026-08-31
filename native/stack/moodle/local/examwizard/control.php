<?php
// Exam Control - live control of one ongoing exam.
//   Whole exam: pause (close now) / reopen / end & submit everyone.
//   Per candidate: end & submit now / give extra time / resume / delete attempt.

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$cmid = required_param('cmid', PARAM_INT);

[$course, $cm] = get_course_and_cm_from_cmid($cmid, 'quiz');
require_login($course, false, $cm);

$context = context_module::instance($cm->id);
// Seeing the control screen needs the quiz reports capability; steering the
// running exam needs either mod/quiz:manage (teachers/managers) or the
// dedicated local/examwizard:control (hall invigilators - no settings/grades).
require_capability('mod/quiz:viewreports', $context);
$canmanage  = has_capability('mod/quiz:manage', $context);
$cancontrol = $canmanage || has_capability('local/examwizard:control', $context);
if (!$cancontrol) {
    require_capability('local/examwizard:control', $context);
}

$quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
$baseurl = new moodle_url('/local/examwizard/control.php', ['cmid' => $cmid]);
$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('lc_title', 'local_examwizard'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'), new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('lc_title', 'local_examwizard'));

$s = fn($id, $a = null) => get_string($id, 'local_examwizard', $a);
$now = time();

// ---------------------------------------------------------------------
// Actions (POST + sesskey).
// ---------------------------------------------------------------------
$action = optional_param('action', '', PARAM_ALPHA);
if ($action && confirm_sesskey() && data_submitted()) {
    $attemptid = optional_param('attempt', 0, PARAM_INT);
    $minutes = optional_param('minutes', 15, PARAM_INT);
    $hours = optional_param('hours', 2, PARAM_INT);
    $msg = '';

    $finishone = function (int $aid) use ($quiz) {
        $a = \mod_quiz\quiz_attempt::create($aid);
        if ($a->get_quizid() != $quiz->id) {
            return;
        }
        if ($a->get_state() === \mod_quiz\quiz_attempt::IN_PROGRESS
                || $a->get_state() === \mod_quiz\quiz_attempt::OVERDUE) {
            $a->process_finish(time(), false);
        }
    };

    switch ($action) {
        case 'pauseexam':
            $DB->set_field('quiz', 'timeclose', $now, ['id' => $quiz->id]);
            quiz_update_open_attempts(['quizid' => $quiz->id]);
            $msg = $s('lc_msg_paused');
            break;

        case 'reopenexam':
            $DB->set_field('quiz', 'timeclose', $now + $hours * 3600, ['id' => $quiz->id]);
            if ($quiz->timeopen && $quiz->timeopen > $now) {
                $DB->set_field('quiz', 'timeopen', $now - 60, ['id' => $quiz->id]);
            }
            quiz_update_open_attempts(['quizid' => $quiz->id]);
            $msg = $s('lc_msg_reopened', $hours);
            break;

        case 'endall':
            $ids = $DB->get_fieldset_select('quiz_attempts', 'id',
                'quiz = ? AND state IN (?, ?)',
                [$quiz->id, \mod_quiz\quiz_attempt::IN_PROGRESS, \mod_quiz\quiz_attempt::OVERDUE]);
            foreach ($ids as $aid) {
                $finishone((int) $aid);
            }
            $DB->set_field('quiz', 'timeclose', $now, ['id' => $quiz->id]);
            quiz_update_open_attempts(['quizid' => $quiz->id]);
            $msg = $s('lc_msg_endedall', count($ids));
            break;

        case 'submit':
            if ($attemptid) {
                $finishone($attemptid);
                $msg = $s('lc_msg_submitted');
            }
            break;

        case 'resume':
            if ($attemptid) {
                $a = \mod_quiz\quiz_attempt::create($attemptid);
                if ($a->get_quizid() == $quiz->id
                        && in_array($a->get_state(), [\mod_quiz\quiz_attempt::ABANDONED, \mod_quiz\quiz_attempt::OVERDUE], true)
                        && method_exists($a, 'process_reopen_abandoned')) {
                    $a->process_reopen_abandoned(time());
                    $msg = $s('lc_msg_resumed');
                } else {
                    $msg = $s('lc_msg_cantresume');
                }
            }
            break;

        case 'extend':
            if ($attemptid && ($quiz->timelimit > 0 || $quiz->timeclose > 0)) {
                $att = $DB->get_record('quiz_attempts', ['id' => $attemptid, 'quiz' => $quiz->id], '*', MUST_EXIST);
                $add = max(1, $minutes) * 60;
                $ov = $DB->get_record('quiz_overrides',
                    ['quiz' => $quiz->id, 'userid' => $att->userid, 'groupid' => null]);

                $fields = ['quiz' => $quiz->id, 'userid' => $att->userid, 'groupid' => null,
                    'timeopen' => null, 'timeclose' => null, 'timelimit' => null,
                    'attempts' => null, 'password' => null];
                if ($quiz->timelimit > 0) {
                    $base = ($ov && $ov->timelimit) ? $ov->timelimit : $quiz->timelimit;
                    $fields['timelimit'] = $base + $add;
                }
                if ($quiz->timeclose > 0) {
                    $base = ($ov && $ov->timeclose) ? $ov->timeclose : $quiz->timeclose;
                    $fields['timeclose'] = max($now, $base) + $add;
                }
                if ($ov) {
                    $DB->update_record('quiz_overrides', (object) (['id' => $ov->id] + array_filter(
                        $fields, fn($v, $k) => in_array($k, ['timelimit', 'timeclose'], true) && $v !== null,
                        ARRAY_FILTER_USE_BOTH)));
                } else {
                    $DB->insert_record('quiz_overrides', (object) $fields);
                }
                quiz_update_open_attempts(['quizid' => $quiz->id]);
                $msg = $s('lc_msg_extended', $minutes);
            } else {
                $msg = $s('lc_msg_notimed');
            }
            break;

        case 'delete':
            // Destructive and unrecoverable - teachers/managers only, never a
            // plain hall invigilator.
            require_capability('mod/quiz:manage', $context);
            if ($attemptid) {
                $att = $DB->get_record('quiz_attempts', ['id' => $attemptid, 'quiz' => $quiz->id], '*', MUST_EXIST);
                quiz_delete_attempt($att, $quiz);
                $msg = $s('lc_msg_deleted');
            }
            break;
    }

    quiz_update_grades($quiz);
    purge_all_caches();
    redirect($baseurl, $msg, null, \core\output\notification::NOTIFY_SUCCESS);
}

// ---------------------------------------------------------------------
// Data for the view.
// ---------------------------------------------------------------------
$quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);   // re-read after any action
[$esql, $eparams] = get_enrolled_sql($context, 'mod/quiz:attempt');
$sql = "SELECT qa.id, qa.userid, qa.attempt, qa.state, qa.timestart, qa.timefinish, qa.sumgrades,
               u.firstname, u.lastname, u.username
          FROM {quiz_attempts} qa
          JOIN {user} u ON u.id = qa.userid
          JOIN ($esql) je ON je.id = u.id
         WHERE qa.quiz = :quizid AND qa.preview = 0
      ORDER BY (qa.state = 'inprogress') DESC, qa.timestart DESC";
$attempts = $DB->get_records_sql($sql, $eparams + ['quizid' => $quiz->id]);

$inprogress = 0;
foreach ($attempts as $a) {
    if ($a->state === 'inprogress') {
        $inprogress++;
    }
}
$examopen = (!$quiz->timeclose || $quiz->timeclose > $now) && (!$quiz->timeopen || $quiz->timeopen <= $now);

// ---------------------------------------------------------------------
// Render
// ---------------------------------------------------------------------
$actionform = function (string $action, string $label, string $btnclass, string $confirm, array $hidden = [])
        use ($baseurl) {
    $o = html_writer::start_tag('form', ['method' => 'post', 'action' => $baseurl->out(false),
        'class' => 'd-inline', 'onsubmit' => "return confirm('" . addslashes($confirm) . "');"]);
    $o .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    $o .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => $action]);
    foreach ($hidden as $k => $v) {
        $o .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $k, 'value' => $v]);
    }
    $o .= html_writer::tag('button', $label, ['type' => 'submit', 'class' => 'btn btn-sm ' . $btnclass]);
    $o .= html_writer::end_tag('form');
    return $o;
};

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($quiz->name));
echo html_writer::div($s('lc_intro'), 'text-muted mb-3');

// ---- whole-exam controls ----
echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('lc_examcontrols'), ['class' => 'ew-card-h']);
echo html_writer::div(
    $s('lc_state') . ': ' .
    html_writer::tag('span', $examopen ? $s('stat_open') : $s('stat_closed'),
        ['class' => 'ew-badge ew-badge-' . ($examopen ? 'open' : 'closed')]) .
    ' · ' . $s('lc_inprogress_n', $inprogress),
    'mb-2');
echo html_writer::div(
    ($examopen
        ? $actionform('pauseexam', $s('lc_pause'), 'btn-outline-danger', $s('lc_confirm_pause'))
        : $actionform('reopenexam', $s('lc_reopen'), 'btn-outline-primary', $s('lc_confirm_reopen'), ['hours' => 2])
    ) . ' ' .
    $actionform('endall', $s('lc_endall'), 'btn-danger', $s('lc_confirm_endall')),
    'ew-review-actions');
echo html_writer::end_div();

// ---- per-candidate ----
echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('lc_candidates'), ['class' => 'ew-card-h']);
if (!$attempts) {
    echo html_writer::div($s('lc_noattempts'), 'text-muted');
} else {
    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('st_name')) . html_writer::tag('th', $s('rs_state')) .
        html_writer::tag('th', $s('lc_started')) . html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    $statelabels = [
        'inprogress' => $s('rs_st_inprogress'), 'finished' => $s('rs_st_finished'),
        'abandoned' => $s('rs_st_abandoned'), 'overdue' => $s('lc_st_overdue'),
    ];
    foreach ($attempts as $a) {
        $acts = '';
        if (in_array($a->state, ['inprogress', 'overdue'], true)) {
            $acts .= $actionform('submit', $s('lc_endsubmit'), 'btn-outline-danger',
                $s('lc_confirm_submit', fullname($a)), ['attempt' => $a->id]) . ' ';
            if ($quiz->timelimit > 0 || $quiz->timeclose > 0) {
                $acts .= $actionform('extend', $s('lc_extend15'), 'btn-outline-secondary',
                    $s('lc_confirm_extend', fullname($a)), ['attempt' => $a->id, 'minutes' => 15]) . ' ';
            }
        }
        if (in_array($a->state, ['abandoned', 'overdue'], true)) {
            $acts .= $actionform('resume', $s('lc_resume'), 'btn-outline-primary',
                $s('lc_confirm_resume', fullname($a)), ['attempt' => $a->id]) . ' ';
        }
        if ($canmanage) {
            $acts .= $actionform('delete', $s('lc_delete'), 'btn-outline-danger',
                $s('lc_confirm_delete', fullname($a)), ['attempt' => $a->id]);
        }

        echo html_writer::tag('tr',
            html_writer::tag('td',
                html_writer::div(fullname($a), 'ew-ename') .
                html_writer::div('@' . s($a->username) . ' · ' . $s('lc_attemptn', $a->attempt), 'ew-emeta')) .
            html_writer::tag('td', html_writer::tag('span', $statelabels[$a->state] ?? $a->state,
                ['class' => 'ew-badge ew-badge-' . ($a->state === 'inprogress' ? 'live'
                    : ($a->state === 'finished' ? 'closed' : 'scheduled'))])) .
            html_writer::tag('td', $a->timestart ? userdate($a->timestart, get_string('strftimetime')) : '–') .
            html_writer::tag('td', $acts, ['class' => 'ew-elinks']),
            ['class' => $a->state === 'inprogress' ? '' : 'ew-row-bad']);
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}
echo html_writer::end_div();

echo html_writer::div(
    html_writer::link(new moodle_url('/local/examwizard/index.php'), $s('lc_back'),
        ['class' => 'btn btn-link pl-0']), 'mt-2');

echo $OUTPUT->footer();
