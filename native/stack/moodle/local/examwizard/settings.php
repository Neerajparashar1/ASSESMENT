<?php
// Adds "Exam Wizard" under Site administration > Courses.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('courses', new admin_externalpage(
        'local_examwizard',
        get_string('pluginname', 'local_examwizard'),
        new moodle_url('/local/examwizard/index.php'),
        'local/examwizard:use'
    ));
}
