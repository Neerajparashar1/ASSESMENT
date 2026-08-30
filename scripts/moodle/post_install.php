<?php
// =====================================================================
//  PHASE 3 + 4  post-install tuning  (Moodle CLI script)
//  Runs as www-data from the container entrypoint after install/upgrade.
//   - Activates & themes theme_boost_union (SaaS look, custom SCSS)
//   - Configures Safe Exam Browser (quizaccess_seb)
//   - Configures webcam proctoring (quizaccess_proctoring) if present
//   - Sets exam-hardening quiz defaults (shuffle / one-per-page / free nav)
//   - Creates the "Invigilator / Proctor" RBAC role
// =====================================================================
define('CLI_SCRIPT', true);

// Config path: env override for native / non-Docker runs, Docker default otherwise.
$eap_config = getenv('EAP_MOODLE_CONFIG');
if ($eap_config === false || $eap_config === '') {
    $eap_config = '/var/www/html/moodle/config.php';
}
require($eap_config);
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/accesslib.php');

function eap_out($m) { cli_writeln('[post_install] ' . $m); }

$dirroot = $CFG->dirroot;
$changed = 0;

// ---------------------------------------------------------------
// 1. THEME  --  modern SaaS frontend
// ---------------------------------------------------------------
$scssfile = getenv('EAP_CUSTOM_SCSS');
if ($scssfile === false || $scssfile === '') {
    $scssfile = '/opt/eap/custom.scss';
}
$scss = is_readable($scssfile) ? file_get_contents($scssfile) : '';

if (file_exists("$dirroot/theme/boost_union/version.php")) {
    $theme = 'boost_union';
    $tcomp = 'theme_boost_union';
    eap_out('theme_boost_union detected -> activating.');
} else {
    $theme = 'boost';
    $tcomp = 'theme_boost';
    eap_out('boost_union NOT found -> falling back to Boost with custom SCSS.');
}

set_config('theme', $theme);
set_config('themedesignermode', 0);
set_config('brandcolor', '#4f46e5', $tcomp);
if ($scss !== '') {
    // Boost Union: "Raw SCSS" field is `scss`; Boost: `scss` too.
    set_config('scss', $scss, $tcomp);
    // Boost Union also exposes `scsspre` for variables; harmless if unused.
}
// Nudge a few Boost/Boost Union niceties (ignored if the key is unknown)
set_config('unaddableblocks', '', $tcomp);
$changed++;

// ---------------------------------------------------------------
// 2. SAFE EXAM BROWSER  (core plugin quizaccess_seb)
// ---------------------------------------------------------------
if (file_exists("$dirroot/mod/quiz/accessrule/seb/version.php")) {
    $quiturl = rtrim($CFG->wwwroot, '/') . '/mod/quiz/accessrule/seb/config.php';
    // Global defaults (per-quiz settings still override).
    set_config('autoreconfigureseb', 1, 'quizaccess_seb');
    set_config('displayblocksbeforestart', 0, 'quizaccess_seb');
    set_config('displayblockswhenfinished', 1, 'quizaccess_seb');
    set_config('showseblink', 1, 'quizaccess_seb');
    set_config('showhttplink', 1, 'quizaccess_seb');
    set_config('quiturl', $quiturl, 'quizaccess_seb');
    if (!empty($_SERVER['SEB_QUIT_PASSWORD']) || getenv('SEB_QUIT_PASSWORD')) {
        $qpw = getenv('SEB_QUIT_PASSWORD') ?: $_SERVER['SEB_QUIT_PASSWORD'];
        set_config('quitpassword', $qpw, 'quizaccess_seb');
    }
    eap_out('quizaccess_seb configured (core).');
    $changed++;
} else {
    eap_out('WARN quizaccess_seb missing from core tree.');
}

// ---------------------------------------------------------------
// 3. WEBCAM PROCTORING  (contrib quizaccess_proctoring)
// ---------------------------------------------------------------
if (file_exists("$dirroot/mod/quiz/accessrule/proctoring/version.php")) {
    // Names vary slightly across releases; set the common aliases.
    set_config('autoreconfigureproctoring', 1, 'quizaccess_proctoring');
    set_config('proctoringmethod', 'image', 'quizaccess_proctoring');
    set_config('fkcheckstartlink', 1, 'quizaccess_proctoring');
    set_config('take_snapshot_delay', 30, 'quizaccess_proctoring');   // seconds
    set_config('screenshotdelay', 30, 'quizaccess_proctoring');
    set_config('screenshotwidth', 320, 'quizaccess_proctoring');
    set_config('stored_snapshot', 1, 'quizaccess_proctoring');
    set_config('imssnapshotdelay', 30, 'quizaccess_proctoring');
    set_config('facematch', 0, 'quizaccess_proctoring');              // no paid API
    set_config('awschecknumber', 0, 'quizaccess_proctoring');
    eap_out('quizaccess_proctoring configured (periodic image snapshots, 30s).');
    $changed++;
} else {
    eap_out('NOTE quizaccess_proctoring not installed (optional).');
}

// ---------------------------------------------------------------
// 4. QUIZ / QUESTION HARDENING DEFAULTS
// ---------------------------------------------------------------
set_config('shuffleanswers', 1, 'quiz');        // scramble A/B/C/D by default
set_config('questionsperpage', 1, 'quiz');      // one question per page
set_config('navmethod', 'free', 'quiz');        // free navigation grid
set_config('attemptonlast', 0, 'quiz');
set_config('password', '', 'quiz');
set_config('shuffleanswers', 1, 'qtype_multichoice');
set_config('answernumbering', 'abc', 'qtype_multichoice');
// Section-level "shuffle questions" default for new quizzes:
set_config('shufflequestions', 1, 'quiz');
eap_out('Quiz defaults: shuffle answers + shuffle questions + 1/page + free nav.');
$changed++;

// ---------------------------------------------------------------
// 5. RBAC  --  Invigilator / Proctor role (Exam Cell / Faculty / Student)
// ---------------------------------------------------------------
if (!$DB->record_exists('role', ['shortname' => 'invigilator'])) {
    $rid = create_role(
        'Invigilator / Proctor',
        'invigilator',
        'Faculty member: creates quizzes/question banks and runs live webcam proctoring & SEB review. No site administration.',
        'editingteacher'
    );
    set_role_contextlevels($rid, [CONTEXT_COURSE, CONTEXT_MODULE]);
    $sys = context_system::instance();
    $caps = [
        'mod/quiz:manage', 'mod/quiz:preview', 'mod/quiz:grade', 'mod/quiz:regrade',
        'mod/quiz:viewreports', 'mod/quiz:deleteattempts',
        'moodle/question:add', 'moodle/question:editall', 'moodle/question:useall',
        'quizaccess/seb:manage_seb_requiresafeexambrowser',
        'quizaccess/proctoring:viewreport', 'quizaccess/proctoring:sendnotification',
        'report/proctoring:view',
    ];
    foreach ($caps as $cap) {
        if (get_capability_info($cap)) {
            assign_capability($cap, CAP_ALLOW, $rid, $sys->id, true);
        }
    }
    eap_out("Created role 'invigilator' (id $rid) with quiz + proctoring capabilities.");
    $changed++;
} else {
    eap_out("Role 'invigilator' already present.");
}

// ---------------------------------------------------------------
// 6. Global niceties
// ---------------------------------------------------------------
set_config('enablewebservices', 1);
set_config('enablemobilewebservice', 0);
set_config('passwordpolicy', 1);
set_config('minpasswordlength', 8);
set_config('cronclionly', 1);                   // cron only via CLI (our cron container)
set_config('timezone', getenv('TZ') ?: 'UTC');
set_config('country', 'IN');

// ---------------------------------------------------------------
purge_all_caches();
if (function_exists('theme_reset_static_caches')) {
    theme_reset_static_caches();
}
eap_out("Done. $changed configuration groups applied. Caches purged.");
exit(0);
