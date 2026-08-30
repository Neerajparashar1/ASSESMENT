<?php
// =====================================================================
//  Create the sample students (if missing) and enrol them into a course
//  as "Student" via the manual enrolment method.
//
//  Run:
//    E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\enrol_test_students.php
//    ... --csv=E:\path\roll.csv --course=SEBPROCTEST --role=student
//
//  CSV columns: username,password,firstname,lastname,email[,idnumber,...]
//  Idempotent: existing users are reused, already-enrolled users are skipped.
// =====================================================================
define('CLI_SCRIPT', true);

require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/enrollib.php');

list($opts) = cli_get_params(
    ['csv' => 'E:\ASSESMENT\scripts\students_sample.csv', 'course' => 'SEBPROCTEST',
     'role' => 'student', 'help' => false],
    ['h' => 'help']);
if ($opts['help']) {
    cli_writeln("--csv=<file>  --course=<shortname|id>  --role=<shortname, default student>");
    exit(0);
}
function out($m) { cli_writeln('[enrol] ' . $m); }

// ---- course ----
$course = is_numeric($opts['course'])
    ? $DB->get_record('course', ['id' => (int)$opts['course']])
    : $DB->get_record('course', ['shortname' => $opts['course']]);
if (!$course) { cli_error("course '{$opts['course']}' not found"); }
$context = context_course::instance($course->id);
out("course {$course->id} : {$course->fullname}");

// ---- role ----
$role = $DB->get_record('role', ['shortname' => $opts['role']], '*', MUST_EXIST);

// ---- manual enrol instance ----
$plugin = enrol_get_plugin('manual');
$instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
if (!$instance) {
    $iid = $plugin->add_default_instance($course);
    if (!$iid) { $iid = $plugin->add_instance($course); }
    $instance = $DB->get_record('enrol', ['id' => $iid], '*', MUST_EXIST);
    out('added a manual enrolment instance to the course');
}
if ($instance->status != ENROL_INSTANCE_ENABLED) {
    $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
}

// ---- read CSV ----
if (!is_readable($opts['csv'])) { cli_error("cannot read CSV: {$opts['csv']}"); }
$rows = array_map('str_getcsv', file($opts['csv'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
$head = array_map(fn($h) => strtolower(trim($h)), array_shift($rows));
$need = ['username', 'firstname', 'lastname', 'email'];
foreach ($need as $n) {
    if (!in_array($n, $head, true)) { cli_error("CSV missing column: $n"); }
}

$created = 0; $enrolled = 0; $skipped = 0;
foreach ($rows as $r) {
    $rec = array_combine($head, array_pad($r, count($head), ''));
    $username = strtolower(trim($rec['username']));
    if ($username === '') { continue; }

    $user = $DB->get_record('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id]);
    if (!$user) {
        $new = new stdClass();
        $new->auth         = !empty($rec['auth']) ? $rec['auth'] : 'manual';
        $new->confirmed    = 1;
        $new->mnethostid   = $CFG->mnet_localhost_id;
        $new->username     = $username;
        $new->password     = !empty($rec['password']) ? $rec['password'] : 'Chang3Me!' . random_int(1000, 9999);
        $new->firstname    = trim($rec['firstname']);
        $new->lastname     = trim($rec['lastname']);
        $new->email        = trim($rec['email']);
        $new->idnumber     = $rec['idnumber'] ?? '';
        $new->lang         = !empty($rec['lang']) ? $rec['lang'] : ($CFG->lang ?? 'en');
        $new->timezone     = !empty($rec['timezone']) ? $rec['timezone'] : '99';
        $new->id = user_create_user($new, true, false);
        $user = $DB->get_record('user', ['id' => $new->id]);
        $created++;
        out("created user {$username} (id {$user->id})");
    }

    if (is_enrolled($context, $user, '', true)) {
        $skipped++;
        continue;
    }
    $plugin->enrol_user($instance, $user->id, $role->id, 0, 0, ENROL_USER_ACTIVE);
    $enrolled++;
    out("enrolled {$username} as {$role->shortname}");
}

// ---- summary ----
$total = count(get_enrolled_users($context));
cli_writeln('');
cli_writeln('=====================================================================');
cli_writeln(" created {$created} user(s)  |  enrolled {$enrolled}  |  already-enrolled {$skipped}");
cli_writeln(" course now has {$total} enrolled user(s)");
cli_writeln(" Participants: " . rtrim($CFG->wwwroot, '/') . "/user/index.php?id={$course->id}");
cli_writeln('=====================================================================');
exit(0);
