<?php
// Wizard step 3 - Rules.

namespace local_examwizard\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class wizard_rules_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('select', 'attempts', get_string('w_attempts', 'local_examwizard'), [
            1 => '1', 2 => '2', 3 => '3', 0 => get_string('unlimited'),
        ]);
        $mform->setDefault('attempts', 1);

        $mform->addElement('select', 'review', get_string('w_review', 'local_examwizard'), [
            'afterclose'  => get_string('w_review_afterclose', 'local_examwizard'),
            'immediately' => get_string('w_review_immediately', 'local_examwizard'),
        ]);
        $mform->setDefault('review', 'afterclose');
        $mform->addHelpButton('review', 'w_review', 'local_examwizard');

        $mform->addElement('advcheckbox', 'shuffle', get_string('w_shuffle', 'local_examwizard'), '',
            null, [0, 1]);
        $mform->setDefault('shuffle', 1);

        $mform->addElement('advcheckbox', 'negative', get_string('w_negative', 'local_examwizard'), '',
            null, [0, 1]);
        $mform->addHelpButton('negative', 'w_negative', 'local_examwizard');

        $mform->addElement('header', 'securehdr', get_string('w_security', 'local_examwizard'));
        $mform->setExpanded('securehdr');

        $mform->addElement('advcheckbox', 'seb', get_string('w_seb', 'local_examwizard'), '',
            null, [0, 1]);
        $mform->setDefault('seb', 1);
        $mform->addHelpButton('seb', 'w_seb', 'local_examwizard');

        $mform->addElement('advcheckbox', 'proctoring', get_string('w_proctoring', 'local_examwizard'), '',
            null, [0, 1]);
        $mform->addHelpButton('proctoring', 'w_proctoring', 'local_examwizard');

        $this->add_action_buttons(false, get_string('w_next', 'local_examwizard'));
    }
}
