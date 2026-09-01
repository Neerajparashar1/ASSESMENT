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
    $node = $navigation->add(
        get_string('pluginname', 'local_examwizard'),
        new moodle_url('/local/examwizard/index.php'),
        navigation_node::TYPE_CONTAINER,
        null,
        'local_examwizard',
        new pix_icon('i/import', '')
    );
    $node->add(
        get_string('createexam', 'local_examwizard'),
        new moodle_url('/local/examwizard/wizard.php', ['courseid' => $course->id]),
        navigation_node::TYPE_SETTING,
        null,
        'local_examwizard_wizard',
        new pix_icon('i/course', '')
    );
    $node->add(
        get_string('uploadquestions', 'local_examwizard'),
        new moodle_url('/local/examwizard/questions.php', ['courseid' => $course->id]),
        navigation_node::TYPE_SETTING,
        null,
        'local_examwizard_upload',
        new pix_icon('i/import', '')
    );
}

/**
 * Build a random login password that satisfies Moodle's current password policy
 * (default: >= 8 chars, at least one upper, one lower, one digit, one symbol).
 *
 * Ambiguous glyphs (0/O, 1/l/I) are left out so staff can read it aloud without
 * confusion. Used by credentials.php when the exam cell resets a candidate.
 *
 * @return string
 */
function local_examwizard_generate_password(): string {
    $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $lower = 'abcdefghijkmnpqrstuvwxyz';
    $digit = '23456789';
    $sym   = '@#$%*!?';

    $pick = fn($set) => $set[random_int(0, strlen($set) - 1)];

    $pw = '';
    for ($try = 0; $try < 30; $try++) {
        $chars = [
            $pick($upper), $pick($upper),
            $pick($lower), $pick($lower), $pick($lower),
            $pick($digit), $pick($digit),
            $pick($sym),
        ];
        shuffle($chars);
        $pw = implode('', $chars);
        $err = '';
        if (check_password_policy($pw, $err)) {
            return $pw;
        }
    }
    // Policy is stricter than expected - fall back to a longer shape.
    return $pick($upper) . $pick($upper) . $pick($lower) . $pick($lower)
        . $pick($lower) . $pick($lower) . $pick($digit) . $pick($digit)
        . $pick($digit) . $pick($sym) . $pick($sym);
}
