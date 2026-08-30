<?php
// =====================================================================
//  Auto-submit endpoint.
//
//  Called two ways:
//   1. navigator.sendBeacon() from the quiz attempt page when it is hidden
//      / unloaded because the candidate is leaving SEB   (POST, beacon=1,
//      sesskey required).
//   2. SEB's quitURL when the candidate clicks "Quit" in SEB
//      (GET, no sesskey - SEB is already tearing the session down).
//
//  Either way: finish every in-progress attempt that belongs to the
//  current user (optionally narrowed to one quiz / one attempt), so the
//  attempt can never be resumed after leaving the secure browser.
// =====================================================================
define('NO_DEBUG_DISPLAY', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

require_login(null, false);

$cmid     = optional_param('cmid', 0, PARAM_INT);
$attempt  = optional_param('attempt', 0, PARAM_INT);
$viabeacon = optional_param('beacon', 0, PARAM_BOOL);
$sesskey  = optional_param('sesskey', '', PARAM_RAW);

// CSRF: the beacon path always carries a sesskey. The SEB quitURL path is a
// plain GET with none - accepted because it only ever finishes the caller's
// own attempts and SEB is quitting regardless.
if ($viabeacon && (empty($sesskey) || !confirm_sesskey($sesskey))) {
    http_response_code(400);
    exit('bad sesskey');
}

@ignore_user_abort(true);
core\session\manager::write_close();   // don't hold the session lock while grading

$params = ['uid' => $USER->id, 'st' => \mod_quiz\quiz_attempt::IN_PROGRESS];
$where  = 'qa.userid = :uid AND qa.state = :st';
if ($cmid) {
    $cm = get_coursemodule_from_id('quiz', $cmid, 0, false, IGNORE_MISSING);
    if ($cm) { $where .= ' AND qa.quiz = :quizid'; $params['quizid'] = $cm->instance; }
}
if ($attempt) { $where .= ' AND qa.id = :aid'; $params['aid'] = $attempt; }

$recs = $DB->get_records_sql("SELECT qa.id FROM {quiz_attempts} qa WHERE $where", $params);

$done = 0;
foreach ($recs as $r) {
    try {
        $attemptobj = \mod_quiz\quiz_attempt::create($r->id);
        if ($attemptobj->get_userid() == $USER->id && !$attemptobj->is_finished()) {
            // studentisonline = false -> treated like an automatic/offline finish
            $attemptobj->process_finish(time(), false);
            $done++;
        }
    } catch (Throwable $e) {
        // best effort - keep going
    }
}

http_response_code(204);
if (!$viabeacon) {
    // human (SEB quitURL) landing - show a tiny confirmation page
    @header('Content-Type: text/html; charset=utf-8');
    http_response_code(200);
    echo '<!doctype html><meta charset="utf-8"><title>Exam ended</title>'
       . '<body style="font:16px system-ui;margin:3rem;text-align:center">'
       . '<h2>Your exam has been submitted.</h2>'
       . '<p>' . $done . ' attempt(s) finalised. You may close this window.</p>';
}
