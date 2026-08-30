<?php
// Navigation hooks for local_examwizard.

defined('MOODLE_INTERNAL') || die();

/**
 * Add an "Exam Wizard" link to a course's navigation.
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function local_examwizard_extend_navigation_course(navigation_node $navigation, stdClass $course, context_course $context) {
    if (!has_capability('local/examwizard:use', $context) || !has_capability('moodle/question:add', $context)) {
        return;
    }
    $navigation->add(
        get_string('pluginname', 'local_examwizard'),
        new moodle_url('/local/examwizard/questions.php', ['courseid' => $course->id]),
        navigation_node::TYPE_SETTING,
        null,
        'local_examwizard',
        new pix_icon('i/import', '')
    );
}
