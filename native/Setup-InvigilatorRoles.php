<?php
// =====================================================================
//  Two invigilation roles for the exam portal.
//
//  A) invigilator      "Invigilator / Proctor"      (already made by
//                        scripts/moodle/post_install.php) - faculty:
//                        builds exams + question banks, grades, reviews
//                        proctoring. Left as-is here, caps re-asserted.
//
//  B) examinvigilator  "Exam Hall Invigilator"       (NEW) - hall staff
//                        who only WATCH a live exam and INTERVENE:
//                        live proctoring feed, monitor report, and the
//                        Exam Control actions (pause / reopen / extend a
//                        student / force-submit / resume). CANNOT create
//                        or edit exams, touch questions, grade, delete
//                        attempts, or reach site administration.
//
//  Idempotent. Assign either role per course or per single quiz:
//    Course / Quiz  ->  Participants / "Assign roles"  ->  pick the role
//  or from CLI:
//    php native\stack\moodle\admin\cli\assign_roles.php ...
//
//    E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\Setup-InvigilatorRoles.php
//    ... --show      (list both roles + their caps, make no changes)
// =====================================================================
define('CLI_SCRIPT', true);
require(__DIR__ . '/stack/moodle/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/accesslib.php');

list($o) = cli_get_params(['show' => false], []);
function out($m) { cli_writeln('[invig] ' . $m); }

$sys = context_system::instance();

/**
 * Ensure a role exists with exactly the given ALLOW capabilities at system level.
 */
function eap_upsert_role(string $shortname, string $name, string $desc, string $archetype,
                         array $contextlevels, array $allowcaps, bool $showonly): void {
    global $DB, $sys;

    $role = $DB->get_record('role', ['shortname' => $shortname]);
    if (!$role) {
        if ($showonly) { out("role '$shortname' does NOT exist yet"); return; }
        $rid = create_role($name, $shortname, $desc, $archetype);
        $role = $DB->get_record('role', ['id' => $rid], '*', MUST_EXIST);
        out("created role '$shortname' (id $rid)");
    } else {
        out("role '$shortname' present (id {$role->id})");
    }
    $rid = $role->id;

    if ($showonly) {
        $have = $DB->get_records_menu('role_capabilities',
            ['roleid' => $rid, 'contextid' => $sys->id], 'capability', 'capability, permission');
        foreach ($have as $cap => $perm) {
            out(sprintf('    %-52s %s', $cap, $perm == CAP_ALLOW ? 'ALLOW' : $perm));
        }
        return;
    }

    set_role_contextlevels($rid, $contextlevels);

    // add the wanted caps
    $wanted = array_flip($allowcaps);
    foreach ($allowcaps as $cap) {
        if (!get_capability_info($cap)) { out("    skip (unknown cap) $cap"); continue; }
        assign_capability($cap, CAP_ALLOW, $rid, $sys->id, true);
    }
    // strip anything we did NOT ask for (keeps the role tight on re-run)
    $current = $DB->get_records('role_capabilities', ['roleid' => $rid, 'contextid' => $sys->id]);
    foreach ($current as $rc) {
        if (!isset($wanted[$rc->capability])) {
            unassign_capability($rc->capability, $rid, $sys->id);
            out("    removed stray cap {$rc->capability}");
        }
    }
    out("    " . count($allowcaps) . " capabilities set");
}

// ---- A) faculty invigilator - unchanged shape, just re-asserted ----
eap_upsert_role(
    'invigilator',
    'Invigilator / Proctor',
    'Faculty: builds quizzes & question banks, runs webcam proctoring & SEB review, grades. No site administration.',
    'editingteacher',
    [CONTEXT_COURSE, CONTEXT_MODULE],
    [
        'local/examwizard:use', 'local/examwizard:control',
        'mod/quiz:manage', 'mod/quiz:preview', 'mod/quiz:grade', 'mod/quiz:regrade',
        'mod/quiz:viewreports', 'mod/quiz:deleteattempts',
        'moodle/question:add', 'moodle/question:editall', 'moodle/question:useall',
        'quizaccess/seb:manage_seb_requiresafeexambrowser',
        'quizaccess/proctoring:viewreport', 'quizaccess/proctoring:sendnotification',
        'report/proctoring:view',
        'moodle/course:view',
    ],
    $o['show']
);

// ---- B) exam hall invigilator - watch + intervene ONLY ----
eap_upsert_role(
    'examinvigilator',
    'Exam Hall Invigilator',
    'Exam-hall staff: watches a live exam (proctoring feed + monitor) and can pause / reopen / '
        . 'extend a student / force-submit / resume attempts. Cannot create or edit exams, edit '
        . 'questions, grade, delete attempts, or access site administration.',
    'teacher',
    // SYSTEM too, so one hall-invigilator login can oversee every exam session.
    [CONTEXT_SYSTEM, CONTEXT_COURSE, CONTEXT_MODULE],
    [
        'local/examwizard:use',                 // reach the Exam Control hub
        'local/examwizard:control',             // the live pause/extend/submit/resume actions
        'mod/quiz:view',                        // SEE the quiz on the course page at all
        'mod/quiz:viewreports',                 // monitor report + control screen + core quiz report
        'mod/quiz:preview',                     // see what candidates see (read-only)
        'quizaccess/proctoring:viewreport',
        'report/proctoring:view',
        'moodle/course:view',                   // enter a course without being enrolled
        'moodle/course:viewhiddenactivities',   // exams are often hidden until go-time
        'moodle/course:viewhiddencourses',
        'moodle/user:viewdetails',              // resolve candidate names in the reports
    ],
    $o['show']
);

if ($o['show']) { exit(0); }

// let managers assign both roles from the "Assign roles" screens (dedup - the
// insert throws on a repeat, so only add the pairing when it is missing)
$managerrole = $DB->get_record('role', ['shortname' => 'manager']);
if ($managerrole) {
    foreach (['invigilator', 'examinvigilator'] as $sn) {
        $r = $DB->get_record('role', ['shortname' => $sn]);
        if ($r && !$DB->record_exists('role_allow_assign',
                ['roleid' => $managerrole->id, 'allowassign' => $r->id])) {
            $DB->insert_record('role_allow_assign',
                (object) ['roleid' => $managerrole->id, 'allowassign' => $r->id]);
        }
    }
}

purge_all_caches();
out('caches purged');

cli_writeln('');
cli_writeln('DONE.');
cli_writeln('  Faculty ......... assign role "invigilator"     on a course.');
cli_writeln('  Hall staff ...... assign role "examinvigilator" on a course, or on one quiz');
cli_writeln('                    (Quiz > Participants is per-course; use the quiz\'s context via');
cli_writeln('                     Course admin > Users > Other users, or Assign roles on the module).');
cli_writeln('  Review caps ..... php native\\Setup-InvigilatorRoles.php --show');
