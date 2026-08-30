<?php
// Reset the 5 sample students to a simple policy-compliant password and
// clear any force-password-change / lockout flags so they can log in via SEB.
//   php _fix_student_logins.php            (dry run: shows state)
//   php _fix_student_logins.php --apply    (reset password to Exam@2026)
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/user/lib.php');

list($o) = cli_get_params(['apply' => false, 'pass' => 'Exam@2026'], []);

$users = $DB->get_records_select('user', "username LIKE 's2026%' AND deleted = 0", null, 'username');
foreach ($users as $u) {
    $force  = get_user_preferences('auth_forcepasswordchange', 0, $u->id);
    $failed = get_user_preferences('login_failed_count', 0, $u->id);
    $lock   = get_user_preferences('login_lockout', 0, $u->id);
    cli_writeln(sprintf("%-10s id=%-3d auth=%-7s forcepwchange=%s failedcount=%s lockout=%s",
        $u->username, $u->id, $u->auth, $force, $failed, $lock));

    if ($o['apply']) {
        update_internal_user_password($u, $o['pass']);
        unset_user_preference('auth_forcepasswordchange', $u);
        unset_user_preference('login_failed_count', $u);
        unset_user_preference('login_lockout', $u);
        unset_user_preference('login_lockout_secret', $u);
        unset_user_preference('login_lockout_ignored', $u);
        cli_writeln("   -> password reset to '{$o['pass']}', flags cleared");
    }
}

if (!$o['apply']) {
    cli_writeln("\n(dry run) re-run with --apply to reset all to '{$o['pass']}'");
} else {
    cli_writeln("\nDONE. Login: s2026001 .. s2026005  /  {$o['pass']}");
}
