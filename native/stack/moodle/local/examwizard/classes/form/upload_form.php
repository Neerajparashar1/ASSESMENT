<?php
// Step 1 form for the Exam Wizard question uploader.

namespace local_examwizard\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

/**
 * Pick a category / quiz and upload the questions file.
 *
 * customdata:
 *  - contexts : \core_question\local\bank\question_edit_contexts
 *  - quizzes  : array<int,string>  (quiz cmid => name) for the "also add to quiz" select
 */
class upload_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;
        $contexts = $this->_customdata['contexts'];
        $quizzes = $this->_customdata['quizzes'] ?? [];

        $mform->addElement('header', 'filehdr', get_string('questionsfile', 'local_examwizard'));
        $mform->setExpanded('filehdr');

        $mform->addElement('static', 'tpl', get_string('downloadtemplate', 'local_examwizard'),
            \html_writer::link(new \moodle_url('/local/examwizard/questions.php', [
                'courseid' => $this->_customdata['courseid'], 'template' => 'csv',
            ]), get_string('templatecsv', 'local_examwizard'), ['class' => 'btn btn-outline-secondary btn-sm mr-2']) .
            \html_writer::link(new \moodle_url('/local/examwizard/questions.php', [
                'courseid' => $this->_customdata['courseid'], 'template' => 'xlsx',
            ]), get_string('templatexlsx', 'local_examwizard'), ['class' => 'btn btn-outline-secondary btn-sm']));
        $mform->addHelpButton('tpl', 'downloadtemplate', 'local_examwizard');

        $mform->addElement('filepicker', 'questionsfile', get_string('questionsfile', 'local_examwizard'), null, [
            'maxbytes' => 5 * 1024 * 1024,
            'accepted_types' => ['.csv', '.xlsx', '.txt', '.xml', '.gift'],
        ]);
        $mform->addRule('questionsfile', null, 'required', null, 'client');
        $mform->addHelpButton('questionsfile', 'questionsfile', 'local_examwizard');

        $mform->addElement('header', 'wherehdr', get_string('targetcategory', 'local_examwizard'));
        $mform->setExpanded('wherehdr');

        $mform->addElement('questioncategory', 'category', get_string('targetcategory', 'local_examwizard'), [
            'contexts' => $contexts->all(),
            'top' => false,
            'currentcat' => 0,
            'nochildrenof' => -1,
        ]);

        $mform->addElement('text', 'newcategoryname', get_string('newcategoryname', 'local_examwizard'),
            ['size' => 40, 'placeholder' => get_string('newcategoryname', 'local_examwizard')]);
        $mform->setType('newcategoryname', PARAM_TEXT);
        $mform->addHelpButton('newcategoryname', 'newcategoryname', 'local_examwizard');

        if ($quizzes) {
            $options = [0 => get_string('alsoaddtoquiz_none', 'local_examwizard')] + $quizzes;
            $mform->addElement('select', 'addtoquiz', get_string('alsoaddtoquiz', 'local_examwizard'), $options);
            $mform->setDefault('addtoquiz', 0);
        } else {
            $mform->addElement('hidden', 'addtoquiz', 0);
            $mform->setType('addtoquiz', PARAM_INT);
        }

        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'step', 'upload');
        $mform->setType('step', PARAM_ALPHA);

        $this->add_action_buttons(false, get_string('review', 'local_examwizard'));
    }
}
