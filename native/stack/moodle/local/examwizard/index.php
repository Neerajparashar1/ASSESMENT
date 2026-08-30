<?php
// Exam Wizard landing - pick a course, then jump to a tool.

require(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/examwizard/index.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_examwizard'));
$PAGE->set_heading(get_string('pluginname', 'local_examwizard'));

// Courses where the user may use the wizard.
$courses = [];
foreach (enrol_get_my_courses(['id', 'fullname', 'shortname'], 'fullname ASC') as $c) {
    $ctx = context_course::instance($c->id);
    if (has_capability('local/examwizard:use', $ctx) && has_capability('moodle/question:add', $ctx)) {
        $courses[$c->id] = $c;
    }
}
if (is_siteadmin() && !$courses) {
    foreach (get_courses('all', 'c.fullname ASC', 'c.id, c.fullname, c.shortname') as $c) {
        if ($c->id == SITEID) {
            continue;
        }
        $courses[$c->id] = $c;
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_examwizard'));
echo html_writer::div(get_string('landingintro', 'local_examwizard'), 'lead text-muted mb-4');

if (!$courses) {
    echo $OUTPUT->notification(get_string('nocourses', 'local_examwizard'),
        \core\output\notification::NOTIFY_INFO);
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::start_div('ew-coursegrid');
foreach ($courses as $c) {
    $wizardurl = new moodle_url('/local/examwizard/wizard.php', ['courseid' => $c->id]);
    $uploadurl = new moodle_url('/local/examwizard/questions.php', ['courseid' => $c->id]);
    echo html_writer::start_div('ew-course-card');
    echo html_writer::tag('h3', format_string($c->fullname));
    echo html_writer::tag('p', format_string($c->shortname));
    echo html_writer::div(
        html_writer::link($wizardurl, get_string('createexam', 'local_examwizard'),
            ['class' => 'btn btn-primary btn-sm mr-2']) .
        html_writer::link($uploadurl, get_string('uploadquestions', 'local_examwizard'),
            ['class' => 'btn btn-outline-secondary btn-sm']),
        'ew-course-actions');
    echo html_writer::end_div();
}
echo html_writer::end_div();

echo $OUTPUT->footer();
