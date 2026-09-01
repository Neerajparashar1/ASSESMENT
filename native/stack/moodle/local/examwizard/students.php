<?php
// Exam Wizard - bulk-add students and enrol them into a course.
//   ?courseid=N            -> upload form
//   ?courseid=N&template=1 -> download the CSV template
//   (submit)               -> validated preview
//   (confirm)              -> create + enrol + result

require(__DIR__ . '/../../config.php');

use local_examwizard\local\student_csv;

$courseid = required_param('courseid', PARAM_INT);
$template = optional_param('template', 0, PARAM_BOOL);

$course = get_course($courseid);
require_login($course);
$coursecontext = context_course::instance($courseid);
require_capability('local/examwizard:use', $coursecontext);
require_capability('moodle/course:enrolreview', $coursecontext);
require_capability('enrol/manual:enrol', $coursecontext);

$baseurl = new moodle_url('/local/examwizard/students.php', ['courseid' => $courseid]);
$PAGE->set_url($baseurl);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('st_title', 'local_examwizard'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'), new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('st_title', 'local_examwizard'));

if ($template) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="examwizard-students-template.csv"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    foreach (student_csv::template_rows() as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

require_once($CFG->libdir . '/formslib.php');

/** Simple upload form. */
class local_examwizard_students_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;
        $mform->addElement('static', 'tpl', get_string('downloadtemplate', 'local_examwizard'),
            html_writer::link(new moodle_url('/local/examwizard/students.php',
                ['courseid' => $this->_customdata['courseid'], 'template' => 1]),
                get_string('templatecsv', 'local_examwizard'),
                ['class' => 'btn btn-outline-secondary btn-sm']));
        $mform->addElement('filepicker', 'rosterfile', get_string('st_file', 'local_examwizard'), null,
            ['maxbytes' => 2 * 1024 * 1024, 'accepted_types' => ['.csv', '.xlsx']]);
        $mform->addRule('rosterfile', null, 'required', null, 'client');
        $mform->addElement('text', 'defaultpassword', get_string('st_defaultpw', 'local_examwizard'), ['size' => 20]);
        $mform->setType('defaultpassword', PARAM_RAW_TRIMMED);
        $mform->setDefault('defaultpassword', 'Exam@2026');
        $mform->addHelpButton('defaultpassword', 'st_defaultpw', 'local_examwizard');
        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        $mform->setType('courseid', PARAM_INT);
        $this->add_action_buttons(false, get_string('review', 'local_examwizard'));
    }
}

$mform = new local_examwizard_students_form($baseurl, ['courseid' => $courseid]);

// ---- confirm ----
if (optional_param('doimport', 0, PARAM_BOOL) && confirm_sesskey()) {
    $stash = $SESSION->local_examwizard_roster ?? null;
    if (!$stash || (int) $stash['courseid'] !== $courseid) {
        redirect($baseurl, get_string('err_emptyfile', 'local_examwizard'), null,
            \core\output\notification::NOTIFY_ERROR);
    }
    unset($SESSION->local_examwizard_roster);
    $res = student_csv::apply($stash['rows'], $courseid, $stash['defaultpassword']);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('st_done', 'local_examwizard'));
    echo $OUTPUT->notification(get_string('st_done_msg', 'local_examwizard', (object) $res),
        \core\output\notification::NOTIFY_SUCCESS);
    echo html_writer::div(
        html_writer::link(new moodle_url('/user/index.php', ['id' => $courseid]),
            get_string('st_viewparticipants', 'local_examwizard'), ['class' => 'btn btn-primary mr-2']) .
        html_writer::link($baseurl, get_string('st_addmore', 'local_examwizard'),
            ['class' => 'btn btn-outline-secondary']),
        'mt-3');
    echo $OUTPUT->footer();
    exit;
}

// ---- parse + preview ----
if ($data = $mform->get_data()) {
    $filename = $mform->get_new_filename('rosterfile');
    $content = $mform->get_file_content('rosterfile');
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext === 'xlsx') {
        require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');
        $tmp = make_request_directory() . '/roster.xlsx';
        file_put_contents($tmp, $content);
        try {
            $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmp)->load($tmp)->getActiveSheet();
            $parsed = student_csv::parse_rows($sheet->toArray(null, true, false, false));
        } catch (\Throwable $e) {
            $parsed = ['fatal' => get_string('err_parsefail', 'local_examwizard', $e->getMessage())];
        }
    } else {
        $parsed = student_csv::parse_csv($content);
    }

    if (!empty($parsed['fatal'])) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('st_title', 'local_examwizard'));
        echo $OUTPUT->notification($parsed['fatal'], \core\output\notification::NOTIFY_ERROR);
        $mform->display();
        echo $OUTPUT->footer();
        exit;
    }

    $SESSION->local_examwizard_roster = [
        'courseid' => $courseid,
        'rows' => $parsed['rows'],
        'defaultpassword' => $data->defaultpassword ?: 'Exam@2026',
    ];

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('review', 'local_examwizard'));
    echo html_writer::start_div('ew-review-summary');
    echo html_writer::tag('span', get_string('st_ready', 'local_examwizard', $parsed['valid']),
        ['class' => 'ew-pill ew-pill-ok']);
    if ($parsed['errors']) {
        echo html_writer::tag('span', get_string('rowserrors', 'local_examwizard', $parsed['errors']),
            ['class' => 'ew-pill ew-pill-err']);
    }
    echo html_writer::end_div();

    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', '#') . html_writer::tag('th', get_string('st_name', 'local_examwizard')) .
        html_writer::tag('th', get_string('st_username', 'local_examwizard')) .
        html_writer::tag('th', get_string('email')) .
        html_writer::tag('th', get_string('b_name', 'local_examwizard')) . html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($parsed['rows'] as $r) {
        $bad = !empty($r['errors']);
        echo html_writer::tag('tr',
            html_writer::tag('td', $r['n']) .
            html_writer::tag('td', s(trim($r['firstname'] . ' ' . ($r['lastname'] === '.' ? '' : $r['lastname'])))) .
            html_writer::tag('td', html_writer::tag('code', s($r['username']))) .
            html_writer::tag('td', s($r['email'])) .
            html_writer::tag('td', $r['batch'] !== '' ? s($r['batch']) : '') .
            html_writer::tag('td', $bad ? html_writer::tag('span', implode('; ', array_map('s', $r['errors'])),
                ['class' => 'text-danger small']) : ''),
            ['class' => $bad ? 'ew-row-bad' : '']);
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');

    $confirm = new moodle_url($baseurl, ['doimport' => 1, 'sesskey' => sesskey()]);
    echo html_writer::div(
        ($parsed['valid'] > 0
            ? html_writer::link($confirm, get_string('st_confirm', 'local_examwizard', $parsed['valid']),
                ['class' => 'btn btn-primary btn-lg'])
            : html_writer::tag('span', get_string('st_confirm', 'local_examwizard', 0),
                ['class' => 'btn btn-primary btn-lg disabled'])) . ' ' .
        html_writer::link($baseurl, get_string('backtoupload', 'local_examwizard'),
            ['class' => 'btn btn-outline-secondary btn-lg']),
        'ew-review-actions mt-3');
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('st_title', 'local_examwizard'));
echo html_writer::div(get_string('st_intro', 'local_examwizard'), 'lead text-muted mb-3');
$mform->display();
echo $OUTPUT->footer();
