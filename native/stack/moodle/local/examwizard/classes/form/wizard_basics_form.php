<?php
// Wizard step 1 - Basics.

namespace local_examwizard\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class wizard_basics_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;
        $sections = $this->_customdata['sections'];   // array<int,string>

        $mform->addElement('text', 'name', get_string('w_examname', 'local_examwizard'), ['size' => 60]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('select', 'section', get_string('w_section', 'local_examwizard'), $sections);

        $mform->addElement('date_time_selector', 'timeopen', get_string('quizopen', 'quiz'),
            ['optional' => true]);
        $mform->addElement('date_time_selector', 'timeclose', get_string('quizclose', 'quiz'),
            ['optional' => true]);

        $mform->addElement('text', 'timelimit', get_string('w_timelimit', 'local_examwizard'), ['size' => 6]);
        $mform->setType('timelimit', PARAM_INT);
        $mform->setDefault('timelimit', 30);
        $mform->addHelpButton('timelimit', 'w_timelimit', 'local_examwizard');

        $mform->addElement('editor', 'introeditor', get_string('w_instructions', 'local_examwizard'),
            ['rows' => 4]);
        $mform->setType('introeditor', PARAM_RAW);

        $cohorts = $this->_customdata['cohorts'] ?? [];   // array<int,string> id => "name (N)"
        if ($cohorts) {
            $mform->addElement('autocomplete', 'cohortids',
                get_string('w_batches', 'local_examwizard'), $cohorts, ['multiple' => true]);
            $mform->addHelpButton('cohortids', 'w_batches', 'local_examwizard');
        }

        $this->add_action_buttons(false, get_string('w_next', 'local_examwizard'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['timelimit'] < 0) {
            $errors['timelimit'] = get_string('w_err_timelimit', 'local_examwizard');
        }
        if (!empty($data['timeopen']) && !empty($data['timeclose']) && $data['timeclose'] <= $data['timeopen']) {
            $errors['timeclose'] = get_string('w_err_closebeforeopen', 'local_examwizard');
        }
        return $errors;
    }
}
