<?php
// End-to-end test of local/sebkiosk auto-submit.
//   1. start a fresh in-progress attempt for a test student on cmid 4
//   2. print the attempt id + state
//   3. (script prints a curl line you can run to hit finish.php as that user)
//   4. with --verify <attemptid> : just re-print the state
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

list($o) = cli_get_params(['cmid' => 4, 'user' => 's2026002', 'verify' => 0, 'start' => false], []);

$cm   = get_coursemodule_from_id('quiz', (int)$o['cmid'], 0, false, MUST_EXIST);
$quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
$user = $DB->get_record('user', ['username' => $o['user']], '*', MUST_EXIST);

function show($DB, $quiz, $user) {
    foreach ($DB->get_records('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $user->id], 'id') as $a) {
        cli_writeln(sprintf("  attempt id=%d state=%-10s start=%d finish=%d sumgrades=%s",
            $a->id, $a->state, $a->timestart, $a->timefinish, var_export($a->sumgrades, true)));
    }
}

if ($o['verify']) {
    cli_writeln("attempts for {$user->username} on quiz {$quiz->id}:");
    show($DB, $quiz, $user);
    exit(0);
}

if (!$o['start']) {
    cli_writeln("Pass --start to create a fresh in-progress attempt (deletes prior ones for this user).");
    cli_writeln("Current state:");
    show($DB, $quiz, $user);
    exit(0);
}

// wipe prior attempts for a clean test
foreach ($DB->get_records('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $user->id]) as $a) {
    quiz_delete_attempt($a, $quiz);
    cli_writeln("deleted old attempt {$a->id}");
}

$quizobj = \mod_quiz\quiz_settings::create($quiz->id, $user->id);
$quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
$quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

$timenow = time();
$attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
$attempt = quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
$attempt = quiz_attempt_save_started($quizobj, $quba, $attempt);

cli_writeln("\nStarted in-progress attempt id={$attempt->id} for {$user->username} (state should be 'inprogress').");
show($DB, $quiz, $user);

cli_writeln("\nNow simulate the student leaving SEB. Run:");
cli_writeln("  bash native/_hit_finish.sh {$o['user']} {$o['cmid']} {$attempt->id}");
cli_writeln("then: php native/_test_autosubmit.php --verify=1 --user={$o['user']}");
