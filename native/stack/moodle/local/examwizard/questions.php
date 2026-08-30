<?php
// Exam Wizard - bulk question uploader.
//   ?courseid=N                     -> step 1 (upload form)
//   ?courseid=N&template=csv|xlsx   -> download the blank template
//   (form submit)                   -> step 2 (validated preview)
//   (preview confirm)               -> step 3 (import + result)

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->libdir . '/questionlib.php');

use local_examwizard\local\csv_questions;
use local_examwizard\form\upload_form;

$courseid = required_param('courseid', PARAM_INT);
$template = optional_param('template', '', PARAM_ALPHA);

$course = get_course($courseid);
require_login($course);

$coursecontext = context_course::instance($courseid);
require_capability('local/examwizard:use', $coursecontext);
require_capability('moodle/question:add', $coursecontext);

$baseurl = new moodle_url('/local/examwizard/questions.php', ['courseid' => $courseid]);
$PAGE->set_url($baseurl);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('uploadquestions', 'local_examwizard'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'),
    new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('uploadquestions', 'local_examwizard'));

// ---------------------------------------------------------------------
// Template download.
// ---------------------------------------------------------------------
if ($template === 'csv' || $template === 'xlsx') {
    local_examwizard_send_template($template);
    exit;
}

$contexts = new \core_question\local\bank\question_edit_contexts($coursecontext);

// Quizzes in this course (for the optional "also add to quiz").
$quizzes = [];
$modinfo = get_fast_modinfo($course);
foreach ($modinfo->get_instances_of('quiz') as $cm) {
    if ($cm->uservisible) {
        $quizzes[$cm->id] = format_string($cm->name);
    }
}

$mform = new upload_form($baseurl, [
    'courseid' => $courseid,
    'contexts' => $contexts,
    'quizzes'  => $quizzes,
]);

$renderer = $PAGE->get_renderer('core');

// ---------------------------------------------------------------------
// Step 3 - confirmed import.
// ---------------------------------------------------------------------
if (optional_param('doimport', 0, PARAM_BOOL) && confirm_sesskey()) {
    $stash = $SESSION->local_examwizard_preview ?? null;
    if (!$stash || (int) $stash['courseid'] !== $courseid) {
        redirect($baseurl, get_string('err_emptyfile', 'local_examwizard'), null,
            \core\output\notification::NOTIFY_ERROR);
    }
    unset($SESSION->local_examwizard_preview);

    // Re-validate every stashed row (never trust the stash blindly).
    foreach ($stash['rows'] as &$r) {
        csv_questions::validate_row($r);
    }
    unset($r);

    $category = local_examwizard_resolve_category($stash['settings'], $coursecontext, $contexts);
    $xml = csv_questions::build_xml($stash['rows']);

    $before = local_examwizard_question_ids($category->id);

    require_once($CFG->dirroot . '/question/format/xml/format.php');
    $qformat = new qformat_xml();
    $qformat->setCategory($category);
    $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
    $qformat->setCourse($course);
    $xmlpath = make_request_directory() . '/examwizard.xml';
    file_put_contents($xmlpath, $xml);
    $qformat->setFilename($xmlpath);
    $qformat->setRealfilename('examwizard.xml');
    $qformat->setMatchgrades('nearest');
    $qformat->setCatfromfile(false);
    $qformat->setContextfromfile(false);
    $qformat->setStoponerror(false);

    ob_start();
    $ok = $qformat->importpreprocess() && $qformat->importprocess() && $qformat->importpostprocess();
    ob_end_clean();

    $newids = array_values(array_diff(local_examwizard_question_ids($category->id), $before));

    $addedtoquiz = null;
    $quizcmid = (int) ($stash['settings']['addtoquiz'] ?? 0);
    if ($ok && $newids && $quizcmid && isset($quizzes[$quizcmid])) {
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        $quizcm = $modinfo->get_cm($quizcmid);
        $quiz = $DB->get_record('quiz', ['id' => $quizcm->instance], '*', MUST_EXIST);
        foreach ($newids as $qid) {
            quiz_add_quiz_question($qid, $quiz);
        }
        quiz_update_sumgrades($quiz);
        $addedtoquiz = $quizzes[$quizcmid];
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('importdone', 'local_examwizard'));
    if ($ok) {
        echo $OUTPUT->notification(get_string('importsummary', 'local_examwizard', (object) [
            'ok' => count($newids), 'category' => format_string($category->name),
        ]), \core\output\notification::NOTIFY_SUCCESS);
        if ($addedtoquiz !== null) {
            echo $OUTPUT->notification(get_string('importedintoquiz', 'local_examwizard', $addedtoquiz),
                \core\output\notification::NOTIFY_SUCCESS);
        }
    } else {
        echo $OUTPUT->notification(get_string('cannotimport', 'question'),
            \core\output\notification::NOTIFY_ERROR);
    }
    echo html_writer::div(
        html_writer::link(new moodle_url('/question/edit.php', ['courseid' => $courseid, 'cat' =>
            $category->id . ',' . $category->contextid]),
            get_string('gotoquestionbank', 'local_examwizard'), ['class' => 'btn btn-primary mr-2']) .
        html_writer::link($baseurl, get_string('createanother', 'local_examwizard'),
            ['class' => 'btn btn-outline-secondary']),
        'mt-3');
    echo $OUTPUT->footer();
    exit;
}

// ---------------------------------------------------------------------
// Step 2 - parse the uploaded file and show the preview.
// ---------------------------------------------------------------------
if ($data = $mform->get_data()) {
    $filename = $mform->get_new_filename('questionsfile');
    $content  = $mform->get_file_content('questionsfile');
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Power-user formats -> straight to Moodle's own importer, no preview.
    if (in_array($ext, ['xml', 'gift', 'txt'], true)) {
        $fmt = ($ext === 'xml') ? 'xml' : 'gift';
        $category = local_examwizard_resolve_category([
            'category' => $data->category, 'newcategoryname' => $data->newcategoryname ?? '',
        ], $coursecontext, $contexts);

        require_once($CFG->dirroot . '/question/format/' . $fmt . '/format.php');
        $classname = 'qformat_' . $fmt;
        $qformat = new $classname();
        $qformat->setCategory($category);
        $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
        $qformat->setCourse($course);
        $path = make_request_directory() . '/' . clean_param($filename, PARAM_FILE);
        file_put_contents($path, $content);
        $qformat->setFilename($path);
        $qformat->setRealfilename($filename);
        $qformat->setMatchgrades('nearest');
        $qformat->setStoponerror(false);

        ob_start();
        $ok = $qformat->importpreprocess() && $qformat->importprocess() && $qformat->importpostprocess();
        $log = ob_get_clean();

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('importdone', 'local_examwizard'));
        echo $OUTPUT->notification($ok ? get_string('importsummary', 'local_examwizard', (object) [
            'ok' => '', 'category' => format_string($category->name)]) : get_string('cannotimport', 'question'),
            $ok ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR);
        if (trim(strip_tags($log)) !== '') {
            echo html_writer::div($log, 'card card-body bg-light small');
        }
        echo html_writer::div(html_writer::link($baseurl, get_string('createanother', 'local_examwizard'),
            ['class' => 'btn btn-primary']), 'mt-3');
        echo $OUTPUT->footer();
        exit;
    }

    // CSV / XLSX -> our friendly parser + preview.
    if ($ext === 'xlsx') {
        $parsed = local_examwizard_parse_xlsx($content);
    } else {
        $parsed = csv_questions::parse_csv($content);
    }

    if (!empty($parsed['fatal'])) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('uploadquestions', 'local_examwizard'));
        echo $OUTPUT->notification($parsed['fatal'], \core\output\notification::NOTIFY_ERROR);
        $mform->display();
        echo $OUTPUT->footer();
        exit;
    }

    $SESSION->local_examwizard_preview = [
        'courseid' => $courseid,
        'rows' => $parsed['rows'],
        'settings' => [
            'category' => $data->category,
            'newcategoryname' => $data->newcategoryname ?? '',
            'addtoquiz' => (int) ($data->addtoquiz ?? 0),
        ],
    ];

    echo $OUTPUT->header();
    echo local_examwizard_render_preview($parsed, $baseurl, $quizzes[$data->addtoquiz ?? 0] ?? null);
    echo $OUTPUT->footer();
    exit;
}

// ---------------------------------------------------------------------
// Step 1 - the upload form.
// ---------------------------------------------------------------------
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('uploadquestions', 'local_examwizard'));
echo html_writer::div(get_string('uploadquestions_desc', 'local_examwizard'), 'lead text-muted mb-3');
echo local_examwizard_stepper(1);
$mform->display();
echo $OUTPUT->footer();


// =====================================================================
//  Helpers (kept in-file to keep the plugin small for Phase 1).
// =====================================================================

/**
 * Resolve the target category record, creating a sub-category if a name was given.
 */
function local_examwizard_resolve_category(array $settings, context $coursecontext,
        \core_question\local\bank\question_edit_contexts $contexts): stdClass {
    global $DB, $USER;

    $catandctx = (string) ($settings['category'] ?? '');
    if (strpos($catandctx, ',') !== false) {
        [$catid, $ctxid] = array_map('intval', explode(',', $catandctx));
    } else {
        $catid = (int) $catandctx;
        $ctxid = 0;
    }
    if (!$catid || !$DB->record_exists('question_categories', ['id' => $catid])) {
        // Fall back to the course's default category.
        $defaults = question_get_default_category($coursecontext->id)
            ?: question_make_default_categories([$coursecontext]);
        $catid = $defaults->id;
        $ctxid = $defaults->contextid;
    }
    $parent = $DB->get_record('question_categories', ['id' => $catid], '*', MUST_EXIST);

    $newname = trim((string) ($settings['newcategoryname'] ?? ''));
    if ($newname === '') {
        $parent->context = context::instance_by_id($parent->contextid);
        return $parent;
    }

    require_capability('moodle/question:managecategory', context::instance_by_id($parent->contextid));
    $cat = (object) [
        'parent' => $parent->id,
        'contextid' => $parent->contextid,
        'name' => \core_text::substr($newname, 0, 255),
        'info' => '',
        'infoformat' => FORMAT_HTML,
        'sortorder' => 999,
        'stamp' => make_unique_id_code(),
        'idnumber' => null,
    ];
    $cat->id = $DB->insert_record('question_categories', $cat);
    \core\event\question_category_created::create_from_question_category_instance($cat)->trigger();
    $cat->context = context::instance_by_id($cat->contextid);
    return $cat;
}

/**
 * Current question ids stored under a category (Moodle 4.x versioned schema).
 *
 * @return int[]
 */
function local_examwizard_question_ids(int $categoryid): array {
    global $DB;
    $sql = "SELECT q.id
              FROM {question} q
              JOIN {question_versions} qv ON qv.questionid = q.id
              JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
             WHERE qbe.questioncategoryid = :cat";
    return array_map('intval', array_keys($DB->get_records_sql($sql, ['cat' => $categoryid])));
}

/**
 * Read an .xlsx blob into the same shape csv_questions::parse_rows() expects.
 */
function local_examwizard_parse_xlsx(string $content): array {
    global $CFG;
    $tmp = make_request_directory() . '/upload.xlsx';
    file_put_contents($tmp, $content);
    try {
        require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmp);
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($tmp)->getActiveSheet();
        $rows = $sheet->toArray(null, true, false, false);
        return csv_questions::parse_rows($rows);
    } catch (\Throwable $e) {
        return ['fatal' => get_string('err_parsefail', 'local_examwizard', $e->getMessage()),
            'rows' => [], 'valid' => 0, 'errors' => 0];
    }
}

/**
 * Stream the blank template (csv always; xlsx when PhpSpreadsheet is available).
 */
function local_examwizard_send_template(string $type): void {
    global $CFG;
    $rows = csv_questions::template_rows();

    if ($type === 'xlsx') {
        try {
            require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');
            $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $ss->getActiveSheet();
            $sheet->fromArray($rows, null, 'A1');
            $sheet->getStyle('A1:K1')->getFont()->setBold(true);
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="examwizard-questions-template.xlsx"');
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss))->save('php://output');
            return;
        } catch (\Throwable $e) {
            // Fall through to CSV.
        }
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="examwizard-questions-template.csv"');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
}

/**
 * The 3-dot progress indicator.
 */
function local_examwizard_stepper(int $active): string {
    $steps = [1 => get_string('step1', 'local_examwizard'), 2 => get_string('step2', 'local_examwizard'),
        3 => get_string('step3', 'local_examwizard')];
    $items = '';
    foreach ($steps as $i => $label) {
        $state = $i < $active ? 'done' : ($i === $active ? 'current' : 'todo');
        $items .= html_writer::tag('li', html_writer::tag('span', $i, ['class' => 'ew-step-dot']) .
            html_writer::tag('span', $label, ['class' => 'ew-step-label']), ['class' => 'ew-step ew-step-' . $state]);
    }
    return html_writer::tag('ol', $items, ['class' => 'ew-stepper']);
}

/**
 * Render the validated preview (question cards) + the confirm form.
 */
function local_examwizard_render_preview(array $parsed, moodle_url $baseurl, ?string $quizname): string {
    global $OUTPUT;

    $o  = $OUTPUT->heading(get_string('review', 'local_examwizard'));
    $o .= local_examwizard_stepper(2);
    $o .= html_writer::div(get_string('reviewintro', 'local_examwizard'), 'text-muted mb-3');

    $o .= html_writer::start_div('ew-review-summary');
    $o .= html_writer::tag('span', get_string('rowsok', 'local_examwizard', $parsed['valid']),
        ['class' => 'ew-pill ew-pill-ok']);
    if ($parsed['errors']) {
        $o .= html_writer::tag('span', get_string('rowserrors', 'local_examwizard', $parsed['errors']),
            ['class' => 'ew-pill ew-pill-err']);
    }
    $o .= html_writer::end_div();

    $typelabels = [
        'mcq' => get_string('type_mcq', 'local_examwizard'),
        'multi' => get_string('type_multi', 'local_examwizard'),
        'truefalse' => get_string('type_truefalse', 'local_examwizard'),
        'short' => get_string('type_short', 'local_examwizard'),
    ];

    $o .= html_writer::start_div('ew-cards');
    foreach ($parsed['rows'] as $row) {
        $bad = !empty($row['errors']);
        $o .= html_writer::start_div('ew-qcard' . ($bad ? ' ew-qcard-bad' : ''));

        $o .= html_writer::start_div('ew-qcard-head');
        $o .= html_writer::tag('span', '#' . $row['n'], ['class' => 'ew-qnum']);
        $o .= html_writer::tag('span', $typelabels[$row['type']] ?? s($row['typeraw']), ['class' => 'ew-qtype']);
        $o .= html_writer::tag('span', format_float($row['marks'], -1) . ' mark' . ($row['marks'] == 1 ? '' : 's'),
            ['class' => 'ew-qmarks']);
        $o .= html_writer::end_div();

        $o .= html_writer::div(s($row['questiontext']) ?: '<em class="text-muted">(empty)</em>', 'ew-qtext');

        if ($row['options']) {
            $correct = preg_split('~[\s,;/|]+~', strtoupper($row['answer']), -1, PREG_SPLIT_NO_EMPTY);
            $o .= html_writer::start_tag('ul', ['class' => 'ew-qopts']);
            foreach ($row['options'] as $letter => $text) {
                $is = in_array($letter, $correct, true);
                $o .= html_writer::tag('li',
                    html_writer::tag('b', $letter) . ' ' . s($text),
                    ['class' => $is ? 'ew-opt-correct' : '']);
            }
            $o .= html_writer::end_tag('ul');
        } else if ($row['answer'] !== '') {
            $o .= html_writer::div('<b>Answer:</b> ' . s($row['answer']), 'ew-qans');
        }

        if ($bad) {
            $o .= html_writer::alist(array_map('s', $row['errors']), ['class' => 'ew-qerrors']);
        }
        $o .= html_writer::end_div();
    }
    $o .= html_writer::end_div();

    // Confirm form.
    $confirmurl = new moodle_url($baseurl, ['doimport' => 1, 'sesskey' => sesskey()]);
    $btn = $parsed['valid'] > 0
        ? html_writer::link($confirmurl, get_string('confirmimport', 'local_examwizard', $parsed['valid']),
            ['class' => 'btn btn-primary btn-lg'])
        : html_writer::tag('span', get_string('confirmimport', 'local_examwizard', 0),
            ['class' => 'btn btn-primary btn-lg disabled', 'aria-disabled' => 'true']);
    $o .= html_writer::div(
        $btn . ' ' . html_writer::link($baseurl, get_string('backtoupload', 'local_examwizard'),
            ['class' => 'btn btn-outline-secondary btn-lg']),
        'ew-review-actions');
    if ($quizname) {
        $o .= html_writer::div(get_string('alsoaddtoquiz', 'local_examwizard') . ': <b>' . s($quizname) . '</b>',
            'text-muted small mt-2');
    }
    return $o;
}
