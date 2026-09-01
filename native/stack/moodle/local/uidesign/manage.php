<?php
// local_uidesign - the rule manager. Deliberately plain: never touched by any
// override, so an admin can always recover the site from here.

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/local/rules.php');

use local_uidesign\local\rules;

admin_externalpage_setup('local_uidesign_manage');
$context = context_system::instance();
require_capability('local/uidesign:manage', $context);

$baseurl = new moodle_url('/local/uidesign/manage.php');
$s = fn($id, $a = null) => get_string($id, 'local_uidesign', $a);

// ---- downloads ----
if (optional_param('export', 0, PARAM_BOOL) && confirm_sesskey()) {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="uidesign-rules-' . date('Ymd-His') . '.json"');
    echo rules::export_json();
    exit;
}

// ---- actions ----
if ($data = data_submitted()) {
    require_sesskey();
    $do = optional_param('do', '', PARAM_ALPHA);

    if ($do === 'toggle') {
        rules::set_enabled(required_param('id', PARAM_INT), (bool) optional_param('enabled', 0, PARAM_BOOL));
        redirect($baseurl);
    }
    if ($do === 'delete') {
        rules::delete(required_param('id', PARAM_INT));
        redirect($baseurl, $s('deleted'));
    }
    if ($do === 'resetall') {
        rules::reset_all();
        redirect($baseurl, $s('reset_done'));
    }
    if ($do === 'publish') {
        rules::publish();
        redirect($baseurl, $s('published'));
    }
    if ($do === 'discard') {
        rules::discard_drafts();
        redirect($baseurl, $s('discarded'));
    }
    if ($do === 'rollback') {
        rules::rollback(required_param('id', PARAM_INT));
        redirect($baseurl, $s('restored'));
    }
    if ($do === 'import') {
        $json = optional_param('json', '', PARAM_RAW);
        try {
            $n = rules::import_json($json);
            redirect($baseurl, $s('import_done', $n));
        } catch (\Throwable $e) {
            redirect($baseurl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
    redirect($baseurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($s('managetitle'));
echo html_writer::div($s('manageintro'), 'lead text-muted mb-3');

// ---- draft / publish bar ----
$pending = rules::pending_count();
if ($pending > 0) {
    $pf = html_writer::start_tag('form', ['method' => 'post', 'style' => 'display:inline']);
    $pf .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    $pf .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'publish']);
    $pf .= html_writer::tag('button', $s('publish'), ['type' => 'submit', 'class' => 'btn btn-primary btn-sm']);
    $pf .= html_writer::end_tag('form');
    $df = html_writer::start_tag('form', ['method' => 'post', 'style' => 'display:inline;margin-left:.4rem']);
    $df .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    $df .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'discard']);
    $df .= html_writer::tag('button', $s('discard'), ['type' => 'submit', 'class' => 'btn btn-outline-secondary btn-sm',
        'onclick' => "return confirm('" . $s('discard_confirm') . "')"]);
    $df .= html_writer::end_tag('form');
    echo $OUTPUT->notification($s('pending', $pending) . ' ' . $pf . $df, \core\output\notification::NOTIFY_WARNING);
}

$rules = rules::all();

if (!$rules) {
    echo $OUTPUT->notification($s('norules'), \core\output\notification::NOTIFY_INFO);
} else {
    echo html_writer::start_tag('table', ['class' => 'generaltable']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('col_kind')) . html_writer::tag('th', $s('col_target')) .
        html_writer::tag('th', $s('col_where')) . html_writer::tag('th', $s('col_value')) .
        html_writer::tag('th', $s('col_on')) . html_writer::tag('th', $s('col_actions'))));
    echo html_writer::start_tag('tbody');
    foreach ($rules as $r) {
        $target = $r->kind === 'element' ? ($r->property . ' — ' . $r->selector)
            : ($r->kind === 'token' ? $r->selector : $r->selector);

        $toggle = html_writer::start_tag('form', ['method' => 'post', 'style' => 'display:inline']);
        $toggle .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        $toggle .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'toggle']);
        $toggle .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $r->id]);
        $toggle .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'enabled', 'value' => $r->enabled ? 0 : 1]);
        $toggle .= html_writer::tag('button', $r->enabled ? '✓' : '—',
            ['type' => 'submit', 'class' => 'btn btn-sm ' . ($r->enabled ? 'btn-success' : 'btn-outline-secondary')]);
        $toggle .= html_writer::end_tag('form');

        $del = html_writer::start_tag('form', ['method' => 'post', 'style' => 'display:inline']);
        $del .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        $del .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'delete']);
        $del .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $r->id]);
        $del .= html_writer::tag('button', get_string('delete'),
            ['type' => 'submit', 'class' => 'btn btn-sm btn-outline-danger']);
        $del .= html_writer::end_tag('form');

        $kindcell = html_writer::tag('code', s($r->kind)) .
            (empty($r->published) ? ' ' . html_writer::tag('span', $s('draftbadge'),
                ['class' => 'badge badge-warning']) : '');

        echo html_writer::tag('tr',
            html_writer::tag('td', $kindcell) .
            html_writer::tag('td', html_writer::tag('code', s($target))) .
            html_writer::tag('td', s($r->pagetype === '*' ? 'everywhere' : $r->pagetype)) .
            html_writer::tag('td', s((string) $r->value)) .
            html_writer::tag('td', $toggle) .
            html_writer::tag('td', $del));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}

// ---- published history ----
$versions = rules::versions();
if ($versions) {
    echo html_writer::tag('h3', $s('history'), ['class' => 'mt-4']);
    echo html_writer::start_tag('table', ['class' => 'generaltable']);
    echo html_writer::tag('thead', html_writer::tag('tr',
        html_writer::tag('th', $s('col_when')) . html_writer::tag('th', $s('col_note')) .
        html_writer::tag('th', $s('col_rules')) . html_writer::tag('th', '')));
    echo html_writer::start_tag('tbody');
    foreach ($versions as $v) {
        $rb = html_writer::start_tag('form', ['method' => 'post', 'style' => 'display:inline']);
        $rb .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        $rb .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'rollback']);
        $rb .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $v->id]);
        $rb .= html_writer::tag('button', $s('restore_btn'),
            ['type' => 'submit', 'class' => 'btn btn-sm btn-outline-secondary',
             'onclick' => "return confirm('" . $s('restore_confirm') . "')"]);
        $rb .= html_writer::end_tag('form');
        echo html_writer::tag('tr',
            html_writer::tag('td', userdate($v->timecreated, get_string('strftimedatetimeshort'))) .
            html_writer::tag('td', s($v->note)) .
            html_writer::tag('td', (int) $v->rulecount) .
            html_writer::tag('td', $rb));
    }
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}

// ---- footer actions ----
echo html_writer::start_div('mt-3', ['style' => 'display:flex;gap:.6rem;flex-wrap:wrap;align-items:flex-start']);

echo html_writer::link(new moodle_url($baseurl, ['export' => 1, 'sesskey' => sesskey()]),
    $s('export'), ['class' => 'btn btn-outline-secondary']);

$resetform = html_writer::start_tag('form', ['method' => 'post']);
$resetform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
$resetform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'resetall']);
$resetform .= html_writer::tag('button', $s('resetall'),
    ['type' => 'submit', 'class' => 'btn btn-outline-danger',
     'onclick' => "return confirm('" . $s('resetall_confirm') . "')"]);
$resetform .= html_writer::end_tag('form');
echo $resetform;

echo html_writer::end_div();

// import
echo html_writer::start_tag('form', ['method' => 'post', 'class' => 'mt-3']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'do', 'value' => 'import']);
echo html_writer::tag('label', $s('import'), ['for' => 'uid-import', 'class' => 'font-weight-bold d-block']);
echo html_writer::tag('textarea', '', ['name' => 'json', 'id' => 'uid-import', 'rows' => 5,
    'class' => 'form-control', 'style' => 'max-width:640px', 'placeholder' => '{"plugin":"local_uidesign",...}']);
echo html_writer::div(html_writer::empty_tag('input', ['type' => 'submit',
    'value' => $s('import'), 'class' => 'btn btn-primary mt-2']));
echo html_writer::end_tag('form');

echo $OUTPUT->footer();
