<?php
// Shared question-import helpers used by both the standalone uploader and the wizard.

namespace local_examwizard\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Resolve a target question category, read spreadsheets, and drive qformat_xml.
 */
class importer {

    /**
     * Resolve the target category record; create a sub-category when a name is given.
     *
     * @param array $settings ['category' => "catid,ctxid" | catid, 'newcategoryname' => string]
     */
    public static function resolve_category(array $settings, \context $coursecontext,
            \core_question\local\bank\question_edit_contexts $contexts): \stdClass {
        global $DB;

        $catandctx = (string) ($settings['category'] ?? '');
        if (strpos($catandctx, ',') !== false) {
            [$catid] = array_map('intval', explode(',', $catandctx));
        } else {
            $catid = (int) $catandctx;
        }
        if (!$catid || !$DB->record_exists('question_categories', ['id' => $catid])) {
            $defaults = question_get_default_category($coursecontext->id)
                ?: question_make_default_categories([$coursecontext]);
            $catid = $defaults->id;
        }
        $parent = $DB->get_record('question_categories', ['id' => $catid], '*', MUST_EXIST);

        $newname = trim((string) ($settings['newcategoryname'] ?? ''));
        if ($newname === '') {
            $parent->context = \context::instance_by_id($parent->contextid);
            return $parent;
        }

        require_capability('moodle/question:managecategory', \context::instance_by_id($parent->contextid));
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
        $cat->context = \context::instance_by_id($cat->contextid);
        return $cat;
    }

    /**
     * Question ids stored under a category (Moodle 4.x versioned schema).
     *
     * @return int[]
     */
    public static function question_ids(int $categoryid): array {
        global $DB;
        $sql = "SELECT q.id
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = :cat";
        return array_map('intval', array_keys($DB->get_records_sql($sql, ['cat' => $categoryid])));
    }

    /**
     * Read an .xlsx blob into the shape csv_questions::parse_rows() expects.
     */
    public static function parse_xlsx(string $content): array {
        global $CFG;
        $tmp = make_request_directory() . '/upload.xlsx';
        file_put_contents($tmp, $content);
        try {
            require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmp);
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($tmp)->getActiveSheet();
            return csv_questions::parse_rows($sheet->toArray(null, true, false, false));
        } catch (\Throwable $e) {
            return ['fatal' => get_string('err_parsefail', 'local_examwizard', $e->getMessage()),
                'rows' => [], 'valid' => 0, 'errors' => 0];
        }
    }

    /**
     * Parse an uploaded questions blob (csv / xlsx). Returns parse_rows() shape.
     */
    public static function parse_upload(string $content, string $filename): array {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return $ext === 'xlsx' ? self::parse_xlsx($content) : csv_questions::parse_csv($content);
    }

    /**
     * Import Moodle-XML text into $category. Returns [bool ok, int[] newquestionids].
     */
    public static function import_xml(string $xml, \stdClass $category, \stdClass $course,
            \core_question\local\bank\question_edit_contexts $contexts): array {
        global $CFG;
        require_once($CFG->dirroot . '/question/format.php');
        require_once($CFG->dirroot . '/question/format/xml/format.php');

        $before = self::question_ids($category->id);

        $qformat = new \qformat_xml();
        $qformat->setCategory($category);
        $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
        $qformat->setCourse($course);
        $path = make_request_directory() . '/examwizard.xml';
        file_put_contents($path, $xml);
        $qformat->setFilename($path);
        $qformat->setRealfilename('examwizard.xml');
        $qformat->setMatchgrades('nearest');
        $qformat->setCatfromfile(false);
        $qformat->setContextfromfile(false);
        $qformat->setStoponerror(false);

        ob_start();
        $ok = $qformat->importpreprocess() && $qformat->importprocess() && $qformat->importpostprocess();
        ob_end_clean();

        $new = array_values(array_diff(self::question_ids($category->id), $before));
        return [$ok, $new];
    }

    /**
     * Add a batch of question ids to a quiz and refresh its grade totals.
     */
    public static function add_questions_to_quiz(array $questionids, \stdClass $quiz): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        foreach ($questionids as $qid) {
            quiz_add_quiz_question((int) $qid, $quiz);
        }
        quiz_update_sumgrades($quiz);
        // Make the max grade equal the sum of the question marks so totals add up.
        $fresh = $DB->get_record('quiz', ['id' => $quiz->id], '*', MUST_EXIST);
        if ($fresh->sumgrades > 0 && abs((float) $fresh->grade - (float) $fresh->sumgrades) > 0.001) {
            quiz_set_grade((float) $fresh->sumgrades, $fresh);
        }
    }
}
