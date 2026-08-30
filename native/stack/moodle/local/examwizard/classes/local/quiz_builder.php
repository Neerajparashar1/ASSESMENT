<?php
// Create a quiz activity with exam-sensible defaults and wire SEB / proctoring.

namespace local_examwizard\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Turns the wizard's answers into a real Moodle quiz.
 *
 * All the "26 fieldsets" a teacher would otherwise face are filled from the quiz
 * module's own admin defaults (get_config('quiz')); the wizard only overrides the
 * handful of settings it asks about.
 */
class quiz_builder {

    /**
     * @param stdClass $course
     * @param array $basics ['name','intro','section','timeopen','timeclose','timelimit'(minutes)]
     * @param array $rules  ['attempts','shuffle','review'(afterclose|immediately),'seb'(bool),'proctoring'(bool),'negative'(bool)]
     * @return array [int $cmid, stdClass $quiz, stdClass $cm]
     */
    public static function create(\stdClass $course, array $basics, array $rules): array {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/course/modlib.php');
        require_once($CFG->dirroot . '/mod/quiz/lib.php');
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');

        $cfg = (object) get_config('quiz');

        $timelimit = max(0, (int) ($basics['timelimit'] ?? 0)) * 60;
        $attempts  = (int) ($rules['attempts'] ?? 1);
        $immediate = ($rules['review'] ?? 'afterclose') === 'immediately';

        $mi = new \stdClass();

        // --- generic activity fields ---
        $mi->modulename          = 'quiz';
        $mi->module              = $DB->get_field('modules', 'id', ['name' => 'quiz'], MUST_EXIST);
        $mi->course              = $course->id;
        $mi->section             = (int) ($basics['section'] ?? 0);
        $mi->name                = \core_text::substr(trim($basics['name']), 0, 255);
        $mi->visible             = 1;
        $mi->visibleoncoursepage = 1;
        $mi->cmidnumber          = '';
        $mi->introeditor         = ['text' => (string) ($basics['intro'] ?? ''), 'format' => FORMAT_HTML, 'itemid' => 0];
        $mi->showdescription     = 0;
        $mi->groupmode           = 0;
        $mi->groupingid          = 0;
        $mi->availabilityconditionsjson = '';
        $mi->completion          = 0;

        // --- timing ---
        $mi->timeopen        = (int) ($basics['timeopen'] ?? 0);
        $mi->timeclose       = (int) ($basics['timeclose'] ?? 0);
        $mi->timelimit       = $timelimit;
        $mi->overduehandling = 'autosubmit';
        $mi->graceperiod     = 0;

        // --- attempts / behaviour ---
        $mi->attempts            = $attempts;               // 0 = unlimited
        $mi->attemptonlast       = 0;
        $mi->grademethod         = QUIZ_GRADEHIGHEST;
        $mi->preferredbehaviour  = $cfg->preferredbehaviour ?? 'deferredfeedback';
        $mi->canredoquestions    = 0;
        $mi->shuffleanswers      = 1;
        $mi->questionsperpage    = (int) ($cfg->questionsperpage ?? 1);
        $mi->navmethod           = 'free';
        $mi->quizpassword        = '';
        $mi->subnet              = '';
        $mi->browsersecurity     = '-';
        $mi->delay1              = 0;
        $mi->delay2              = 0;
        $mi->showuserpicture     = 0;
        $mi->showblocks          = 0;
        $mi->decimalpoints       = (int) ($cfg->decimalpoints ?? 2);
        $mi->questiondecimalpoints = (int) ($cfg->questiondecimalpoints ?? -1);
        $mi->autosaveperiod      = (int) ($cfg->autosaveperiod ?? 60);

        // --- grade ---
        $mi->grade               = 100;   // recomputed once questions are attached
        $mi->gradepass           = 0;
        $mi->gradecat            = 0;

        // --- review options (form-style fields; quiz_process_options builds the bitmask) ---
        $cols = ['attempt', 'correctness', 'maxmarks', 'marks', 'specificfeedback',
            'generalfeedback', 'rightanswer', 'overallfeedback'];
        foreach ($cols as $c) {
            $mi->{$c . 'closed'} = 1;                        // always visible after the quiz closes
            if ($immediate) {
                $mi->{$c . 'immediately'} = 1;               // ...and right after each attempt
            }
        }

        // --- create it (SEB / proctoring wired separately, below) ---
        $moduleinfo = add_moduleinfo($mi, $course);
        $cm = get_coursemodule_from_id('quiz', $moduleinfo->coursemodule, $course->id, false, MUST_EXIST);
        $quiz = $DB->get_record('quiz', ['id' => $cm->instance], '*', MUST_EXIST);

        // Shuffle-the-questions lives on the quiz's first section row, not the quiz record.
        if (!empty($rules['shuffle'])) {
            $DB->set_field('quiz_sections', 'shufflequestions', 1,
                ['quizid' => $quiz->id, 'firstslot' => 1]);
        }

        if (!empty($rules['seb'])) {
            self::wire_seb($quiz, $cm);
        }
        if (!empty($rules['proctoring'])) {
            self::wire_proctoring($quiz);
        }

        return [(int) $cm->id, $quiz, $cm];
    }

    /**
     * Write the quizaccess_seb_quizsettings row (mirrors native/harden_seb_quiz.php's
     * per-quiz part: manual config, quit link -> local_sebkiosk/finish.php).
     */
    protected static function wire_seb(\stdClass $quiz, \stdClass $cm): void {
        global $DB, $USER, $CFG;

        // The SEB access rule may already have stubbed a row during add_moduleinfo;
        // replace it wholesale so our quit-link / quit settings win.
        $DB->delete_records('quizaccess_seb_quizsettings', ['quizid' => $quiz->id]);
        $now = time();
        $row = (object) [
            'quizid' => $quiz->id,
            'cmid' => $cm->id,
            'templateid' => 0,
            'requiresafeexambrowser' => 1,        // USE_SEB_CONFIG_MANUALLY
            'showsebtaskbar' => 1,
            'showwificontrol' => 0,
            'showreloadbutton' => 1,
            'showtime' => 1,
            'showkeyboardlayout' => 1,
            'allowuserquitseb' => 1,
            'quitpassword' => '',
            'linkquitseb' => rtrim($CFG->wwwroot, '/') . '/local/sebkiosk/finish.php?cmid=' . $cm->id,
            'userconfirmquit' => 1,
            'enableaudiocontrol' => 0,
            'muteonstartup' => 0,
            'allowcapturecamera' => 0,
            'allowcapturemicrophone' => 0,
            'allowspellchecking' => 0,
            'allowreloadinexam' => 0,
            'activateurlfiltering' => 0,
            'filterembeddedcontent' => 0,
            'expressionsallowed' => '',
            'regexallowed' => '',
            'expressionsblocked' => '',
            'regexblocked' => '',
            'allowedbrowserexamkeys' => '',
            'showsebdownloadlink' => 1,
            'usermodified' => $USER->id,
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $DB->insert_record('quizaccess_seb_quizsettings', $row);

        // Backstop: only ever one attempt, auto-submit when the clock runs out.
        if ((int) $quiz->attempts !== 1 || (int) $quiz->timelimit === 0) {
            $updates = ['id' => $quiz->id, 'overduehandling' => 'autosubmit'];
            if ((int) $quiz->timelimit === 0) {
                $updates['timelimit'] = 1800;
            }
            $DB->update_record('quiz', (object) $updates);
        }
    }

    protected static function wire_proctoring(\stdClass $quiz): void {
        global $DB;
        if ($DB->record_exists('quizaccess_proctoring', ['quizid' => $quiz->id])) {
            return;
        }
        $DB->insert_record('quizaccess_proctoring', (object) [
            'quizid' => $quiz->id,
            'proctoringrequired' => 1,
        ]);
    }
}
