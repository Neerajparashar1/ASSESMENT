<?php
// =====================================================================
//  Create a test course + quiz with Safe Exam Browser AND webcam
//  proctoring enforced, populated from scripts/sample_questions.gift.
//
//  Run (native stack):
//    E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\create_test_quiz.php
//
//  Re-runnable: reuses the course by shortname, always adds a fresh quiz
//  (pass --reset to delete previously generated quizzes first).
// =====================================================================
define('CLI_SCRIPT', true);

$moodle = __DIR__ . '/stack/moodle';
require($moodle . '/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/testing/generator/lib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/gift/format.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');   // course_delete_module() for --reset

list($opts) = cli_get_params(['reset' => false, 'noseb' => false, 'noproctor' => false, 'help' => false], ['h' => 'help']);
if ($opts['help']) {
    cli_writeln("Creates 'SEB + Proctoring Test' course with a SEB + proctoring quiz.\n"
        . "  --reset      delete earlier generated quizzes in that course first\n"
        . "  --noseb      do NOT require Safe Exam Browser (attemptable in a normal browser)\n"
        . "  --noproctor  do NOT require webcam proctoring");
    exit(0);
}

function out($m) { cli_writeln('[test-quiz] ' . $m); }

$USER = get_admin();               // run as the site admin
$gen  = new testing_data_generator();

// ---------------------------------------------------------------
// 1. Course
// ---------------------------------------------------------------
$shortname = 'SEBPROCTEST';
$course = $DB->get_record('course', ['shortname' => $shortname]);
if (!$course) {
    $course = $gen->create_course([
        'fullname'    => 'SEB + Proctoring Test',
        'shortname'   => $shortname,
        'category'    => 1,
        'summary'     => 'Auto-generated: quiz locked to Safe Exam Browser with webcam proctoring.',
        'summaryformat' => FORMAT_HTML,
        'numsections' => 1,
        'format'      => 'topics',
    ]);
    out("created course id {$course->id} ({$shortname})");
} else {
    out("reusing course id {$course->id} ({$shortname})");
}
$coursecontext = context_course::instance($course->id);

// ---------------------------------------------------------------
// 2. Import GIFT questions into the course question bank
// ---------------------------------------------------------------
$qcat = question_get_default_category($coursecontext->id);
if (!$qcat) {
    $qcat = question_make_default_categories([$coursecontext]);
}
out("question category id {$qcat->id}");

$catquestions = function () use ($DB, $qcat) {
    return $DB->get_fieldset_sql(
        "SELECT q.id
           FROM {question} q
           JOIN {question_versions} qv ON qv.questionid = q.id
           JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
          WHERE qbe.questioncategoryid = :catid", ['catid' => $qcat->id]);
};
$before = $catquestions();

$giftpath = 'E:\ASSESMENT\scripts\sample_questions.gift';
if (is_readable($giftpath)) {
    $qformat = new qformat_gift();
    $qformat->setContexts([$coursecontext]);
    $qformat->setCourse($course);
    $qformat->setFilename($giftpath);
    $qformat->setCategory($qcat);
    $qformat->setCatfromfile(false);
    $qformat->setContextfromfile(false);
    $qformat->setStoponerror(false);
    $qformat->setCattofile(false);
    try {
        if ($qformat->importpreprocess() && $qformat->importprocess()) {
            $qformat->importpostprocess();
            out('GIFT import ok');
        } else {
            out('WARN GIFT import returned false (continuing)');
        }
    } catch (Throwable $e) {
        out('WARN GIFT import threw: ' . $e->getMessage());
    }
} else {
    out("WARN cannot read $giftpath - quiz will have no questions");
}
$after   = $catquestions();
$newqids = array_values(array_diff($after, $before));
if (!$newqids) { $newqids = $after; }   // reuse existing on a repeat run
out('questions available for quiz: ' . count($newqids));

// ---------------------------------------------------------------
// 3. Quiz module: SEB required + proctoring required
// ---------------------------------------------------------------
if ($opts['reset']) {
    $old = $DB->get_records_select('quiz', "course = ? AND name LIKE 'Proctored SEB Exam%'", [$course->id]);
    foreach ($old as $o) {
        $cm = get_coursemodule_from_instance('quiz', $o->id, $course->id);
        if ($cm) { course_delete_module($cm->id); out("deleted old quiz cmid {$cm->id}"); }
    }
}

$name = 'Proctored SEB Exam (' . date('Y-m-d H:i') . ')';
$quiz = $gen->create_module('quiz', [
    'course'             => $course->id,
    'name'               => $name,
    'intro'              => '<p>Test exam. Requires <strong>Safe Exam Browser</strong> and <strong>webcam proctoring</strong>. '
                          . 'Snapshots are captured every 30&nbsp;seconds.</p>',
    'introformat'        => FORMAT_HTML,
    'preferredbehaviour' => 'deferredfeedback',
    'attempts'           => 1,
    'grademethod'        => QUIZ_GRADEHIGHEST,
    'questionsperpage'   => 1,
    'shuffleanswers'     => 1,
    'navmethod'          => 'free',
    'timelimit'          => 30 * MINSECS,
    'grade'              => 100,
    'visible'            => 1,
    // --- quizaccess_seb : 1 = "Yes - Configure manually" (Moodle builds the .seb), 0 = off ---
    'seb_requiresafeexambrowser' => $opts['noseb'] ? 0 : 1,
    'seb_showsebtaskbar'         => 1,
    'seb_allowspellchecking'     => 0,
    'seb_allowreloadinexam'      => 0,
    'seb_activateurlfiltering'   => 0,
    'seb_linkquitseb'            => rtrim($CFG->wwwroot, '/') . '/mod/quiz/accessrule/seb/config.php',
    // --- quizaccess_proctoring : 1 = required, 0 = off ---
    'proctoringrequired' => $opts['noproctor'] ? 0 : 1,
]);
out("created quiz  id {$quiz->id}  cmid {$quiz->cmid}");

// ---------------------------------------------------------------
// 4. Add questions + recompute grades
// ---------------------------------------------------------------
$quizrec = $DB->get_record('quiz', ['id' => $quiz->id], '*', MUST_EXIST);
$added = 0;
foreach ($newqids as $i => $qid) {
    try {
        quiz_add_quiz_question($qid, $quizrec, 0);
        $added++;
    } catch (Throwable $e) {
        out("WARN could not add question $qid: " . $e->getMessage());
    }
}
if ($added) {
    quiz_update_sumgrades($quizrec);
    out("added $added question(s); sumgrades recomputed");
}

// ---------------------------------------------------------------
// 5. Verify the access rules actually persisted
// ---------------------------------------------------------------
$seb  = $DB->get_record('quizaccess_seb_quizsettings', ['quizid' => $quiz->id]);
$proc = $DB->get_record('quizaccess_proctoring', ['quizid' => $quiz->id]);
out('quizaccess_seb_quizsettings row : ' . ($seb ? "yes (requiresafeexambrowser={$seb->requiresafeexambrowser})" : 'MISSING'));
out('quizaccess_proctoring row       : ' . ($proc ? "yes (proctoringrequired={$proc->proctoringrequired})" : 'MISSING'));

$base = rtrim($CFG->wwwroot, '/');
cli_writeln('');
cli_writeln('=====================================================================');
cli_writeln(' TEST QUIZ READY');
cli_writeln('=====================================================================');
cli_writeln("  Course : {$base}/course/view.php?id={$course->id}");
cli_writeln("  Quiz   : {$base}/mod/quiz/view.php?id={$quiz->cmid}");
cli_writeln("  Edit   : {$base}/mod/quiz/edit.php?cmid={$quiz->cmid}");
cli_writeln("  SEB cfg: {$base}/mod/quiz/accessrule/seb/config.php?cmid={$quiz->cmid}  (download .seb)");
cli_writeln("  Settings: {$base}/course/modedit.php?update={$quiz->cmid}");
cli_writeln('');
cli_writeln('  Enrol students:  .\\native\\Import-Students.ps1 -Csv .\\scripts\\students_sample.csv');
cli_writeln('  then enrol them into the course above as "Student".');
exit(0);
