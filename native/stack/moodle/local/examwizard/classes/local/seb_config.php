<?php
// Build the portal "bootstrap" Safe Exam Browser config.

namespace local_examwizard\local;

defined('MOODLE_INTERNAL') || die();

/**
 * One .seb file for the whole exam portal.
 *
 * It carries no exam-specific settings - just enough to launch SEB into the
 * portal login page in kiosk mode. Each quiz's real SEB settings are pulled
 * live from Moodle when the candidate opens the quiz (autoreconfigureseb),
 * so this file never has to be re-distributed when a quiz setting changes.
 */
class seb_config {

    /**
     * The bootstrap .seb as a plist XML string.
     */
    public static function portal_plist(): string {
        global $CFG;

        $www = rtrim($CFG->wwwroot, '/');
        $starturl = $www . '/login/index.php';
        // SEB reconfigures from / quits to this Moodle endpoint.
        $quiturl = $www . '/mod/quiz/accessrule/seb/config.php';
        $quitpw = (string) get_config('quizaccess_seb', 'quitpassword');

        $b = fn($k, $v) => "    <key>$k</key>\n    " . ($v ? "<true/>" : "<false/>") . "\n";
        $i = fn($k, $v) => "    <key>$k</key>\n    <integer>$v</integer>\n";
        $s = fn($k, $v) => "    <key>$k</key>\n    <string>" . htmlspecialchars($v, ENT_XML1) . "</string>\n";

        $x  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $x .= "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n";
        $x .= "<!--\n  ITM Group of Institutions, Gwalior - Online Examination Portal\n";
        $x .= "  Portal bootstrap SEB config. Hand this ONE file to every candidate.\n";
        $x .= "  Do NOT re-distribute it when a quiz setting changes - SEB pulls the\n";
        $x .= "  current exam config from Moodle on launch (autoreconfigureseb).\n-->\n";
        $x .= "<plist version=\"1.0\">\n<dict>\n";

        // start / quit
        $x .= $s('startURL', $starturl);
        $x .= $s('sebServerURL', '');
        $x .= $s('quitURL', $quiturl);
        if (trim($quitpw) !== '') {
            $x .= $s('hashedQuitPassword', hash('sha256', $quitpw));
        }
        $x .= $b('allowQuit', true);
        $x .= $b('quitURLConfirm', true);

        // this file starts an exam session (not a client config)
        $x .= $i('sebConfigPurpose', 0);

        // integrity - send the keys so Moodle can verify + trigger reconfigure
        $x .= $b('sendBrowserExamKey', true);
        $x .= $b('examSessionClearCookiesOnStart', true);
        $x .= $b('examSessionClearCookiesOnEnd', true);

        // kiosk / OS lockdown (matches the project's exam-default config)
        $x .= $i('browserViewMode', 1);                 // full screen
        $x .= $b('allowSwitchToApplications', false);
        $x .= $b('allowFlashFullscreen', false);
        $x .= $b('enableAppSwitcherCheck', true);
        $x .= $b('forceAppFolderInstall', true);
        $x .= $b('allowUserSwitching', false);
        $x .= $b('enableLogging', true);
        $x .= $b('createNewDesktop', true);             // Windows: isolates the desktop
        $x .= $b('killExplorerShell', true);
        $x .= $b('allowWlan', false);
        $x .= $b('enableAltTab', false);
        $x .= $b('enableAltEsc', false);
        $x .= $b('enableCtrlEsc', false);
        $x .= $b('enableAltF4', false);
        $x .= $b('enableRightMouse', false);
        $x .= $b('enablePrintScreen', false);
        $x .= $b('allowScreenSharing', false);
        $x .= $b('allowDisplayMirroring', false);
        $x .= $b('allowVirtualMachine', false);

        // browser chrome
        $x .= $b('showTaskBar', true);
        $x .= $b('showReloadButton', true);
        $x .= $b('showTime', true);
        $x .= $b('showInputLanguage', true);
        $x .= $b('browserWindowAllowReload', false);
        $x .= $b('allowPreferencesWindow', false);
        $x .= $b('audioControlEnabled', false);

        // no URL filter here - the candidate must reach the whole Moodle site to
        // get to the quiz; the per-quiz config it reconfigures to can lock URLs.
        $x .= $b('URLFilterEnable', false);
        $x .= $b('URLFilterEnableContentFilter', false);
        $x .= "    <key>URLFilterRules</key>\n    <array/>\n";

        // camera off in the bootstrap (a proctored quiz turns it on via reconfigure)
        $x .= $b('browserMediaCaptureCamera', false);
        $x .= $b('browserMediaCaptureMicrophone', false);

        $x .= "</dict>\n</plist>\n";
        return $x;
    }

    /** Suggested download filename. */
    public static function portal_filename(): string {
        global $SITE;
        $name = preg_replace('~[^A-Za-z0-9]+~', '-', (string) $SITE->shortname) ?: 'Exam';
        return trim($name, '-') . '-Exam-Portal.seb';
    }
}
