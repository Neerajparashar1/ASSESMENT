<?php
// Read exam results for the Exam Control "results" view + CSV export.

namespace local_examwizard\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Aggregates and per-student rows for one quiz.
 */
class results {

    /**
     * Headline numbers for an exam.
     *
     * @return array enrolled, attempted, inprogress, notstarted, avg, avgpct, max, min,
     *               maxgrade, pass, passcount, passrate
     */
    public static function summary(\stdClass $quiz, \context $context): array {
        global $DB;

        $enrolled = count_enrolled_users($context, 'mod/quiz:attempt');
        $grades = $DB->get_records('quiz_grades', ['quiz' => $quiz->id], '', 'userid, grade');
        $attempted = count($grades);
        $inprogress = $DB->count_records('quiz_attempts', ['quiz' => $quiz->id, 'state' => 'inprogress']);

        $sum = 0.0;
        $max = 0.0;
        $min = null;
        $pass = ($quiz->gradepass > 0) ? (float) $quiz->gradepass : null;
        $passcount = 0;
        foreach ($grades as $g) {
            $sum += $g->grade;
            $max = max($max, (float) $g->grade);
            $min = is_null($min) ? (float) $g->grade : min($min, (float) $g->grade);
            if ($pass !== null && $g->grade >= $pass) {
                $passcount++;
            }
        }
        $avg = $attempted ? $sum / $attempted : 0.0;
        $maxgrade = (float) $quiz->grade;

        return [
            'enrolled'   => $enrolled,
            'attempted'  => $attempted,
            'inprogress' => $inprogress,
            'notstarted' => max(0, $enrolled - $attempted - $inprogress),
            'avg'        => $avg,
            'avgpct'     => $maxgrade ? $avg / $maxgrade * 100 : 0,
            'max'        => $max,
            'min'        => $min ?? 0.0,
            'maxgrade'   => $maxgrade,
            'pass'       => $pass,
            'passcount'  => $passcount,
            'passrate'   => ($pass !== null && $attempted) ? $passcount / $attempted * 100 : null,
        ];
    }

    /**
     * One row per enrolled candidate.
     *
     * @return array[] each: userid, name, username, email, attempts, grade|null, percent|null,
     *                 passed|null, firststart, lastfinish, duration (seconds), laststate
     */
    public static function rows(\stdClass $quiz, \context $context): array {
        global $DB;

        [$esql, $eparams] = get_enrolled_sql($context, 'mod/quiz:attempt');
        $params = $eparams + [
            'q1' => $quiz->id, 'q2' => $quiz->id, 'q3' => $quiz->id, 'q4' => $quiz->id, 'q5' => $quiz->id,
        ];
        $sql = "SELECT u.id AS userid, u.firstname, u.lastname, u.username, u.email,
                       qg.grade AS grade,
                       (SELECT MIN(qa.timestart) FROM {quiz_attempts} qa
                          WHERE qa.quiz = :q1 AND qa.userid = u.id) AS firststart,
                       (SELECT MAX(qa.timefinish) FROM {quiz_attempts} qa
                          WHERE qa.quiz = :q2 AND qa.userid = u.id AND qa.state = 'finished') AS lastfinish,
                       (SELECT COUNT(*) FROM {quiz_attempts} qa
                          WHERE qa.quiz = :q3 AND qa.userid = u.id AND qa.preview = 0) AS attempts,
                       (SELECT qa.state FROM {quiz_attempts} qa
                          WHERE qa.quiz = :q4 AND qa.userid = u.id AND qa.preview = 0
                       ORDER BY qa.attempt DESC LIMIT 1) AS laststate
                  FROM {user} u
                  JOIN ($esql) je ON je.id = u.id
             LEFT JOIN {quiz_grades} qg ON qg.quiz = :q5 AND qg.userid = u.id
                 WHERE u.deleted = 0
              ORDER BY u.lastname, u.firstname";

        $maxgrade = (float) $quiz->grade;
        $pass = ($quiz->gradepass > 0) ? (float) $quiz->gradepass : null;

        $out = [];
        foreach ($DB->get_records_sql($sql, $params) as $r) {
            $grade = is_null($r->grade) ? null : (float) $r->grade;
            $out[] = [
                'userid'     => (int) $r->userid,
                'name'       => fullname($r),
                'username'   => $r->username,
                'email'      => $r->email,
                'attempts'   => (int) $r->attempts,
                'grade'      => $grade,
                'percent'    => (!is_null($grade) && $maxgrade) ? $grade / $maxgrade * 100 : null,
                'passed'     => (!is_null($grade) && $pass !== null) ? ($grade >= $pass) : null,
                'firststart' => (int) $r->firststart,
                'lastfinish' => (int) $r->lastfinish,
                'duration'   => ($r->firststart && $r->lastfinish) ? (int) $r->lastfinish - (int) $r->firststart : 0,
                'laststate'  => $r->laststate ?: 'notstarted',
            ];
        }
        return $out;
    }

    /**
     * Stream the per-student results as CSV. Halts the request.
     */
    public static function send_csv(\stdClass $quiz, \context $context, string $examname): void {
        $rows = self::rows($quiz, $context);
        $maxgrade = (float) $quiz->grade;
        $haspass = ($quiz->gradepass > 0);

        $fname = preg_replace('~[^A-Za-z0-9_-]+~', '_', $examname);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="results-' . $fname . '.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');

        $head = ['Name', 'Username', 'Email', 'Attempts', 'Score', 'Out of', 'Percent'];
        if ($haspass) {
            $head[] = 'Result';
        }
        $head = array_merge($head, ['Started', 'Submitted', 'Duration (min)', 'State']);
        fputcsv($out, $head);

        foreach ($rows as $r) {
            $line = [
                $r['name'],
                $r['username'],
                $r['email'],
                $r['attempts'],
                is_null($r['grade']) ? '' : round($r['grade'], 2),
                round($maxgrade, 2),
                is_null($r['percent']) ? '' : round($r['percent'], 1),
            ];
            if ($haspass) {
                $line[] = is_null($r['passed']) ? '' : ($r['passed'] ? 'Pass' : 'Fail');
            }
            $line[] = $r['firststart'] ? userdate($r['firststart'], '%Y-%m-%d %H:%M') : '';
            $line[] = $r['lastfinish'] ? userdate($r['lastfinish'], '%Y-%m-%d %H:%M') : '';
            $line[] = $r['duration'] ? round($r['duration'] / 60) : '';
            $line[] = $r['laststate'];
            fputcsv($out, $line);
        }
        fclose($out);
        exit;
    }
}
