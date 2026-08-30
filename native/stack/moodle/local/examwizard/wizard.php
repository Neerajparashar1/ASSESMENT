<?php
// Exam Wizard - 4-step Create Exam flow.
//   1 Basics -> 2 Questions -> 3 Rules -> 4 Review & Publish
// State is kept in $SESSION->local_examwizard_wizard for the current course.

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->libdir . '/questionlib.php');

use local_examwizard\local\csv_questions;
use local_examwizard\local\importer;
use local_examwizard\local\quiz_builder;

$courseid = required_param('courseid', PARAM_INT);
$goback   = optional_param('back', 0, PARAM_BOOL);
$reset    = optional_param('reset', 0, PARAM_BOOL);

$course = get_course($courseid);
require_login($course);

$coursecontext = context_course::instance($courseid);
require_capability('local/examwizard:use', $coursecontext);
require_capability('moodle/question:add', $coursecontext);
require_capability('mod/quiz:addinstance', $coursecontext);

$baseurl = new moodle_url('/local/examwizard/wizard.php', ['courseid' => $courseid]);
$PAGE->set_url($baseurl);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('w_title', 'local_examwizard'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'),
    new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('w_title', 'local_examwizard'));

$contexts = new \core_question\local\bank\question_edit_contexts($coursecontext);

// ---- session state ----
if ($reset || empty($SESSION->local_examwizard_wizard['courseid'])
        || $SESSION->local_examwizard_wizard['courseid'] != $courseid) {
    $SESSION->local_examwizard_wizard = ['courseid' => $courseid, 'step' => 1];
}
$state = &$SESSION->local_examwizard_wizard;
$step = (int) ($state['step'] ?? 1);

if ($goback && $step > 1) {
    $state['step'] = --$step;
    redirect($baseurl);
}

// Course sections for the "where" picker.
$sections = [];
$modinfo = get_fast_modinfo($course);
foreach ($modinfo->get_section_info_all() as $s) {
    $sections[$s->section] = $s->section == 0
        ? get_string('w_generalsection', 'local_examwizard')
        : (get_section_name($course, $s) ?: get_string('sectionname', 'format_' . $course->format) . ' ' . $s->section);
}

// =====================================================================
//  STEP 4 - review & publish
// =====================================================================
if ($step === 4) {
    if (optional_param('publish', 0, PARAM_BOOL) && confirm_sesskey()) {
        $basics = $state['basics'];
        $rules = $state['rules'];

        [$cmid, $quiz, $cm] = quiz_builder::create($course, $basics, $rules);

        $qsummary = '';
        $qsrc = $state['questions']['source'] ?? 'later';
        if ($qsrc === 'upload' && !empty($state['questions']['rows'])) {
            $cat = importer::resolve_category(
                ['newcategoryname' => \core_text::substr($basics['name'], 0, 200) . ' – questions'],
                $coursecontext, $contexts);
            $xml = csv_questions::build_xml($state['questions']['rows'], !empty($rules['negative']));
            [$ok, $newids] = importer::import_xml($xml, $cat, $course, $contexts);
            if ($ok && $newids) {
                importer::add_questions_to_quiz($newids, $quiz);
            }
            $qsummary = get_string('w_pub_added', 'local_examwizard', count($newids));
        } else if ($qsrc === 'category' && !empty($state['questions']['category'])) {
            [$catid] = array_map('intval', explode(',', $state['questions']['category']));
            $ids = importer::question_ids($catid);
            if ($ids) {
                importer::add_questions_to_quiz($ids, $quiz);
            }
            $qsummary = get_string('w_pub_added', 'local_examwizard', count($ids));
        } else {
            $qsummary = get_string('w_pub_later', 'local_examwizard');
        }

        unset($SESSION->local_examwizard_wizard);

        $viewurl = new moodle_url('/mod/quiz/view.php', ['id' => $cmid]);
        $editurl = new moodle_url('/mod/quiz/edit.php', ['cmid' => $cmid]);

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('w_published', 'local_examwizard'));
        echo $OUTPUT->notification(get_string('w_published_msg', 'local_examwizard',
            (object) ['name' => format_string($quiz->name), 'questions' => $qsummary]),
            \core\output\notification::NOTIFY_SUCCESS);
        $wires = [];
        if (!empty($rules['seb'])) {
            $wires[] = get_string('w_wired_seb', 'local_examwizard');
        }
        if (!empty($rules['proctoring'])) {
            $wires[] = get_string('w_wired_proctoring', 'local_examwizard');
        }
        if ($wires) {
            echo html_writer::alist($wires, ['class' => 'ew-wires']);
        }
        echo html_writer::div(
            html_writer::link($viewurl, get_string('w_openquiz', 'local_examwizard'),
                ['class' => 'btn btn-primary mr-2']) .
            html_writer::link($editurl, get_string('w_editquestions', 'local_examwizard'),
                ['class' => 'btn btn-outline-secondary mr-2']) .
            html_writer::link(new moodle_url($baseurl, ['reset' => 1]),
                get_string('w_another', 'local_examwizard'), ['class' => 'btn btn-outline-secondary']),
            'mt-3');
        echo $OUTPUT->footer();
        exit;
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('w_title', 'local_examwizard'));
    echo local_examwizard_wizard_stepper(4);
    echo local_examwizard_wizard_review($state, $sections, $baseurl);
    echo $OUTPUT->footer();
    exit;
}

// =====================================================================
//  STEPS 1-3 - forms
// =====================================================================
$formmap = [
    1 => [\local_examwizard\form\wizard_basics_form::class, ['sections' => $sections]],
    2 => [\local_examwizard\form\wizard_questions_form::class, ['contexts' => $contexts, 'courseid' => $courseid]],
    3 => [\local_examwizard\form\wizard_rules_form::class, []],
];
[$formclass, $custom] = $formmap[$step];
$mform = new $formclass($baseurl, $custom);

// Prefill from saved state.
$saved = [1 => 'basics', 2 => 'questions', 3 => 'rules'][$step];
if (!empty($state[$saved])) {
    $mform->set_data($state[$saved]);
}

if ($mform->is_cancelled()) {
    unset($SESSION->local_examwizard_wizard);
    redirect(new moodle_url('/local/examwizard/index.php'));
}

if ($data = $mform->get_data()) {
    $data = (array) $data;

    if ($step === 1) {
        $ed = $data['introeditor'] ?? ['text' => '', 'format' => FORMAT_HTML];
        $state['basics'] = [
            'name' => trim($data['name']),
            'section' => (int) $data['section'],
            'timeopen' => (int) ($data['timeopen'] ?? 0),
            'timeclose' => (int) ($data['timeclose'] ?? 0),
            'timelimit' => max(0, (int) $data['timelimit']),
            'intro' => is_array($ed) ? ($ed['text'] ?? '') : (string) $ed,
        ];
        $state['step'] = 2;
        redirect($baseurl);
    }

    if ($step === 2) {
        $src = $data['source'] ?? 'later';
        $slice = ['source' => $src];
        if ($src === 'upload') {
            $filename = $mform->get_new_filename('questionsfile');
            $content = $mform->get_file_content('questionsfile');
            if ($content === false) {
                $slice['error'] = get_string('required');
            } else {
                $parsed = importer::parse_upload($content, $filename);
                if (!empty($parsed['fatal'])) {
                    $slice['error'] = $parsed['fatal'];
                } else {
                    $slice['rows'] = $parsed['rows'];
                    $slice['valid'] = $parsed['valid'];
                    $slice['errors'] = $parsed['errors'];
                }
            }
        } else if ($src === 'category') {
            $slice['category'] = $data['category'] ?? '';
        }

        if (!empty($slice['error'])) {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('w_title', 'local_examwizard'));
            echo local_examwizard_wizard_stepper(2);
            echo $OUTPUT->notification($slice['error'], \core\output\notification::NOTIFY_ERROR);
            $mform->display();
            echo $OUTPUT->footer();
            exit;
        }

        $state['questions'] = $slice;
        $state['step'] = 3;
        redirect($baseurl);
    }

    if ($step === 3) {
        $state['rules'] = [
            'attempts' => (int) $data['attempts'],
            'review' => $data['review'] === 'immediately' ? 'immediately' : 'afterclose',
            'shuffle' => !empty($data['shuffle']) ? 1 : 0,
            'negative' => !empty($data['negative']) ? 1 : 0,
            'seb' => !empty($data['seb']) ? 1 : 0,
            'proctoring' => !empty($data['proctoring']) ? 1 : 0,
        ];
        $state['step'] = 4;
        redirect($baseurl);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('w_title', 'local_examwizard'));
echo local_examwizard_wizard_stepper($step);
if ($step > 1) {
    echo html_writer::div(html_writer::link(new moodle_url($baseurl, ['back' => 1]),
        '← ' . get_string('w_back', 'local_examwizard'), ['class' => 'btn btn-link pl-0']), 'mb-2');
}
$mform->display();
echo $OUTPUT->footer();


// =====================================================================
//  Presentation helpers.
// =====================================================================

function local_examwizard_wizard_stepper(int $active): string {
    $steps = [
        1 => get_string('w_step_basics', 'local_examwizard'),
        2 => get_string('w_step_questions', 'local_examwizard'),
        3 => get_string('w_step_rules', 'local_examwizard'),
        4 => get_string('w_step_review', 'local_examwizard'),
    ];
    $items = '';
    foreach ($steps as $i => $label) {
        $state = $i < $active ? 'done' : ($i === $active ? 'current' : 'todo');
        $items .= html_writer::tag('li',
            html_writer::tag('span', $i < $active ? '✓' : $i, ['class' => 'ew-step-dot']) .
            html_writer::tag('span', $label, ['class' => 'ew-step-label']),
            ['class' => 'ew-step ew-step-' . $state]);
    }
    return html_writer::tag('ol', $items, ['class' => 'ew-stepper']);
}

function local_examwizard_wizard_review(array $state, array $sections, moodle_url $baseurl): string {
    $b = $state['basics'] ?? [];
    $q = $state['questions'] ?? [];
    $r = $state['rules'] ?? [];
    $s = fn($id, $a = null) => get_string($id, 'local_examwizard', $a);

    $when = '';
    if (!empty($b['timeopen'])) {
        $when .= $s('w_rev_opens') . ' ' . userdate($b['timeopen']) . '<br>';
    }
    if (!empty($b['timeclose'])) {
        $when .= $s('w_rev_closes') . ' ' . userdate($b['timeclose']);
    }
    if ($when === '') {
        $when = $s('w_rev_anytime');
    }

    $qtext = match ($q['source'] ?? 'later') {
        'upload' => $s('w_rev_q_upload', (object) ['valid' => $q['valid'] ?? 0, 'errors' => $q['errors'] ?? 0]),
        'category' => $s('w_rev_q_category'),
        default => $s('w_rev_q_later'),
    };

    $rows = [
        [$s('w_examname'), s($b['name'] ?? '')],
        [$s('w_section'), s($sections[$b['section'] ?? 0] ?? '')],
        [$s('w_rev_when'), $when],
        [$s('w_timelimit'), ($b['timelimit'] ?? 0) ? ($b['timelimit'] . ' ' . $s('w_minutes')) : $s('w_none')],
        [$s('w_step_questions'), $qtext],
        [$s('w_attempts'), ($r['attempts'] ?? 1) ?: get_string('unlimited')],
        [$s('w_review'), ($r['review'] ?? '') === 'immediately' ? $s('w_review_immediately') : $s('w_review_afterclose')],
        [$s('w_shuffle'), empty($r['shuffle']) ? $s('w_no') : $s('w_yes')],
        [$s('w_negative'), empty($r['negative']) ? $s('w_no') : $s('w_yes')],
        [$s('w_seb'), empty($r['seb']) ? $s('w_no') : $s('w_yes')],
        [$s('w_proctoring'), empty($r['proctoring']) ? $s('w_no') : $s('w_yes')],
    ];

    $o = html_writer::start_div('ew-review-table');
    foreach ($rows as [$k, $v]) {
        $o .= html_writer::div(
            html_writer::span($k, 'ew-rk') . html_writer::span($v, 'ew-rv'), 'ew-rrow');
    }
    $o .= html_writer::end_div();

    $publishurl = new moodle_url($baseurl, ['publish' => 1, 'sesskey' => sesskey()]);
    $o .= html_writer::div(
        html_writer::link($publishurl, get_string('w_publish', 'local_examwizard'),
            ['class' => 'btn btn-primary btn-lg']) . ' ' .
        html_writer::link(new moodle_url($baseurl, ['back' => 1]), get_string('w_back', 'local_examwizard'),
            ['class' => 'btn btn-outline-secondary btn-lg']),
        'ew-review-actions mt-3');
    return $o;
}
