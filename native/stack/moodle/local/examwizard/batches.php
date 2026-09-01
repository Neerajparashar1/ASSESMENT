<?php
// Exam Wizard - Batches: manage sections/batches as Moodle cohorts and
// cohort-sync a whole batch into a course in one step.
//
//   batches.php                       -> list all batches + "new batch"
//   batches.php?enrolinto=C           -> same, with an "enrol a batch into course C" panel
//   batches.php?id=N                  -> members of batch N (add / remove) + its course syncs
//   POST create        name [, code]                -> create a batch
//   POST addmembers    id, userids[]                -> add students to a batch
//   POST removemember  id, userid                   -> remove one student
//   POST enrol         id, courseid                 -> cohort-sync the batch into a course
//   POST unsync        id, enrolid                  -> stop a course sync
//   POST deletebatch   id, confirm                  -> delete an empty batch
//
// A batch is a Moodle cohort at system context. "Enrol a batch" adds an
// enrol_cohort instance (role = Student) so every current AND future member
// is enrolled automatically; removing a member unenrols them from every
// synced course.

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/cohort:manage', $context);

$id        = optional_param('id', 0, PARAM_INT);
$enrolinto = optional_param('enrolinto', 0, PARAM_INT);
$q         = optional_param('q', '', PARAM_RAW_TRIMMED);

$baseurl = new moodle_url('/local/examwizard/batches.php');
$PAGE->set_url($baseurl, array_filter(['id' => $id, 'enrolinto' => $enrolinto]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('b_title', 'local_examwizard'));
$PAGE->set_heading(get_string('b_title', 'local_examwizard'));
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'), new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('b_title', 'local_examwizard'));

$s = fn($k, $a = null) => get_string($k, 'local_examwizard', $a);
$studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
$cohortplugin  = enrol_get_plugin('cohort');

/** id => "SHORT — Full name" for every real course. */
$coursemenu = function () use ($DB) {
    $out = [];
    foreach ($DB->get_records_select('course', 'id <> ?', [SITEID], 'fullname ASC',
            'id, fullname, shortname') as $c) {
        $out[$c->id] = format_string($c->shortname . ' — ' . $c->fullname);
    }
    return $out;
};

/** Courses a batch is currently synced into. */
$batchsyncs = function (int $cohortid) use ($DB) {
    return $DB->get_records_sql(
        "SELECT e.id AS enrolid, e.courseid, e.status, c.fullname, c.shortname
           FROM {enrol} e
           JOIN {course} c ON c.id = e.courseid
          WHERE e.enrol = 'cohort' AND e.customint1 = ?
       ORDER BY c.fullname", [$cohortid]);
};

$membercount = fn(int $cid) => $DB->count_records('cohort_members', ['cohortid' => $cid]);

// ---------------------------------------------------------------------
//  POST actions
// ---------------------------------------------------------------------
if ($data = data_submitted()) {
    require_sesskey();
    $do = optional_param('do', '', PARAM_ALPHA);

    if ($do === 'create') {
        $name = trim(optional_param('name', '', PARAM_TEXT));
        $code = trim(optional_param('code', '', PARAM_RAW_TRIMMED));
        if ($name === '') {
            redirect($baseurl, $s('b_err_noname'), null, \core\output\notification::NOTIFY_ERROR);
        }
        $code = strtoupper(preg_replace('~[^A-Za-z0-9]+~', '', $code));
        if ($code === '') {
            $code = local_examwizard_batch_idnumber($name);
        } else if ($DB->record_exists('cohort', ['idnumber' => $code])) {
            redirect($baseurl, $s('b_err_dupcode', s($code)), null, \core\output\notification::NOTIFY_ERROR);
        }
        $newid = cohort_add_cohort((object) [
            'name'              => $name,
            'idnumber'          => $code,
            'contextid'         => $context->id,
            'description'       => '',
            'descriptionformat' => FORMAT_HTML,
            'visible'           => 1,
        ]);
        redirect(new moodle_url($baseurl, ['id' => $newid]), $s('b_created', s($name)));
    }

    // Everything below needs a valid batch.
    $cohort = $DB->get_record('cohort', ['id' => $id, 'contextid' => $context->id], '*', MUST_EXIST);

    if ($do === 'addmembers') {
        $uids = array_values(array_unique(array_filter(optional_param_array('userids', [], PARAM_INT))));
        $added = 0;
        foreach ($uids as $uid) {
            if ($DB->record_exists('user', ['id' => $uid, 'deleted' => 0])
                    && !cohort_is_member($cohort->id, $uid)) {
                cohort_add_member($cohort->id, $uid);
                $added++;
            }
        }
        enrol_cohort_sync(new null_progress_trace());
        redirect(new moodle_url($baseurl, ['id' => $id]), $s('b_added_n', $added));
    }

    if ($do === 'bulkadd') {
        // One username or email per line. Existing accounts only.
        $raw = optional_param('idlist', '', PARAM_RAW);
        $tokens = preg_split('~[\r\n,;]+~', \core_text::strtolower(trim($raw)), -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_values(array_unique(array_map('trim', $tokens)));

        $added = 0; $already = 0; $notfound = [];
        foreach ($tokens as $tok) {
            if ($tok === '') {
                continue;
            }
            $u = $DB->get_record('user',
                ['username' => $tok, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0]);
            if (!$u && strpos($tok, '@') !== false) {
                $u = $DB->get_record('user',
                    ['email' => $tok, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0]);
            }
            if (!$u) {
                $notfound[] = $tok;
            } else if (cohort_is_member($cohort->id, $u->id)) {
                $already++;
            } else {
                cohort_add_member($cohort->id, $u->id);
                $added++;
            }
        }
        if ($added) {
            enrol_cohort_sync(new null_progress_trace());
        }
        $SESSION->local_examwizard_bulkadd = [
            'id' => $cohort->id, 'added' => $added, 'already' => $already,
            'notfound' => array_slice($notfound, 0, 200),
        ];
        redirect(new moodle_url($baseurl, ['id' => $id]));
    }

    if ($do === 'removemember') {
        $uid = required_param('userid', PARAM_INT);
        if (cohort_is_member($cohort->id, $uid)) {
            cohort_remove_member($cohort->id, $uid);
            enrol_cohort_sync(new null_progress_trace());
        }
        redirect(new moodle_url($baseurl, ['id' => $id]), $s('b_removed'));
    }

    if ($do === 'enrol') {
        $courseid = required_param('courseid', PARAM_INT);
        if ($courseid == SITEID) {
            redirect(new moodle_url($baseurl, ['id' => $id]), $s('b_err_frontpage'), null,
                \core\output\notification::NOTIFY_ERROR);
        }
        $course = get_course($courseid);
        require_capability('enrol/cohort:config', context_course::instance($courseid));
        local_examwizard_ensure_batch_sync($cohort->id, $course, $studentroleid);
        redirect(new moodle_url($baseurl, ['id' => $id]),
            $s('b_enrolled', (object) ['batch' => s($cohort->name), 'course' => format_string($course->shortname)]));
    }

    if ($do === 'unsync') {
        $enrolid = required_param('enrolid', PARAM_INT);
        $inst = $DB->get_record('enrol',
            ['id' => $enrolid, 'enrol' => 'cohort', 'customint1' => $cohort->id], '*', MUST_EXIST);
        require_capability('enrol/cohort:config', context_course::instance($inst->courseid));
        $cohortplugin->delete_instance($inst);
        redirect(new moodle_url($baseurl, ['id' => $id]), $s('b_unsynced'));
    }

    if ($do === 'deletebatch') {
        if (!optional_param('confirm', 0, PARAM_BOOL)) {
            redirect(new moodle_url($baseurl, ['id' => $id]));
        }
        if ($batchsyncs($cohort->id)) {
            redirect(new moodle_url($baseurl, ['id' => $id]), $s('b_err_hassyncs'), null,
                \core\output\notification::NOTIFY_ERROR);
        }
        cohort_delete_cohort($cohort);
        redirect($baseurl, $s('b_deleted', s($cohort->name)));
    }

    redirect($baseurl);
}

$PAGE->requires->js_amd_inline("
    require(['jquery'], function(\$){
        \$('#ew-b-all').on('change', function(){
            \$('.ew-b-cb').prop('checked', \$(this).prop('checked'));
        });
    });
");

echo $OUTPUT->header();

// =====================================================================
//  MEMBERS VIEW
// =====================================================================
if ($id) {
    $cohort = $DB->get_record('cohort', ['id' => $id, 'contextid' => $context->id], '*', MUST_EXIST);

    echo html_writer::div(html_writer::link($baseurl, '← ' . $s('b_allbatches'), ['class' => 'btn btn-link pl-0']));
    echo $OUTPUT->heading(s($cohort->name));
    echo html_writer::div($s('b_code') . ': ' . html_writer::tag('code', s($cohort->idnumber))
        . ' &middot; ' . $s('b_membercount', $membercount($cohort->id)), 'text-muted mb-3');

    // ---- result of a bulk add we just did ----
    if (!empty($SESSION->local_examwizard_bulkadd)
            && (int) $SESSION->local_examwizard_bulkadd['id'] === (int) $cohort->id) {
        $ba = $SESSION->local_examwizard_bulkadd;
        unset($SESSION->local_examwizard_bulkadd);
        echo $OUTPUT->notification(
            $s('b_bulk_result', (object) ['added' => $ba['added'], 'already' => $ba['already'],
                'notfound' => count($ba['notfound'])]),
            $ba['notfound'] ? \core\output\notification::NOTIFY_WARNING
                            : \core\output\notification::NOTIFY_SUCCESS);
        if ($ba['notfound']) {
            echo html_writer::start_div('ew-card');
            echo html_writer::tag('h4', $s('b_bulk_notfound_h'));
            echo html_writer::div($s('b_bulk_notfound_hint'), 'small text-muted mb-2');
            echo html_writer::tag('pre', s(implode("\n", $ba['notfound'])),
                ['class' => 'ew-qtext', 'style' => 'white-space:pre-wrap']);
            echo html_writer::end_div();
        }
    }

    // ---- course syncs ----
    echo html_writer::start_div('ew-card');
    echo html_writer::tag('h3', $s('b_syncs_h'), ['class' => 'ew-card-h']);
    $syncs = $batchsyncs($cohort->id);
    if ($syncs) {
        echo html_writer::start_tag('ul', ['class' => 'ew-checklist']);
        foreach ($syncs as $sy) {
            $form = html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false),
                'style' => 'display:inline']);
            $form .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
            $form .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'unsync']);
            $form .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
            $form .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'enrolid', 'value' => $sy->enrolid]);
            $form .= html_writer::tag('button', $s('b_stopsync'),
                ['type' => 'submit', 'class' => 'btn btn-sm btn-outline-secondary ml-2']);
            $form .= html_writer::end_tag('form');
            echo html_writer::tag('li',
                html_writer::link(new moodle_url('/user/index.php', ['id' => $sy->courseid]),
                    format_string($sy->shortname) . ' — ' . format_string($sy->fullname)) . $form);
        }
        echo html_writer::end_tag('ul');
    } else {
        echo html_writer::div($s('b_nosyncs'), 'text-muted mb-2');
    }
    // enrol-into-course mini form
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false), 'class' => 'mt-2']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'enrol']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
    echo html_writer::tag('label', $s('b_enrolinto_label') . ' ', ['for' => 'ew-b-course', 'class' => 'mr-2']);
    echo html_writer::select($coursemenu(), 'courseid', $enrolinto ?: '', ['' => 'choosedots'],
        ['id' => 'ew-b-course', 'class' => 'mr-2']);
    echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => $s('b_enrol_go'),
        'class' => 'btn btn-primary']);
    echo html_writer::end_tag('form');
    echo html_writer::end_div();

    // ---- members ----
    echo html_writer::start_div('ew-card');
    echo html_writer::tag('h3', $s('b_members_h'), ['class' => 'ew-card-h']);
    $members = $DB->get_records_sql(
        "SELECT u.id, u.firstname, u.lastname, u.username, u.email
           FROM {cohort_members} cm
           JOIN {user} u ON u.id = cm.userid
          WHERE cm.cohortid = ? AND u.deleted = 0
       ORDER BY u.lastname, u.firstname", [$cohort->id]);
    if ($members) {
        echo html_writer::start_tag('table', ['class' => 'ew-exams']);
        echo html_writer::tag('thead', html_writer::tag('tr',
            html_writer::tag('th', $s('st_name')) . html_writer::tag('th', $s('st_username')) .
            html_writer::tag('th', get_string('email')) . html_writer::tag('th', '')));
        echo html_writer::start_tag('tbody');
        foreach ($members as $m) {
            $rm = html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false),
                'style' => 'display:inline']);
            $rm .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
            $rm .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'removemember']);
            $rm .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
            $rm .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'userid', 'value' => $m->id]);
            $rm .= html_writer::tag('button', $s('b_remove'),
                ['type' => 'submit', 'class' => 'btn btn-sm btn-outline-secondary']);
            $rm .= html_writer::end_tag('form');
            echo html_writer::tag('tr',
                html_writer::tag('td', s(fullname($m))) .
                html_writer::tag('td', html_writer::tag('code', s($m->username))) .
                html_writer::tag('td', s($m->email)) .
                html_writer::tag('td', $rm, ['class' => 'ew-elinks']));
        }
        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
    } else {
        echo html_writer::div($s('b_nomembers'), 'text-muted mb-2');
    }

    // ---- add members ----
    echo html_writer::tag('h4', $s('b_add_h'), ['class' => 'mt-3']);

    // bulk: paste a list of usernames / roll numbers / emails
    echo html_writer::start_div('ew-card');
    echo html_writer::tag('strong', $s('b_bulk_h'));
    echo html_writer::div($s('b_bulk_hint'), 'small text-muted mb-2');
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false)]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'bulkadd']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
    echo html_writer::tag('textarea', '', ['name' => 'idlist', 'rows' => 6, 'class' => 'form-control',
        'style' => 'max-width:520px', 'placeholder' => "s2026101\ns2026102\ns2026103"]);
    echo html_writer::div(html_writer::empty_tag('input', ['type' => 'submit',
        'value' => $s('b_bulk_go'), 'class' => 'btn btn-primary mt-2']));
    echo html_writer::end_tag('form');
    echo html_writer::end_div();

    // or search for one at a time
    echo html_writer::div($s('b_add_or_search'), 'small text-muted mt-3 mb-1');
    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $baseurl->out(false), 'class' => 'mb-2']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
    echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'q', 'value' => $q,
        'size' => 30, 'placeholder' => $s('cred_search_ph'), 'class' => 'mr-2']);
    echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => $s('cred_search_go'),
        'class' => 'btn btn-outline-secondary']);
    echo html_writer::end_tag('form');

    if (core_text::strlen($q) >= 2) {
        $like = $DB->sql_like($DB->sql_concat('u.firstname', "' '", 'u.lastname', "' '", 'u.username', "' '", 'u.email'),
            ':term', false, false);
        $found = $DB->get_records_sql(
            "SELECT u.id, u.firstname, u.lastname, u.username, u.email
               FROM {user} u
              WHERE u.deleted = 0 AND u.mnethostid = :mnet AND u.id <> :guest AND $like
           ORDER BY u.lastname, u.firstname",
            ['term' => '%' . $DB->sql_like_escape($q) . '%', 'mnet' => $CFG->mnet_localhost_id,
             'guest' => $CFG->siteguest], 0, 50);
        // drop existing members
        $found = array_filter($found, fn($u) => !cohort_is_member($cohort->id, $u->id));
        if ($found) {
            echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false)]);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'addmembers']);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
            echo html_writer::start_tag('table', ['class' => 'ew-exams']);
            echo html_writer::tag('thead', html_writer::tag('tr',
                html_writer::tag('th', html_writer::empty_tag('input', ['type' => 'checkbox', 'id' => 'ew-b-all'])) .
                html_writer::tag('th', $s('st_name')) . html_writer::tag('th', $s('st_username')) .
                html_writer::tag('th', get_string('email'))));
            echo html_writer::start_tag('tbody');
            foreach ($found as $u) {
                echo html_writer::tag('tr',
                    html_writer::tag('td', html_writer::empty_tag('input',
                        ['type' => 'checkbox', 'class' => 'ew-b-cb', 'name' => 'userids[]', 'value' => $u->id])) .
                    html_writer::tag('td', s(fullname($u))) .
                    html_writer::tag('td', html_writer::tag('code', s($u->username))) .
                    html_writer::tag('td', s($u->email)));
            }
            echo html_writer::end_tag('tbody');
            echo html_writer::end_tag('table');
            echo html_writer::div(html_writer::empty_tag('input', ['type' => 'submit',
                'value' => $s('b_add_selected'), 'class' => 'btn btn-primary mt-2']));
            echo html_writer::end_tag('form');
        } else {
            echo $OUTPUT->notification($s('cred_none'), \core\output\notification::NOTIFY_INFO);
        }
    }
    echo html_writer::end_div();

    // ---- delete ----
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false), 'class' => 'mt-2']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'deletebatch']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cohort->id]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'confirm', 'value' => 1]);
    echo html_writer::tag('button', $s('b_delete'),
        ['type' => 'submit', 'class' => 'btn btn-sm btn-link text-danger pl-0',
         'onclick' => "return confirm('" . $s('b_delete_confirm') . "')"]);
    echo html_writer::end_tag('form');

    echo $OUTPUT->footer();
    exit;
}

// =====================================================================
//  LIST VIEW
// =====================================================================
echo $OUTPUT->heading($s('b_title'));
echo html_writer::div($s('b_intro'), 'lead text-muted mb-3');

// enrol-a-batch-into-this-course panel (from the Participants pill)
if ($enrolinto && $enrolinto != SITEID
        && ($ec = $DB->get_record('course', ['id' => $enrolinto]))
        && has_capability('enrol/cohort:config', context_course::instance($enrolinto))) {
    $all = cohort_get_all_cohorts(0, 1000);
    $menu = [];
    foreach ($all['cohorts'] as $c) {
        $menu[$c->id] = format_string($c->name) . ' (' . $membercount($c->id) . ')';
    }
    echo html_writer::start_div('ew-card ew-card-live');
    echo html_writer::tag('h3', $s('b_enrolinto_course', format_string($ec->shortname . ' — ' . $ec->fullname)),
        ['class' => 'ew-card-h']);
    if ($menu) {
        echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false)]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'enrol']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'courseid', 'value' => $enrolinto]);
        echo html_writer::select($menu, 'id', '', ['' => 'choosedots'], ['class' => 'mr-2']);
        echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => $s('b_enrol_go'),
            'class' => 'btn btn-primary']);
        echo html_writer::end_tag('form');
    } else {
        echo html_writer::div($s('b_nobatches_yet'), 'text-muted');
    }
    echo html_writer::end_div();
}

// new batch
echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('b_new_h'), ['class' => 'ew-card-h']);
echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url->out(false)]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'create']);
echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'name', 'size' => 34,
    'placeholder' => $s('b_name_ph'), 'class' => 'mr-2', 'required' => 'required']);
echo html_writer::empty_tag('input', ['type' => 'text', 'name' => 'code', 'size' => 14,
    'placeholder' => $s('b_code_ph'), 'class' => 'mr-2']);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => $s('b_create'), 'class' => 'btn btn-primary']);
echo html_writer::end_tag('form');
echo html_writer::div($s('b_code_hint'), 'small text-muted mt-1');
echo html_writer::end_div();

// list
$all = cohort_get_all_cohorts(0, 1000);
echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('b_all_h'), ['class' => 'ew-card-h']);
if (!$all['cohorts']) {
    echo html_writer::div($s('b_nobatches_yet'), 'text-muted');
} else {
    echo html_writer::start_tag('table', ['class' => 'ew-exams']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('b_name')) . html_writer::tag('th', $s('b_code')) .
        html_writer::tag('th', $s('b_members')) . html_writer::tag('th', $s('b_syncs')) .
        html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($all['cohorts'] as $c) {
        $syncs = $batchsyncs($c->id);
        $syncnames = array_map(fn($x) => format_string($x->shortname), $syncs);
        echo html_writer::tag('tr',
            html_writer::tag('td', html_writer::tag('span', s($c->name), ['class' => 'ew-ename'])) .
            html_writer::tag('td', html_writer::tag('code', s($c->idnumber))) .
            html_writer::tag('td', $membercount($c->id)) .
            html_writer::tag('td', $syncnames ? s(implode(', ', $syncnames))
                : html_writer::tag('span', $s('b_none'), ['class' => 'text-muted'])) .
            html_writer::tag('td',
                html_writer::link(new moodle_url($baseurl, ['id' => $c->id]), $s('b_manage'),
                    ['class' => 'btn btn-sm btn-outline-secondary']),
                ['class' => 'ew-elinks']));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}
echo html_writer::end_div();

echo $OUTPUT->footer();
