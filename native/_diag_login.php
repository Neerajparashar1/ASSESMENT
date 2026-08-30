<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/authlib.php');

foreach ($DB->get_records_select('user', "username LIKE 's2026%'", null, 'username',
        'id,username,auth,confirmed,suspended,deleted,firstname,lastname,lastlogin,password') as $u) {
    printf("id=%-3d %-10s auth=%-8s conf=%d susp=%d del=%d lastlogin=%d hash=%s\n",
        $u->id, $u->username, $u->auth, $u->confirmed, $u->suspended, $u->deleted,
        $u->lastlogin, substr($u->password, 0, 7) . '...');
}

echo "\n-- relevant \$CFG --\n";
foreach (['passwordpolicy','lockoutthreshold','authloginviaemail','alternateloginurl',
          'auth','registerauth','wwwroot'] as $k) {
    echo "  $k = " . var_export($CFG->$k ?? '(unset)', true) . "\n";
}

// Actually test the credential for s2026001
$test = 'Chang3Me!001';
$user = $DB->get_record('user', ['username' => 's2026001']);
if ($user) {
    $ok = validate_internal_user_password($user, $test);
    echo "\nvalidate_internal_user_password('s2026001', '$test') => " . ($ok ? 'OK' : 'FAIL') . "\n";
    $authplugin = get_auth_plugin($user->auth);
    echo "auth plugin '{$user->auth}' user_login => " .
        ($authplugin->user_login('s2026001', $test) ? 'OK' : 'FAIL') . "\n";
}

// lockout state
$locked = $DB->get_records_select('user_preferences',
    "name IN ('login_lockout','login_failed_count','login_lockout_secret') AND userid = :uid",
    ['uid' => $user->id ?? 0]);
echo "\nlockout prefs for s2026001: " . ($locked ? json_encode(array_map(fn($p)=>[$p->name=>$p->value], $locked)) : 'none') . "\n";
