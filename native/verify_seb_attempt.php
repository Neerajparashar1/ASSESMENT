<?php
// Simulate a student hitting the SEB-locked quiz and print what the
// access manager decides. Proves SEB enforcement without SEB installed.
//   E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\verify_seb_attempt.php --cmid=3 --user=s2026001
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

list($o) = cli_get_params(['cmid' => 3, 'user' => 's2026001'], []);

$cm      = get_coursemodule_from_id('quiz', (int)$o['cmid'], 0, false, MUST_EXIST);
$student = $DB->get_record('user', ['username' => $o['user']], '*', MUST_EXIST);
\core\session\manager::set_user($student);          // "log in" as the student
$USER = $student;

$quizobj = \mod_quiz\quiz_settings::create($cm->instance, $student->id);
$canignore = has_capability('mod/quiz:ignoretimelimits', $quizobj->get_context(), $student->id, false);
$accessmanager = new \mod_quiz\access_manager($quizobj, time(), $canignore);

cli_writeln("=== Student: {$student->username}   Quiz: {$quizobj->get_quiz_name()}   cmid: {$cm->id} ===");

cli_writeln("\n-- Rules shown to the student on the quiz front page --");
foreach ($accessmanager->describe_rules() as $desc) {
    cli_writeln('  * ' . trim(preg_replace('/\s+/', ' ', strip_tags($desc))));
}

cli_writeln("\n-- Preflight: what happens when the student clicks 'Attempt quiz' in a normal browser --");
$reasons = $accessmanager->prevent_access();
if ($reasons) {
    cli_writeln('  >>> ATTEMPT BLOCKED <<<');
    foreach ((array)$reasons as $r) {
        cli_writeln('    - ' . trim(preg_replace('/\s+/', ' ', strip_tags($r))));
    }
} else {
    cli_writeln('  (not blocked)');
}

$seb = $DB->get_record('quizaccess_seb_quizsettings', ['quizid' => $quizobj->get_quizid()]);
cli_writeln("\n-- quizaccess_seb_quizsettings row --");
cli_writeln('  requiresafeexambrowser = ' . ($seb->requiresafeexambrowser ?? 'MISSING')
    . '   (1 = enforced, Moodle-generated config)');
cli_writeln('  showsebtaskbar=' . $seb->showsebtaskbar
    . '  allowreloadinexam=' . $seb->allowreloadinexam
    . '  linkquitseb=' . ($seb->linkquitseb ?: '(none)'));

$proc = $DB->get_record('quizaccess_proctoring', ['quizid' => $quizobj->get_quizid()]);
cli_writeln('  quizaccess_proctoring.proctoringrequired = ' . ($proc->proctoringrequired ?? 'MISSING'));

cli_writeln("\nDONE.");
