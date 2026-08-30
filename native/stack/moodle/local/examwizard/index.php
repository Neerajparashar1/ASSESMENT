<?php
// Exam Control - the beginner-friendly home for exam-cell staff.
//   - "Getting started" checklist (auto-detected, dismissible)
//   - Quick actions
//   - Your exams (status + Monitor / Grades / .seb links)
//   - At a glance + Help

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/questionlib.php');

require_login();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/examwizard/index.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('home_title', 'local_examwizard'));
$PAGE->set_heading(get_string('home_title', 'local_examwizard'));

// Dismiss / restore the checklist.
if (optional_param('hidechecklist', 0, PARAM_BOOL) && confirm_sesskey()) {
    set_user_preference('local_examwizard_hidechecklist', 1);
    redirect($PAGE->url);
}
if (optional_param('showchecklist', 0, PARAM_BOOL) && confirm_sesskey()) {
    unset_user_preference('local_examwizard_hidechecklist');
    redirect($PAGE->url);
}

// ---------------------------------------------------------------------
// Which courses can this user run exams in?
// ---------------------------------------------------------------------
$courses = [];
foreach (enrol_get_my_courses(['id', 'fullname', 'shortname'], 'fullname ASC') as $c) {
    $ctx = context_course::instance($c->id);
    if (has_capability('mod/quiz:addinstance', $ctx) || has_capability('moodle/course:manageactivities', $ctx)) {
        $courses[$c->id] = $c;
    }
}
if (is_siteadmin()) {
    foreach (get_courses('all', 'c.fullname ASC', 'c.id, c.fullname, c.shortname') as $c) {
        if ($c->id != SITEID) {
            $courses[$c->id] = $c;
        }
    }
}

if (!$courses) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nocourses', 'local_examwizard'),
        \core\output\notification::NOTIFY_INFO);
    echo $OUTPUT->footer();
    exit;
}

$firstcourseid = array_key_first($courses);
$s = fn($id, $a = null) => get_string($id, 'local_examwizard', $a);

// ---------------------------------------------------------------------
// Gather the exams + counts.
// ---------------------------------------------------------------------
$now = time();
$exams = [];
$stats = ['students' => 0, 'courses' => count($courses), 'exams' => 0, 'live' => 0];

$studentrole = $DB->get_field('role', 'id', ['shortname' => 'student']);
if (is_siteadmin()) {
    $stats['students'] = $DB->count_records_sql(
        'SELECT COUNT(DISTINCT ra.userid) FROM {role_assignments} ra WHERE ra.roleid = ?', [$studentrole]);
} else {
    [$insql, $inparams] = $DB->get_in_or_equal(array_keys($courses));
    $stats['students'] = $DB->count_records_sql("
        SELECT COUNT(DISTINCT ra.userid)
          FROM {role_assignments} ra
          JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = " . CONTEXT_COURSE . "
         WHERE ra.roleid = ? AND ctx.instanceid $insql", array_merge([$studentrole], $inparams));
}

foreach ($courses as $c) {
    $modinfo = get_fast_modinfo($c->id);
    foreach ($modinfo->get_instances_of('quiz') as $cm) {
        $q = $DB->get_record('quiz', ['id' => $cm->instance], 'id, name, timeopen, timeclose, timelimit');
        if (!$q) {
            continue;
        }
        $stats['exams']++;

        // status
        if ($q->timeopen && $q->timeopen > $now) {
            $status = 'scheduled';
        } else if ($q->timeclose && $q->timeclose < $now) {
            $status = 'closed';
        } else if (($q->timeopen && $q->timeopen <= $now) || $q->timeopen == 0) {
            $status = ($q->timeopen || $q->timeclose) ? 'live' : 'open';
        } else {
            $status = 'open';
        }
        if ($status === 'live') {
            $stats['live']++;
        }

        $hasseb = $DB->record_exists_select('quizaccess_seb_quizsettings',
            'quizid = ? AND requiresafeexambrowser > 0', [$q->id]);
        $started = $DB->count_records('quiz_attempts', ['quiz' => $q->id, 'state' => 'inprogress']);
        $submittedusers = $DB->count_records('quiz_grades', ['quiz' => $q->id]);
        $modcontext = context_module::instance($cm->id);
        $enrolled = count_enrolled_users($modcontext, 'mod/quiz:attempt');

        $exams[] = [
            'name' => format_string($q->name),
            'course' => format_string($c->shortname),
            'cmid' => $cm->id,
            'courseid' => $c->id,
            'status' => $status,
            'seb' => $hasseb,
            'started' => $started,
            'submitted' => $submittedusers,
            'enrolled' => $enrolled,
            'notstarted' => max(0, $enrolled - $submittedusers - $started),
            'closetime' => (int) $q->timeclose,
            'when' => $q->timeopen ? userdate($q->timeopen, get_string('strftimedatetimeshort')) : '',
        ];
    }
}
// live first, then scheduled, open, closed
$order = ['live' => 0, 'open' => 1, 'scheduled' => 2, 'closed' => 3];
usort($exams, fn($a, $b) => ($order[$a['status']] <=> $order[$b['status']]) ?: strcmp($a['name'], $b['name']));

$liveexams = array_values(array_filter($exams,
    fn($e) => $e['status'] === 'live' || ($e['status'] === 'open' && $e['started'] > 0)));
$recentexams = array_values(array_filter($exams,
    fn($e) => $e['submitted'] > 0));
usort($recentexams, fn($a, $b) => $b['closetime'] <=> $a['closetime']);
$recentexams = array_slice($recentexams, 0, 5);

// ---------------------------------------------------------------------
// Checklist status (auto-detected).
// ---------------------------------------------------------------------
$sebquit = (string) get_config('quizaccess_seb', 'quitpassword');
$checks = [
    'branding' => stripos((string) $SITE->fullname, 'ITM') !== false,
    'students' => $stats['students'] > 0,
    'exam' => $stats['exams'] > 0,
    'sebpw' => trim($sebquit) !== '',
    'testrun' => $DB->record_exists('quiz_attempts', ['state' => 'finished']),
];
$done = count(array_filter($checks));

// =====================================================================
//  Render
// =====================================================================
$reloadjs = $liveexams ? "setTimeout(function(){ location.reload(); }, 30000);" : "";
$PAGE->requires->js_amd_inline("
    require(['jquery'], function($){
        $('.ew-copy').on('click', function(){
            var t = $(this).data('copy');
            navigator.clipboard && navigator.clipboard.writeText(t);
            $(this).text('" . get_string('copied', 'local_examwizard') . "');
        });
        $('.ew-reveal').on('click', function(e){
            e.preventDefault();
            $('#' + $(this).data('target')).toggleClass('ew-masked');
        });
        $reloadjs
    });
");

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('home_title', 'local_examwizard'));
echo html_writer::div($s('home_intro'), 'lead text-muted mb-3');

// ---- at a glance ----
echo html_writer::start_div('ew-glance');
foreach ([
    ['exams', $stats['exams'], $s('g_exams')],
    ['students', $stats['students'], $s('g_students')],
    ['courses', $stats['courses'], $s('g_subjects')],
    ['live', $stats['live'], $s('g_live')],
] as [$k, $v, $label]) {
    echo html_writer::div(
        html_writer::tag('span', $v, ['class' => 'ew-glance-n']) .
        html_writer::tag('span', $label, ['class' => 'ew-glance-l']),
        'ew-glance-item ew-glance-' . $k);
}
echo html_writer::end_div();

// ---- LIVE NOW spotlight ----
if ($liveexams) {
    echo html_writer::start_div('ew-card ew-card-live');
    echo html_writer::tag('h3', $s('live_title'), ['class' => 'ew-card-h']);
    echo html_writer::div($s('live_refresh'), 'ew-live-refresh small text-muted mb-2');
    foreach ($liveexams as $e) {
        $bar = $e['enrolled']
            ? round(($e['submitted'] / max(1, $e['enrolled'])) * 100)
            : 0;
        echo html_writer::start_div('ew-live-item');
        echo html_writer::div(
            html_writer::tag('span', s($e['name']), ['class' => 'ew-ename']) .
            html_writer::tag('span', s($e['course']), ['class' => 'ew-emeta']), 'ew-live-name');
        echo html_writer::start_div('ew-live-stats');
        echo html_writer::tag('span', $e['started'] . ' ' . $s('live_inprogress'), ['class' => 'ew-live-n ew-live-progress']);
        echo html_writer::tag('span', $e['submitted'] . ' ' . $s('live_submitted'), ['class' => 'ew-live-n ew-live-done']);
        echo html_writer::tag('span', $e['notstarted'] . ' ' . $s('live_notstarted'), ['class' => 'ew-live-n ew-live-wait']);
        echo html_writer::end_div();
        echo html_writer::div(html_writer::div('', 'ew-live-fill', ['style' => 'width:' . $bar . '%']), 'ew-live-bar');
        echo html_writer::div(
            html_writer::link(new moodle_url('/local/examwizard/control.php', ['cmid' => $e['cmid']]),
                $s('lc_control'), ['class' => 'btn btn-primary btn-sm mr-2']) .
            html_writer::link(new moodle_url('/mod/quiz/report.php', ['id' => $e['cmid'], 'mode' => 'overview']),
                $s('live_monitor'), ['class' => 'btn btn-outline-secondary btn-sm mr-2']) .
            html_writer::link(new moodle_url('/local/examwizard/results.php', ['cmid' => $e['cmid']]),
                $s('ye_results'), ['class' => 'btn btn-outline-secondary btn-sm']),
            'mt-2');
        echo html_writer::end_div();
    }
    echo html_writer::end_div();
}

// ---- CARD 1 : getting started ----
if (!get_user_preferences('local_examwizard_hidechecklist')) {
    $items = [
        ['branding', $s('chk_branding'), new moodle_url('/admin/settings.php', ['section' => 'themesettingboost_union'])],
        ['students', $s('chk_students'), new moodle_url('/local/examwizard/students.php', ['courseid' => $firstcourseid])],
        ['exam', $s('chk_exam'), new moodle_url('/local/examwizard/wizard.php', ['courseid' => $firstcourseid])],
        ['sebpw', $s('chk_sebpw'), new moodle_url('/local/examwizard/seb.php')],
        ['testrun', $s('chk_testrun'), new moodle_url('/local/examwizard/index.php')],
    ];
    echo html_writer::start_div('ew-card ew-card-checklist');
    echo html_writer::start_div('ew-card-head');
    echo html_writer::tag('h3', $s('chk_title'));
    echo html_writer::tag('span', $s('chk_progress', (object) ['done' => $done, 'total' => count($checks)]),
        ['class' => 'ew-chk-progress']);
    echo html_writer::link(new moodle_url($PAGE->url, ['hidechecklist' => 1, 'sesskey' => sesskey()]),
        $s('chk_dismiss'), ['class' => 'ew-chk-dismiss']);
    echo html_writer::end_div();
    echo html_writer::start_tag('ul', ['class' => 'ew-checklist']);
    foreach ($items as [$key, $label, $url]) {
        $ok = !empty($checks[$key]);
        echo html_writer::tag('li',
            html_writer::tag('span', $ok ? '✓' : '', ['class' => 'ew-chk-box']) .
            ($ok ? html_writer::tag('span', $label)
                 : html_writer::link($url, $label)),
            ['class' => $ok ? 'ew-chk-done' : '']);
    }
    echo html_writer::end_tag('ul');
    echo html_writer::end_div();
} else {
    echo html_writer::div(html_writer::link(
        new moodle_url($PAGE->url, ['showchecklist' => 1, 'sesskey' => sesskey()]),
        $s('chk_show')), 'mb-2 small');
}

// ---- CARD 2 : quick actions ----
echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('qa_title'), ['class' => 'ew-card-h']);
echo html_writer::start_div('ew-actions');
$actions = [
    ['t/add', $s('qa_createexam'), new moodle_url('/local/examwizard/wizard.php', ['courseid' => $firstcourseid]), 'primary'],
    ['i/import', $s('qa_upload'), new moodle_url('/local/examwizard/questions.php', ['courseid' => $firstcourseid]), 'secondary'],
    ['i/users', $s('qa_students'), new moodle_url('/local/examwizard/students.php', ['courseid' => $firstcourseid]), 'secondary'],
    ['i/down', $s('qa_seb'), new moodle_url('/local/examwizard/seb.php'), 'secondary'],
];
foreach ($actions as [$icon, $label, $url, $style]) {
    echo html_writer::link($url,
        $OUTPUT->pix_icon($icon, '') . html_writer::tag('span', $label),
        ['class' => 'ew-action ew-action-' . $style]);
}
echo html_writer::end_div();
echo html_writer::end_div();

// ---- CARD 3 : your exams ----
echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('ye_title'), ['class' => 'ew-card-h']);
if (!$exams) {
    echo html_writer::div($s('ye_empty'), 'text-muted');
} else {
    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('ye_exam')) . html_writer::tag('th', $s('ye_status')) .
        html_writer::tag('th', $s('ye_attempts')) . html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($exams as $e) {
        $statusbadge = html_writer::tag('span', $s('stat_' . $e['status']),
            ['class' => 'ew-badge ew-badge-' . $e['status']]);
        $links = '';
        if ($e['status'] === 'live' || $e['started'] > 0) {
            $links .= html_writer::link(new moodle_url('/local/examwizard/control.php',
                ['cmid' => $e['cmid']]), $s('lc_control'), ['class' => 'ew-tlink ew-tlink-strong']);
        }
        $links .= html_writer::link(new moodle_url('/mod/quiz/report.php',
            ['id' => $e['cmid'], 'mode' => 'overview']), $s('ye_monitor'), ['class' => 'ew-tlink']);
        $links .= html_writer::link(new moodle_url('/local/examwizard/results.php',
            ['cmid' => $e['cmid']]), $s('ye_results'), ['class' => 'ew-tlink']);
        if ($e['seb']) {
            $links .= html_writer::link(new moodle_url('/mod/quiz/accessrule/seb/config.php',
                ['cmid' => $e['cmid']]), $s('ye_seb'), ['class' => 'ew-tlink']);
        }
        echo html_writer::tag('tr',
            html_writer::tag('td',
                html_writer::div(s($e['name']), 'ew-ename') .
                html_writer::div(s($e['course']) . ($e['when'] ? ' · ' . s($e['when']) : ''), 'ew-emeta')) .
            html_writer::tag('td', $statusbadge) .
            html_writer::tag('td', $s('ye_attcount',
                (object) ['live' => $e['started'], 'done' => $e['submitted']])) .
            html_writer::tag('td', $links, ['class' => 'ew-elinks']));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}
echo html_writer::end_div();

// ---- CARD : recent results ----
if ($recentexams) {
    echo html_writer::start_div('ew-card');
    echo html_writer::tag('h3', $s('rr_title'), ['class' => 'ew-card-h']);
    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('ye_exam')) . html_writer::tag('th', $s('rr_submitted')) .
        html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($recentexams as $e) {
        echo html_writer::tag('tr',
            html_writer::tag('td',
                html_writer::div(s($e['name']), 'ew-ename') .
                html_writer::div(s($e['course']), 'ew-emeta')) .
            html_writer::tag('td', $e['submitted'] . ' / ' . $e['enrolled']) .
            html_writer::tag('td',
                html_writer::link(new moodle_url('/local/examwizard/results.php', ['cmid' => $e['cmid']]),
                    $s('ye_results'), ['class' => 'ew-tlink']) .
                html_writer::link(new moodle_url('/local/examwizard/results.php',
                    ['cmid' => $e['cmid'], 'export' => 'csv']), $s('rr_export'), ['class' => 'ew-tlink']),
                ['class' => 'ew-elinks']));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
    echo html_writer::end_div();
}

// ---- CARD : help / references ----
echo html_writer::start_div('ew-card ew-card-help');
echo html_writer::tag('h3', $s('help_title'), ['class' => 'ew-card-h']);
echo html_writer::start_tag('dl', ['class' => 'ew-help']);
echo html_writer::tag('dt', $s('help_sebpw'));
$cansebconfig = has_capability('moodle/site:config', context_system::instance());
if (trim($sebquit) !== '') {
    echo html_writer::tag('dd',
        html_writer::tag('code', $sebquit, ['id' => 'ew-sebpw', 'class' => 'ew-masked']) . ' ' .
        html_writer::link('#', $s('help_reveal'), ['class' => 'ew-reveal', 'data-target' => 'ew-sebpw']) . ' ' .
        html_writer::tag('button', $s('help_copy'),
            ['type' => 'button', 'class' => 'ew-copy btn btn-sm btn-outline-secondary', 'data-copy' => $sebquit]) .
        ($cansebconfig ? ' ' . html_writer::link(new moodle_url('/local/examwizard/seb.php'),
            $s('seb_change'), ['class' => 'ml-1']) : ''));
} else {
    echo html_writer::tag('dd', $cansebconfig
        ? html_writer::link(new moodle_url('/local/examwizard/seb.php'), $s('help_setsebpw'))
        : $s('seb_current_none'));
}
echo html_writer::tag('dt', $s('help_studentlogin'));
echo html_writer::tag('dd', $s('help_studentlogin_d', $CFG->wwwroot));
echo html_writer::tag('dt', $s('help_distribute'));
echo html_writer::tag('dd', $s('help_distribute_d'));
echo html_writer::end_tag('dl');
echo html_writer::end_div();

echo $OUTPUT->footer();
