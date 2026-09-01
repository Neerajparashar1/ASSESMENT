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

/**
 * A short, unique-ish ID code for a batch (cohort idnumber) derived from its name.
 * "CS - A Batch 2024" -> "CSABATCH2024" (trimmed to 90, made unique with a suffix).
 *
 * @param string $name
 * @return string
 */
function local_examwizard_batch_idnumber(string $name): string {
    global $DB;
    $base = strtoupper(preg_replace('~[^A-Za-z0-9]+~', '', $name));
    $base = $base === '' ? 'BATCH' : \core_text::substr($base, 0, 90);
    $code = $base;
    $i = 2;
    while ($DB->record_exists('cohort', ['idnumber' => $code])) {
        $code = \core_text::substr($base, 0, 88) . $i;
        $i++;
    }
    return $code;
}

/**
 * Find a batch (cohort) by ID code or name, creating it at system context if
 * it does not exist yet. Used by the roster importer's "batch" column.
 *
 * @param string $label cohort idnumber or name from the CSV
 * @return int cohort id
 */
function local_examwizard_find_or_create_batch(string $label): int {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/cohort/lib.php');

    $label = trim($label);
    $code  = strtoupper(preg_replace('~[^A-Za-z0-9]+~', '', $label));

    if ($code !== '' && ($c = $DB->get_record('cohort', ['idnumber' => $code]))) {
        return (int) $c->id;
    }
    if ($c = $DB->get_record('cohort', ['name' => $label])) {
        return (int) $c->id;
    }

    $cohort = (object) [
        'name'              => $label,
        'idnumber'          => local_examwizard_batch_idnumber($label),
        'contextid'         => \context_system::instance()->id,
        'description'       => '',
        'descriptionformat' => FORMAT_HTML,
        'visible'           => 1,
    ];
    return (int) cohort_add_cohort($cohort);
}

/**
 * Cohort-sync a batch into a course as $roleid and run the sync immediately so
 * current members are enrolled straight away. No-op if the sync already exists.
 *
 * @param int $cohortid
 * @param stdClass $course
 * @param int $roleid role to give synced members (usually the student role)
 */
function local_examwizard_ensure_batch_sync(int $cohortid, stdClass $course, int $roleid): void {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

    if ($course->id == SITEID) {
        return;   // the front page has no enrolments
    }
    if (!$DB->record_exists('enrol',
            ['enrol' => 'cohort', 'courseid' => $course->id, 'customint1' => $cohortid])) {
        $plugin = enrol_get_plugin('cohort');
        if ($plugin) {
            $plugin->add_instance($course, ['customint1' => $cohortid, 'roleid' => $roleid]);
        }
    }
    enrol_cohort_sync(new null_progress_trace(), $course->id);
}
