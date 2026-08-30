<?php
// Parse a friendly spreadsheet of questions and turn it into Moodle question XML.

namespace local_examwizard\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Reads the Exam Wizard question template (CSV / XLSX rows) and:
 *  - normalises every row into a typed question array,
 *  - validates it (errors are attached, never thrown),
 *  - builds Moodle XML for the valid rows so qformat_xml can import them.
 */
class csv_questions {

    /** @var string[] canonical column keys we look for in the header row. */
    const COLUMNS = ['type', 'question', 'a', 'b', 'c', 'd', 'e', 'f', 'answer', 'marks', 'feedback'];

    /** Multi-answer: number of correct options => a Moodle-legal positive fraction. */
    const MULTI_FRACTIONS = [1 => '100', 2 => '50', 3 => '33.33333', 4 => '25', 5 => '20', 6 => '16.66667'];

    /**
     * Parse a 2D array of cell values (row 0 = headers).
     *
     * @param array $rows array of arrays of scalar cell values
     * @return array{rows: array, valid: int, errors: int}
     */
    public static function parse_rows(array $rows): array {
        // Drop fully-empty trailing rows.
        $rows = array_values(array_filter($rows, static function ($r) {
            foreach ((array) $r as $c) {
                if (trim((string) $c) !== '') {
                    return true;
                }
            }
            return false;
        }));

        if (count($rows) < 1) {
            return ['fatal' => get_string('err_emptyfile', 'local_examwizard'), 'rows' => [], 'valid' => 0, 'errors' => 0];
        }

        $map = self::map_header(array_shift($rows));
        if ($map === null || !isset($map['type'], $map['question'], $map['answer'])) {
            return ['fatal' => get_string('err_noheader', 'local_examwizard'), 'rows' => [], 'valid' => 0, 'errors' => 0];
        }
        if (count($rows) < 1) {
            return ['fatal' => get_string('err_emptyfile', 'local_examwizard'), 'rows' => [], 'valid' => 0, 'errors' => 0];
        }

        $out = [];
        $valid = 0;
        $errors = 0;
        $n = 0;
        foreach ($rows as $raw) {
            $n++;
            $row = self::build_row($n, $raw, $map);
            self::validate_row($row);
            if (empty($row['errors'])) {
                $valid++;
            } else {
                $errors++;
            }
            $out[] = $row;
        }

        return ['fatal' => null, 'rows' => $out, 'valid' => $valid, 'errors' => $errors];
    }

    /**
     * Parse raw CSV text.
     *
     * @param string $text
     * @return array see {@see parse_rows()}
     */
    public static function parse_csv(string $text): array {
        $text = preg_replace('~^\xEF\xBB\xBF~', '', $text);              // strip UTF-8 BOM
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);
        $rows = [];
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            $rows[] = str_getcsv($line);
        }
        return self::parse_rows($rows);
    }

    /**
     * Map the header row: canonical key => column index. Case / space / synonym tolerant.
     *
     * @param array $header
     * @return array<string,int>|null
     */
    private static function map_header(array $header): ?array {
        $synonyms = [
            'type' => 'type', 'qtype' => 'type', 'kind' => 'type',
            'question' => 'question', 'text' => 'question', 'questiontext' => 'question', 'q' => 'question',
            'a' => 'a', 'optiona' => 'a', 'option a' => 'a', 'opt a' => 'a', '1' => 'a',
            'b' => 'b', 'optionb' => 'b', 'option b' => 'b', 'opt b' => 'b', '2' => 'b',
            'c' => 'c', 'optionc' => 'c', 'option c' => 'c', 'opt c' => 'c', '3' => 'c',
            'd' => 'd', 'optiond' => 'd', 'option d' => 'd', 'opt d' => 'd', '4' => 'd',
            'e' => 'e', 'optione' => 'e', 'option e' => 'e', 'opt e' => 'e', '5' => 'e',
            'f' => 'f', 'optionf' => 'f', 'option f' => 'f', 'opt f' => 'f', '6' => 'f',
            'answer' => 'answer', 'correct' => 'answer', 'key' => 'answer', 'ans' => 'answer',
            'marks' => 'marks', 'mark' => 'marks', 'points' => 'marks', 'score' => 'marks', 'grade' => 'marks',
            'feedback' => 'feedback', 'explanation' => 'feedback', 'solution' => 'feedback',
        ];
        $map = [];
        foreach ($header as $i => $label) {
            $k = strtolower(trim((string) $label));
            $k = preg_replace('~\s+~', ' ', $k);
            if (isset($synonyms[$k]) && !isset($map[$synonyms[$k]])) {
                $map[$synonyms[$k]] = $i;
            }
        }
        return $map ?: null;
    }

    /**
     * Turn one spreadsheet row into a normalised question array.
     */
    private static function build_row(int $n, array $raw, array $map): array {
        $get = static function (string $key) use ($raw, $map): string {
            if (!isset($map[$key]) || !array_key_exists($map[$key], $raw)) {
                return '';
            }
            return trim((string) $raw[$map[$key]]);
        };

        $typeraw = strtolower($get('type'));
        $typemap = [
            'mcq' => 'mcq', 'single' => 'mcq', 'multichoice' => 'mcq', 'multiplechoice' => 'mcq',
            'choice' => 'mcq', 'radio' => 'mcq', 'mc' => 'mcq',
            'multi' => 'multi', 'multiple' => 'multi', 'checkbox' => 'multi', 'multianswer' => 'multi',
            'truefalse' => 'truefalse', 'tf' => 'truefalse', 'true/false' => 'truefalse',
            'true-false' => 'truefalse', 'boolean' => 'truefalse',
            'short' => 'short', 'shortanswer' => 'short', 'sa' => 'short', 'textanswer' => 'short',
        ];
        $type = $typemap[$typeraw] ?? ($typeraw === '' ? '' : 'unknown');

        $options = [];
        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $letter) {
            $v = $get($letter);
            if ($v !== '') {
                $options[strtoupper($letter)] = $v;
            }
        }

        $marksraw = $get('marks');
        $marks = ($marksraw === '') ? 1.0 : (float) str_replace(',', '.', $marksraw);

        return [
            'n'            => $n,
            'type'         => $type,
            'typeraw'      => $typeraw,
            'questiontext' => $get('question'),
            'options'      => $options,
            'answer'       => $get('answer'),
            'marksraw'     => $marksraw,
            'marks'        => $marks,
            'feedback'     => $get('feedback'),
            'name'         => self::derive_name($get('question'), $n),
            'errors'       => [],
        ];
    }

    private static function derive_name(string $questiontext, int $n): string {
        $plain = trim(preg_replace('~\s+~', ' ', strip_tags($questiontext)));
        if ($plain === '') {
            return 'Q' . $n;
        }
        return \core_text::substr($plain, 0, 80);
    }

    /**
     * Attach human-readable errors to $row['errors'].
     */
    public static function validate_row(array &$row): void {
        $e = [];
        $s = static function ($id, $a = null) {
            return get_string($id, 'local_examwizard', $a);
        };

        if ($row['type'] === '') {
            $e[] = $s('err_notype');
        } else if ($row['type'] === 'unknown') {
            $e[] = $s('err_badtype', s($row['typeraw']));
        }
        if ($row['questiontext'] === '') {
            $e[] = $s('err_noquestion');
        }
        if ($row['marksraw'] !== '' && (!is_numeric(str_replace(',', '.', $row['marksraw'])) || $row['marks'] <= 0)) {
            $e[] = $s('err_badmarks');
        }

        $answer = trim($row['answer']);

        if ($row['type'] === 'mcq' || $row['type'] === 'multi') {
            if (count($row['options']) < 2) {
                $e[] = $s('err_nooptions');
            }
            if ($answer === '') {
                $e[] = $s('err_noanswer');
            } else {
                $letters = self::answer_letters($answer);
                $known = array_keys($row['options']);
                $bad = array_diff($letters, $known);
                if ($letters === [] || $bad !== []) {
                    $e[] = $s('err_badanswer', s($answer));
                } else if ($row['type'] === 'mcq' && count($letters) !== 1) {
                    $e[] = $s('err_badanswer', s($answer));
                } else if ($row['type'] === 'multi') {
                    $ncorrect = count($letters);
                    if ($ncorrect < 1 || $ncorrect > 6 || !isset(self::MULTI_FRACTIONS[$ncorrect])) {
                        $e[] = $s('err_multicount');
                    }
                }
            }
        } else if ($row['type'] === 'truefalse') {
            if (self::truthy($answer) === null) {
                $e[] = $s('err_badtf');
            }
        } else if ($row['type'] === 'short') {
            if ($answer === '') {
                $e[] = $s('err_noanswer');
            }
        }

        $row['errors'] = $e;
    }

    /** "B", "a,c", "A C", "b;d" => ['B'], ['A','C'] ... uppercased, de-duped. */
    private static function answer_letters(string $answer): array {
        $parts = preg_split('~[\s,;/|]+~', strtoupper(trim($answer)), -1, PREG_SPLIT_NO_EMPTY);
        $letters = [];
        foreach ($parts as $p) {
            if (preg_match('~^[A-F]$~', $p)) {
                $letters[$p] = true;
            } else {
                return []; // any non-letter token => invalid, signalled as empty.
            }
        }
        return array_keys($letters);
    }

    /** True/False/yes/no/1/0 => bool, else null. */
    private static function truthy(string $v): ?bool {
        $v = strtolower(trim($v));
        if (in_array($v, ['true', 't', 'yes', 'y', '1'], true)) {
            return true;
        }
        if (in_array($v, ['false', 'f', 'no', 'n', '0'], true)) {
            return false;
        }
        return null;
    }

    /**
     * Build a Moodle-XML <quiz> document from the rows that have no errors.
     *
     * @param array $rows output of parse_rows()['rows']
     * @param bool $negative apply 1/3 negative marking to single-choice MCQ wrong options
     * @return string XML
     */
    public static function build_xml(array $rows, bool $negative = false): string {
        $x = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<quiz>\n";
        foreach ($rows as $row) {
            if (!empty($row['errors'])) {
                continue;
            }
            switch ($row['type']) {
                case 'mcq':
                case 'multi':
                    $x .= self::xml_multichoice($row, $negative);
                    break;
                case 'truefalse':
                    $x .= self::xml_truefalse($row);
                    break;
                case 'short':
                    $x .= self::xml_shortanswer($row);
                    break;
            }
        }
        $x .= "</quiz>\n";
        return $x;
    }

    private static function cdata(string $s): string {
        return '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $s) . ']]>';
    }

    private static function text_el(string $tag, string $value, bool $html = false): string {
        $fmt = $html ? ' format="html"' : '';
        return "  <$tag$fmt><text>" . self::cdata($value) . "</text></$tag>\n";
    }

    private static function common_head(array $row): string {
        $marks = rtrim(rtrim(number_format($row['marks'], 7, '.', ''), '0'), '.');
        if ($marks === '') {
            $marks = '1';
        }
        $x  = self::text_el('name', $row['name']);
        $x .= self::text_el('questiontext', $row['questiontext'], true);
        $x .= self::text_el('generalfeedback', (string) $row['feedback'], true);
        $x .= "  <defaultgrade>{$marks}</defaultgrade>\n";
        $x .= "  <hidden>0</hidden>\n";
        return $x;
    }

    private static function xml_multichoice(array $row, bool $negative = false): string {
        $single = $row['type'] === 'mcq';
        $correct = self::answer_letters($row['answer']);
        $ncorrect = count($correct);
        $pos = $single ? '100' : (self::MULTI_FRACTIONS[$ncorrect] ?? '0');
        // single-choice: wrong = 0, or -33.33333 (1/3 penalty) when negative marking is on.
        $neg = $single ? ($negative ? '-33.33333' : '0') : '-' . $pos;

        $x  = "<question type=\"multichoice\">\n";
        $x .= self::common_head($row);
        $x .= "  <penalty>0.3333333</penalty>\n";
        $x .= '  <single>' . ($single ? 'true' : 'false') . "</single>\n";
        $x .= "  <shuffleanswers>true</shuffleanswers>\n";
        $x .= "  <answernumbering>abc</answernumbering>\n";
        $x .= self::text_el('correctfeedback', 'Your answer is correct.', true);
        $x .= self::text_el('partiallycorrectfeedback', 'Your answer is partially correct.', true);
        $x .= self::text_el('incorrectfeedback', 'Your answer is incorrect.', true);
        $x .= "  <shownumcorrect/>\n";
        foreach ($row['options'] as $letter => $optiontext) {
            $frac = in_array($letter, $correct, true) ? $pos : $neg;
            $x .= "  <answer fraction=\"{$frac}\" format=\"html\">\n";
            $x .= '    <text>' . self::cdata($optiontext) . "</text>\n";
            $x .= "    <feedback format=\"html\"><text></text></feedback>\n";
            $x .= "  </answer>\n";
        }
        $x .= "</question>\n";
        return $x;
    }

    private static function xml_truefalse(array $row): string {
        $istrue = self::truthy($row['answer']) === true;
        $x  = "<question type=\"truefalse\">\n";
        $x .= self::common_head($row);
        $x .= "  <penalty>1</penalty>\n";
        $x .= '  <answer fraction="' . ($istrue ? '100' : '0') . "\" format=\"moodle_auto_format\">\n";
        $x .= "    <text>true</text>\n";
        $x .= "    <feedback format=\"html\"><text></text></feedback>\n  </answer>\n";
        $x .= '  <answer fraction="' . ($istrue ? '0' : '100') . "\" format=\"moodle_auto_format\">\n";
        $x .= "    <text>false</text>\n";
        $x .= "    <feedback format=\"html\"><text></text></feedback>\n  </answer>\n";
        $x .= "</question>\n";
        return $x;
    }

    private static function xml_shortanswer(array $row): string {
        $accepted = preg_split('~\s*;\s*~', trim($row['answer']), -1, PREG_SPLIT_NO_EMPTY);
        if ($accepted === []) {
            $accepted = [trim($row['answer'])];
        }
        $x  = "<question type=\"shortanswer\">\n";
        $x .= self::common_head($row);
        $x .= "  <penalty>0.3333333</penalty>\n";
        $x .= "  <usecase>0</usecase>\n";
        foreach ($accepted as $ans) {
            $x .= "  <answer fraction=\"100\" format=\"moodle_auto_format\">\n";
            $x .= '    <text>' . self::cdata($ans) . "</text>\n";
            $x .= "    <feedback format=\"html\"><text></text></feedback>\n  </answer>\n";
        }
        $x .= "</question>\n";
        return $x;
    }

    /**
     * The example rows shipped in the downloadable template.
     *
     * @return array 2D (row 0 = header)
     */
    public static function template_rows(): array {
        return [
            ['Type', 'Question', 'A', 'B', 'C', 'D', 'E', 'F', 'Answer', 'Marks', 'Feedback'],
            ['mcq', 'What is the time complexity of binary search on a sorted array of n items?',
                'O(1)', 'O(log n)', 'O(n)', 'O(n log n)', '', '', 'B', '1',
                'Each step halves the search space.'],
            ['multi', 'Which of the following are prime numbers?',
                '2', '9', '11', '15', '17', '21', 'A,C,E', '2', ''],
            ['truefalse', 'HTTP is a stateless protocol.', '', '', '', '', '', '', 'True', '1', ''],
            ['short', 'Which keyword declares a block-scoped variable in modern JavaScript?',
                '', '', '', '', '', '', 'let; const', '1',
                'Both let and const are block-scoped; var is function-scoped.'],
        ];
    }
}
