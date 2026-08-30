<?php
// Exam Control - results summary + one-click CSV export for one exam.
//   ?cmid=N              -> view
//   ?cmid=N&export=csv   -> download

require(__DIR__ . '/../../config.php');

use local_examwizard\local\results;

$cmid   = required_param('cmid', PARAM_INT);
$export = optional_param('export', '', PARAM_ALPHA);

[$course, $cm] = get_course_and_cm_from_cmid($cmid, 'quiz');
require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/quiz:viewreports', $context);

$quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
$examname = format_string($quiz->name);

if ($export === 'csv') {
    results::send_csv($quiz, $context, $examname);   // exits
}

$baseurl = new moodle_url('/local/examwizard/results.php', ['cmid' => $cmid]);
$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('rs_title', 'local_examwizard'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'), new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('rs_title', 'local_examwizard'));

$sum  = results::summary($quiz, $context);
$rows = results::rows($quiz, $context);
$s = fn($id, $a = null) => get_string($id, 'local_examwizard', $a);

echo $OUTPUT->header();
echo $OUTPUT->heading($examname);
echo html_writer::div($s('rs_intro'), 'text-muted mb-3');

echo html_writer::div(
    html_writer::link(new moodle_url($baseurl, ['export' => 'csv']),
        $OUTPUT->pix_icon('i/down', '') . ' ' . $s('rs_download'),
        ['class' => 'btn btn-primary']) . ' ' .
    html_writer::link(new moodle_url('/mod/quiz/report.php', ['id' => $cmid, 'mode' => 'overview']),
        $s('rs_fullreport'), ['class' => 'btn btn-outline-secondary']),
    'mb-3');

// ---- summary chips ----
echo html_writer::start_div('ew-glance');
$chips = [
    [$sum['attempted'] . ' / ' . $sum['enrolled'], $s('rs_attempted')],
    [format_float($sum['avgpct'], 1) . '%', $s('rs_average')],
    [$sum['inprogress'], $s('rs_inprogress'), $sum['inprogress'] > 0 ? 'live' : ''],
    [$sum['notstarted'], $s('rs_notstarted')],
];
if (!is_null($sum['passrate'])) {
    $chips[] = [format_float($sum['passrate'], 0) . '%', $s('rs_passrate')];
}
foreach ($chips as $c) {
    echo html_writer::div(
        html_writer::tag('span', $c[0], ['class' => 'ew-glance-n']) .
        html_writer::tag('span', $c[1], ['class' => 'ew-glance-l']),
        'ew-glance-item' . (!empty($c[2]) ? ' ew-glance-' . $c[2] : ''));
}
echo html_writer::end_div();

// ---- per-student table ----
$haspass = ($quiz->gradepass > 0);
echo html_writer::start_tag('table', ['class' => 'ew-exams ew-results']);
$head = html_writer::tag('th', $s('st_name')) . html_writer::tag('th', $s('st_username')) .
    html_writer::tag('th', $s('rs_score')) . html_writer::tag('th', $s('rs_percent'));
if ($haspass) {
    $head .= html_writer::tag('th', $s('rs_result'));
}
$head .= html_writer::tag('th', $s('rs_duration')) . html_writer::tag('th', $s('rs_state'));
echo html_writer::tag('thead', html_writer::tag('tr', $head));
echo html_writer::start_tag('tbody');
foreach ($rows as $r) {
    $statelabels = [
        'finished' => $s('rs_st_finished'), 'inprogress' => $s('rs_st_inprogress'),
        'abandoned' => $s('rs_st_abandoned'), 'notstarted' => $s('rs_st_notstarted'),
    ];
    $cells = html_writer::tag('td', s($r['name'])) .
        html_writer::tag('td', html_writer::tag('code', s($r['username']))) .
        html_writer::tag('td', is_null($r['grade']) ? '–'
            : format_float($r['grade'], 2) . ' / ' . format_float($sum['maxgrade'], 2)) .
        html_writer::tag('td', is_null($r['percent']) ? '–' : format_float($r['percent'], 1) . '%');
    if ($haspass) {
        $cells .= html_writer::tag('td', is_null($r['passed']) ? '–'
            : html_writer::tag('span', $r['passed'] ? $s('w_yes') : $s('w_no'),
                ['class' => 'ew-badge ew-badge-' . ($r['passed'] ? 'live' : 'closed')]));
    }
    $cells .= html_writer::tag('td', $r['duration'] ? round($r['duration'] / 60) . 'm' : '–') .
        html_writer::tag('td', $statelabels[$r['laststate']] ?? $r['laststate']);
    echo html_writer::tag('tr', $cells,
        ['class' => $r['laststate'] === 'notstarted' ? 'ew-row-bad' : '']);
}
echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');

echo $OUTPUT->footer();
