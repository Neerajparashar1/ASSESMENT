<?php
// Exam Wizard - reset a candidate's login password and read the new one back once.
//
//   credentials.php                         -> search form
//   credentials.php?courseid=N              -> list that subject's enrolled users
//   credentials.php?q=text                  -> search everyone by name / username / email
//   (POST reset=1 userid=/userids[]=)       -> issue fresh passwords, show them once
//   (POST downloadcsv=1)                    -> download the passwords just issued
//
// Moodle only ever stores a one-way hash of a password, so an EXISTING password
// can never be revealed. This page issues a NEW policy-valid password instead,
// shows it a single time, and optionally forces a change at next login.

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');   // local_examwizard_generate_password()

require_login();
$context = context_system::instance();
require_capability('local/examwizard:resetpassword', $context);

$courseid = optional_param('courseid', 0, PARAM_INT);
$q        = optional_param('q', '', PARAM_RAW_TRIMMED);

$baseurl = new moodle_url('/local/examwizard/credentials.php');
$PAGE->set_url($baseurl, array_filter(['courseid' => $courseid, 'q' => $q]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('cred_title', 'local_examwizard'));
$PAGE->set_heading(get_string('cred_title', 'local_examwizard'));
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'), new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('cred_title', 'local_examwizard'));

$s = fn($id, $a = null) => get_string($id, 'local_examwizard', $a);

// Site admins can never be targeted from here.
$adminids = array_map('intval', explode(',', $CFG->siteadmins ?? ''));

// ---------------------------------------------------------------------
//  Download the CSV of the passwords issued in the previous step.
// ---------------------------------------------------------------------
if (optional_param('downloadcsv', 0, PARAM_BOOL) && confirm_sesskey()) {
    $dump = $SESSION->local_examwizard_pwdump ?? [];
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="issued-passwords-' . date('Ymd-His') . '.csv"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, ['username', 'name', 'password']);
    foreach ($dump as $r) {
        fputcsv($out, [$r['username'], $r['name'], $r['password']]);
    }
    fclose($out);
    exit;
}

// ---------------------------------------------------------------------
//  Perform the reset(s).
// ---------------------------------------------------------------------
$results = [];
$skipped = [];
if (optional_param('reset', 0, PARAM_BOOL) && confirm_sesskey()) {
    require_once($CFG->dirroot . '/lib/moodlelib.php');

    $force  = optional_param('forcechange', 0, PARAM_BOOL);
    $ids    = optional_param_array('userids', [], PARAM_INT);
    $single = optional_param('userid', 0, PARAM_INT);
    if ($single) {
        $ids[] = $single;
    }
    $ids = array_values(array_unique(array_filter($ids)));

    foreach ($ids as $uid) {
        if (in_array($uid, $adminids, true) || $uid == $USER->id) {
            continue;
        }
        $u = $DB->get_record('user', ['id' => $uid, 'deleted' => 0, 'mnethostid' => $CFG->mnet_localhost_id]);
        if (!$u || isguestuser($u)) {
            continue;
        }
        if ($u->auth !== 'manual') {
            $skipped[] = fullname($u) . ' (' . s($u->auth) . ')';
            continue;
        }

        $newpw = local_examwizard_generate_password();
        update_internal_user_password($u, $newpw);
        set_user_preference('auth_forcepasswordchange', $force ? 1 : 0, $u);

        // Audit trail - shows up under Reports > Logs.
        \core\event\user_updated::create_from_userid($u->id)->trigger();

        $results[] = [
            'name'     => fullname($u),
            'username' => $u->username,
            'password' => $newpw,
        ];
    }

    $SESSION->local_examwizard_pwdump = $results;
}

// ---------------------------------------------------------------------
//  Find the users to list.
// ---------------------------------------------------------------------
$users = [];
$listheading = '';
if ($courseid) {
    $course = get_course($courseid);
    $coursecontext = context_course::instance($courseid);
    require_capability('moodle/course:enrolreview', $coursecontext);
    $users = get_enrolled_users($coursecontext, '', 0, 'u.id, u.firstname, u.lastname, u.username, u.email, u.auth, u.lastaccess',
        'u.lastname ASC, u.firstname ASC', 0, 50);
    $listheading = $s('cred_incourse', format_string($course->fullname));
} else if (core_text::strlen($q) >= 2) {
    $like = $DB->sql_like($DB->sql_concat('u.firstname', "' '", 'u.lastname', "' '", 'u.username', "' '", 'u.email'),
        ':term', false, false);
    $sql = "SELECT u.id, u.firstname, u.lastname, u.username, u.email, u.auth, u.lastaccess
              FROM {user} u
             WHERE u.deleted = 0
               AND u.mnethostid = :mnet
               AND u.id <> :guestid
               AND $like
          ORDER BY u.lastname ASC, u.firstname ASC";
    $users = $DB->get_records_sql($sql, [
        'term'    => '%' . $DB->sql_like_escape($q) . '%',
        'mnet'    => $CFG->mnet_localhost_id,
        'guestid' => $CFG->siteguest,
    ], 0, 50);
    $listheading = $s('cred_searchresults', s($q));
}

// ---------------------------------------------------------------------
//  Render.
// ---------------------------------------------------------------------
$PAGE->requires->js_amd_inline("
    require(['jquery'], function(\$){
        \$('.ew-copy').on('click', function(){
            var t = \$(this).data('copy');
            navigator.clipboard && navigator.clipboard.writeText(t);
            \$(this).text('" . $s('copied') . "');
        });
        \$('#ew-cred-all').on('change', function(){
            \$('.ew-cred-cb').prop('checked', \$(this).prop('checked'));
        });
    });
");

echo $OUTPUT->header();
echo $OUTPUT->heading($s('cred_title'));
echo html_writer::div($s('cred_intro'), 'lead text-muted mb-2');
echo $OUTPUT->notification($s('cred_hashwarning'), \core\output\notification::NOTIFY_WARNING);

// ---- results panel from a reset we just did ----
if ($results) {
    echo html_writer::start_div('ew-card ew-card-live');
    echo html_writer::tag('h3', $s('cred_issued', count($results)), ['class' => 'ew-card-h']);
    echo html_writer::div($s('cred_issued_once'), 'small text-muted mb-2');
    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('st_name')) .
        html_writer::tag('th', $s('st_username')) .
        html_writer::tag('th', $s('cred_newpassword')) .
        html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($results as $r) {
        echo html_writer::tag('tr',
            html_writer::tag('td', s($r['name'])) .
            html_writer::tag('td', html_writer::tag('code', s($r['username']))) .
            html_writer::tag('td', html_writer::tag('code', s($r['password']), ['class' => 'ew-pw'])) .
            html_writer::tag('td', html_writer::tag('button', $s('help_copy'),
                ['type' => 'button', 'class' => 'ew-copy btn btn-sm btn-outline-secondary',
                 'data-copy' => $r['password']])));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
    echo html_writer::div(html_writer::link(
        new moodle_url($baseurl, ['downloadcsv' => 1, 'sesskey' => sesskey()]),
        $s('cred_downloadcsv'), ['class' => 'btn btn-outline-secondary btn-sm mt-2']), '');
    echo html_writer::end_div();
}
if ($skipped) {
    echo $OUTPUT->notification($s('cred_skipped', implode(', ', array_map('s', $skipped))),
        \core\output\notification::NOTIFY_INFO);
}

// ---- search box ----
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $baseurl->out(false), 'class' => 'ew-cred-search mb-3']);
echo html_writer::tag('label', $s('cred_search'), ['for' => 'ew-cred-q', 'class' => 'font-weight-bold mr-2']);
echo html_writer::empty_tag('input', ['type' => 'text', 'id' => 'ew-cred-q', 'name' => 'q', 'value' => $q,
    'size' => 32, 'placeholder' => $s('cred_search_ph'), 'class' => 'mr-2']);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => $s('cred_search_go'),
    'class' => 'btn btn-primary']);
echo html_writer::end_tag('form');

// ---- user list + reset form ----
if ($listheading) {
    echo html_writer::tag('h3', $listheading, ['class' => 'ew-card-h']);
}
if (($courseid || core_text::strlen($q) >= 2) && !$users) {
    echo $OUTPUT->notification($s('cred_none'), \core\output\notification::NOTIFY_INFO);
} else if ($users) {
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false)]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'reset', 'value' => 1]);

    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', html_writer::empty_tag('input', ['type' => 'checkbox', 'id' => 'ew-cred-all'])) .
        html_writer::tag('th', $s('st_name')) .
        html_writer::tag('th', $s('st_username')) .
        html_writer::tag('th', get_string('email')) .
        html_writer::tag('th', get_string('lastaccess')) .
        html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($users as $u) {
        $isadmin = in_array((int) $u->id, $adminids, true);
        $isself  = ($u->id == $USER->id);
        $blocked = $isadmin || $isself || $u->auth !== 'manual';
        $note = '';
        if ($isadmin) {
            $note = $s('cred_isadmin');
        } else if ($isself) {
            $note = $s('cred_isself');
        } else if ($u->auth !== 'manual') {
            $note = $s('cred_notmanual', s($u->auth));
        }
        $cell = $blocked
            ? html_writer::tag('span', $note, ['class' => 'text-muted small'])
            : html_writer::empty_tag('input', ['type' => 'checkbox', 'class' => 'ew-cred-cb',
                'name' => 'userids[]', 'value' => $u->id]);
        echo html_writer::tag('tr',
            html_writer::tag('td', $blocked ? '' : $cell) .
            html_writer::tag('td', s(fullname($u))) .
            html_writer::tag('td', html_writer::tag('code', s($u->username))) .
            html_writer::tag('td', s($u->email)) .
            html_writer::tag('td', $u->lastaccess ? userdate($u->lastaccess, get_string('strftimedatetimeshort'))
                : html_writer::tag('span', $s('cred_never'), ['class' => 'text-muted'])) .
            html_writer::tag('td', $blocked ? html_writer::tag('span', $note, ['class' => 'text-muted small']) : ''));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');

    echo html_writer::start_div('mt-2');
    echo html_writer::empty_tag('input', ['type' => 'checkbox', 'name' => 'forcechange', 'value' => 1,
        'checked' => 'checked', 'id' => 'ew-cred-force']);
    echo html_writer::tag('label', ' ' . $s('cred_forcechange'), ['for' => 'ew-cred-force', 'class' => 'ml-1']);
    echo html_writer::end_div();

    echo html_writer::div(html_writer::empty_tag('input', ['type' => 'submit',
        'value' => $s('cred_resetselected'), 'class' => 'btn btn-primary mt-2']), '');
    echo html_writer::end_tag('form');
} else {
    echo html_writer::div($s('cred_startsearch'), 'text-muted');
}

echo $OUTPUT->footer();
