<?php
// =====================================================================
//  B - "Quit button = only way out"  SEB hardening.
//
//  Moodle's "Configure manually" SEB mode (requiresafeexambrowser = 1)
//  can only emit the handful of keys the quiz SEB form exposes - it can
//  NOT emit createNewDesktop / killExplorerShell / enableAltF4=false etc.
//  Those OS-kiosk keys only survive when the per-quiz config is built
//  from a TEMPLATE (requiresafeexambrowser = 2 = USE_SEB_TEMPLATE).
//
//  This script:
//    1. creates / updates an enabled SEB template "EAP Kiosk Lockdown"
//       whose plist carries the full Windows kiosk lockdown,
//    2. re-points quizzes at it (--cmid=N, or --all for every exam whose
//       quit link already goes to local/sebkiosk/finish.php),
//       flipping them to template mode while keeping their own quit link
//       / quit password / task-bar settings,
//    3. regenerates each quiz's SEB config + config key and clears the
//       seb caches,
//    4. prints the generated .seb so you can eyeball the hard keys.
//
//  Idempotent - safe to re-run. The per-quiz "leave = submit" wiring
//  from harden_seb_quiz.php / the exam wizard is untouched.
//
//    E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\Setup-SebLockdownTemplate.php --cmid=13
//    ... --all
//    ... --cmid=13 --show          (just dump the generated config, no changes)
//    ... --name="EAP Kiosk Lockdown"
// =====================================================================
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');

use quizaccess_seb\template;
use quizaccess_seb\seb_quiz_settings;
use quizaccess_seb\settings_provider;

list($o) = cli_get_params(
    ['cmid' => 0, 'all' => false, 'show' => false, 'name' => 'EAP Kiosk Lockdown'],
    ['h' => 'help']
);
function out($m) { cli_writeln('[seblock] ' . $m); }

// ---------------------------------------------------------------------
// The hardened template plist.  Moodle injects startURL +
// sendBrowserExamKey + browserWindowWebView itself.
//
// quitURL IS baked in here (cmid-less local/sebkiosk/finish.php): in
// TEMPLATE mode Moodle only ever *reads* quitURL from the template, it
// never writes the per-quiz linkquitseb into the config - so without
// this line the SEB "Quit" button would just quit and NOT submit.
// finish.php with no cmid finishes every in-progress attempt for the
// caller, which is exactly right for a one-exam kiosk.
//
// URL filtering is deliberately left OFF (matches the current wizard;
// turning it on risks white-screening the exam behind a CDN).
// ---------------------------------------------------------------------
$quiturl = rtrim($CFG->wwwroot, '/') . '/local/sebkiosk/finish.php';
$plistxml = <<<'PLIST'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>browserViewMode</key><integer>1</integer>
    <key>mainBrowserWindowWidth</key><string>100%</string>
    <key>mainBrowserWindowHeight</key><string>100%</string>
    <key>touchOptimized</key><false/>

    <!-- app / desktop isolation: no Task Manager, no Explorer, no Alt-Tab out -->
    <key>allowSwitchToApplications</key><false/>
    <key>allowUserSwitching</key><false/>
    <key>enableAppSwitcherCheck</key><true/>
    <key>forceAppFolderInstall</key><false/>
    <key>forceUnfocusedAppFolderCheck</key><true/>
    <key>createNewDesktop</key><true/>
    <key>killExplorerShell</key><true/>
    <key>allowWlan</key><false/>
    <key>enableStartMenu</key><false/>
    <key>enableChromeNotifications</key><false/>

    <!-- hotkeys -->
    <key>enableAltTab</key><false/>
    <key>enableAltEsc</key><false/>
    <key>enableCtrlEsc</key><false/>
    <key>enableAltF4</key><false/>
    <key>enableAltMouseWheel</key><false/>
    <key>enableRightMouse</key><false/>
    <key>enablePrintScreen</key><false/>
    <key>enableEsc</key><false/>
    <key>enableF1</key><false/>
    <key>enableF2</key><false/>
    <key>enableF3</key><false/>
    <key>enableF4</key><false/>
    <key>enableF5</key><false/>
    <key>enableF6</key><false/>
    <key>enableF7</key><false/>
    <key>enableF8</key><false/>
    <key>enableF9</key><false/>
    <key>enableF10</key><false/>
    <key>enableF11</key><false/>
    <key>enableF12</key><false/>

    <!-- displays / capture -->
    <key>allowedDisplaysMaxNumber</key><integer>1</integer>
    <key>allowedDisplayBuiltin</key><true/>
    <key>allowDisplayMirroring</key><false/>
    <key>allowVirtualMachine</key><false/>
    <key>allowScreenSharing</key><false/>
    <key>allowWindowCapture</key><false/>

    <!-- browser chrome -->
    <key>showMenuBar</key><false/>
    <key>showTaskBar</key><true/>
    <key>taskBarHeight</key><integer>40</integer>
    <key>showReloadButton</key><false/>
    <key>browserWindowAllowReload</key><false/>
    <key>allowReloadInExam</key><false/>
    <key>enableBrowserWindowToolbar</key><false/>
    <key>hideBrowserWindowToolbar</key><true/>
    <key>allowDownUploads</key><false/>
    <key>allowPDFPlugIn</key><false/>
    <key>newBrowserWindowByLinkPolicy</key><integer>0</integer>
    <key>newBrowserWindowByScriptPolicy</key><integer>0</integer>
    <key>blockPopUpWindows</key><true/>

    <!-- misc -->
    <key>audioControlEnabled</key><false/>
    <key>audioMute</key><false/>
    <key>allowDictionaryLookup</key><false/>
    <key>allowSpellCheck</key><false/>
    <key>allowPreferencesWindow</key><false/>
    <key>enableLogging</key><true/>
    <key>allowApplicationLog</key><true/>

    <!-- quit: the button exists (that is the ONE sanctioned exit) and
         the session is NOT torn down before the quit URL is hit, so
         local/sebkiosk/finish.php still has a login to submit with -->
    <key>allowQuit</key><true/>
    <key>quitURLConfirm</key><true/>
    <key>quitURL</key><string>__QUITURL__</string>
    <key>examSessionClearCookiesOnStart</key><true/>
    <key>examSessionClearCookiesOnEnd</key><false/>

    <key>originatorVersion</key><string>EAP-1.0-lockdown</string>
</dict>
</plist>
PLIST;
$plistxml = str_replace('__QUITURL__', $quiturl, $plistxml);

// ---------------------------------------------------------------------
// 1. upsert the template
// ---------------------------------------------------------------------
$existing = template::get_record(['name' => $o['name']]);
if ($existing) {
    $existing->set('content', $plistxml);
    $existing->set('enabled', 1);
    $existing->save();
    $tpl = $existing;
    out("template updated: id={$tpl->get('id')}  name=\"{$o['name']}\"");
} else {
    $tpl = new template(0, (object) [
        'name'        => $o['name'],
        'description' => 'Windows kiosk lockdown (new desktop, no Explorer/Task Manager, hotkeys off). '
                       . 'Managed by native\\Setup-SebLockdownTemplate.php - do not hand edit.',
        'content'     => $plistxml,
        'enabled'     => 1,
        'sortorder'   => 1,
    ]);
    $tpl->save();
    out("template created: id={$tpl->get('id')}  name=\"{$o['name']}\"");
}
$tplid = (int) $tpl->get('id');

// ---------------------------------------------------------------------
// pick target quizzes
// ---------------------------------------------------------------------
$targets = [];
if ($o['cmid']) {
    $cm = get_coursemodule_from_id('quiz', (int) $o['cmid'], 0, false, MUST_EXIST);
    $targets[] = (int) $cm->instance;
} else if ($o['all']) {
    $rs = $DB->get_records_select('quizaccess_seb_quizsettings',
        $DB->sql_like('linkquitseb', ':lk') . ' AND requiresafeexambrowser <> 0',
        ['lk' => '%/local/sebkiosk/finish.php%'], '', 'quizid');
    $targets = array_map('intval', array_column($rs, 'quizid'));
}

if (!$targets && !$o['show']) {
    out('nothing to do - pass --cmid=<N> or --all  (or --show to only dump a config)');
    out("template id is {$tplid}; point a quiz at it by hand with:");
    out("  UPDATE mdl_quizaccess_seb_quizsettings SET requiresafeexambrowser=2, templateid={$tplid} WHERE quizid=?;");
    exit(0);
}

// ---------------------------------------------------------------------
// 2 + 3. re-point each quiz, regenerate config
// ---------------------------------------------------------------------
$hardkeys = ['createNewDesktop', 'killExplorerShell', 'enableAltF4', 'allowSwitchToApplications', 'allowedDisplaysMaxNumber'];

foreach ($targets as $quizid) {
    $s = seb_quiz_settings::get_record(['quizid' => $quizid]);
    if (!$s) { out("quiz {$quizid}: no SEB settings row - skipped"); continue; }

    $name = $DB->get_field('quiz', 'name', ['id' => $quizid]);

    if (!$o['show']) {
        $s->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE); // = 2
        $s->set('templateid', $tplid);
        $s->save(); // after_save() rebuilds config + configkey caches
        out("quiz {$quizid} (\"{$name}\"): -> template mode, templateid={$tplid}");
    }

    $cfg = seb_quiz_settings::get_config_by_quiz_id($quizid);
    $key = seb_quiz_settings::get_config_key_by_quiz_id($quizid);
    $present = [];
    foreach ($hardkeys as $k) {
        $present[$k] = (strpos((string) $cfg, "<key>{$k}</key>") !== false) ? 'yes' : 'MISSING';
    }
    out("quiz {$quizid}: config key = {$key}");
    out("quiz {$quizid}: hard keys  = " . json_encode($present));
    if ($o['show']) {
        cli_writeln("---- generated .seb for quiz {$quizid} ----");
        cli_writeln((string) $cfg);
        cli_writeln("---- end ----");
    }
}

// ---------------------------------------------------------------------
// 4. clear seb caches (belt-and-braces; after_save already did per-quiz)
// ---------------------------------------------------------------------
\cache::make('quizaccess_seb', 'config')->purge();
\cache::make('quizaccess_seb', 'configkey')->purge();
\cache::make('quizaccess_seb', 'quizsettings')->purge();
out('seb caches purged');

cli_writeln('');
cli_writeln('DONE.  Students now get the kiosk-locked config on launch (auto-reconfigure).');
cli_writeln('The SEB task-bar Quit button -> local/sebkiosk/finish.php is the only exit;');
cli_writeln('Alt+Tab / Alt+F4 / Task Manager / second screen are gone.');
cli_writeln('Re-download to inspect:  ' . rtrim($CFG->wwwroot, '/') . '/mod/quiz/accessrule/seb/config.php?cmid=<cmid>');
