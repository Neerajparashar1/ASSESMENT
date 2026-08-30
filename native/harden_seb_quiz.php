<?php
// =====================================================================
//  Harden a SEB quiz so that LEAVING Safe Exam Browser auto-submits the
//  attempt (it can never be resumed after exit), and text selection /
//  clipboard are blocked on the attempt page.
//
//    E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\harden_seb_quiz.php --cmid=4
//
//  What it does (idempotent, re-runnable):
//   1. writes the "leave = submit" beacon + no-select JS/CSS into
//      $CFG->additionalhtmlfooter in config.php  (config.php wins over the
//      DB setting on this stack, so it MUST live there). BOM-less write.
//   2. clears the now-dead DB copy of additionalhtmlfooter.
//   3. points the quiz's SEB quitURL (linkquitseb) at
//      local/sebkiosk/finish.php so a clean "Quit" also submits.
//   4. ensures overduehandling=autosubmit + a non-zero time limit as the
//      backstop for a hard SEB kill.
//   5. purges caches.
//
//  Requires the local_sebkiosk plugin to be installed
//  (native\stack\moodle\local\sebkiosk + admin/cli/upgrade.php).
// =====================================================================
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');

list($o) = cli_get_params(['cmid' => 4], []);
$cmid = (int)$o['cmid'];
function out($m) { cli_writeln('[harden] ' . $m); }

$cm   = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
$quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);
$www  = rtrim($CFG->wwwroot, '/');
$endpoint = $www . '/local/sebkiosk/finish.php';

// ---------------------------------------------------------------------
// 1. footer -> config.php  ($CFG->additionalhtmlfooter)
//    Inline CSS (no-select) + a <script src> to the plugin's exam-ui.js
//    (which holds the clipboard block, the SEB-only auto-submit beacon,
//    and the candidate-UX polish). ONE physical line, NO single quotes.
// ---------------------------------------------------------------------
$jsver  = 16;   // bump to cache-bust local/sebkiosk/exam-ui.js
$jsurl  = $www . '/local/sebkiosk/exam-ui.js?v=' . $jsver;
$footer = '<!-- sebkiosk -->'
  . '<style>'
  . '#page-mod-quiz-attempt .qtext,#page-mod-quiz-attempt .formulation,#page-mod-quiz-attempt .info,'
  . '#page-mod-quiz-attempt #quiznavigation,#page-mod-quiz-attempt .qn_buttons'
  . '{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}'
  . '#page-mod-quiz-attempt input,#page-mod-quiz-attempt textarea,#page-mod-quiz-attempt select,'
  . '#page-mod-quiz-attempt [contenteditable]{-webkit-user-select:text;-moz-user-select:text;user-select:text}'
  . '</style>'
  . '<script defer src="' . $jsurl . '"></script>';

$cfgfile = __DIR__ . '/stack/moodle/config.php';
$src = file_get_contents($cfgfile);
$line = '$CFG->additionalhtmlfooter = ' . var_export($footer, true) . ';';

$new = preg_replace('/^\$CFG->additionalhtmlfooter\s*=\s*.*;[ \t]*$/m', $line, $src, 1, $count);
if (!$count) {
    // no existing assignment - drop it in just before the managed-config end marker
    $new = preg_replace('/^(\/\/ ==== \/EAP MANAGED CONFIG)/m', $line . "\n$1", $src, 1, $count);
}
if (!$count) { cli_error("could not place additionalhtmlfooter in $cfgfile"); }

if ($new !== $src) {
    // BOM-less UTF-8 write (see build-progress gotcha #1)
    file_put_contents($cfgfile, $new);
    out("config.php \$CFG->additionalhtmlfooter rewritten (" . strlen($footer) . " char payload)");
} else {
    out('config.php already up to date');
}

// kill the dead DB copy so there is one source of truth
if ((string)$DB->get_field('config', 'value', ['name' => 'additionalhtmlfooter']) !== '') {
    set_config('additionalhtmlfooter', '');
    out('cleared stale DB additionalhtmlfooter row');
}

// ---------------------------------------------------------------------
// 2. SEB quitURL -> auto-submit endpoint, for this quiz
// ---------------------------------------------------------------------
$seb = $DB->get_record('quizaccess_seb_quizsettings', ['quizid' => $quiz->id]);
if ($seb) {
    $seb->linkquitseb      = $endpoint . '?cmid=' . $cmid;
    $seb->allowuserquitseb = 1;     // keep the Quit button - quitting now submits
    $seb->quitpassword     = '';    // student may quit unaided; exit == submit
    $seb->timemodified     = time();
    $DB->update_record('quizaccess_seb_quizsettings', $seb);
    out("quizaccess_seb_quizsettings.linkquitseb = {$seb->linkquitseb}");
} else {
    out('WARN no quizaccess_seb_quizsettings row for this quiz (SEB not enforced?)');
}

// ---------------------------------------------------------------------
// 3. Backstop for a hard SEB kill: timer auto-submits
// ---------------------------------------------------------------------
$changed = [];
if ($quiz->overduehandling !== 'autosubmit') { $quiz->overduehandling = 'autosubmit'; $changed[] = 'overduehandling=autosubmit'; }
if ((int)$quiz->timelimit === 0)            { $quiz->timelimit = 30 * 60;            $changed[] = 'timelimit=1800'; }
if ($changed) { $DB->update_record('quiz', $quiz); out('quiz: ' . implode(', ', $changed)); }

purge_all_caches();
out('caches purged');

cli_writeln('');
cli_writeln('DONE. Leaving SEB (Quit button, closing the window, or losing focus)');
cli_writeln('now finishes the in-progress attempt; attempts=1 means it cannot restart.');
cli_writeln("Re-download the .seb:  {$www}/mod/quiz/accessrule/seb/config.php?cmid={$cmid}");
