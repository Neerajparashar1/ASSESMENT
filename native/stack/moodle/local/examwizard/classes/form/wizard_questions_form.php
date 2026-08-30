<?php
// Wizard step 2 - Questions.

namespace local_examwizard\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class wizard_questions_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;
        $contexts = $this->_customdata['contexts'];

        $mform->addElement('static', 'intro', '', get_string('w_questions_intro', 'local_examwizard'));

        $radio = [
            $mform->createElement('radio', 'source', '', get_string('w_source_upload', 'local_examwizard'), 'upload'),
            $mform->createElement('radio', 'source', '', get_string('w_source_category', 'local_examwizard'), 'category'),
            $mform->createElement('radio', 'source', '', get_string('w_source_later', 'local_examwizard'), 'later'),
        ];
        $mform->addGroup($radio, 'sourcegrp', get_string('w_source', 'local_examwizard'),
            ['<br>'], false);
        $mform->setDefault('source', 'upload');

        // Upload branch.
        $mform->addElement('static', 'tpl', '',
            \html_writer::link(new \moodle_url('/local/examwizard/questions.php', [
                'courseid' => $this->_customdata['courseid'], 'template' => 'csv',
            ]), get_string('templatecsv', 'local_examwizard'), ['class' => 'btn btn-outline-secondary btn-sm mr-2']) .
            \html_writer::link(new \moodle_url('/local/examwizard/questions.php', [
                'courseid' => $this->_customdata['courseid'], 'template' => 'xlsx',
            ]), get_string('templatexlsx', 'local_examwizard'), ['class' => 'btn btn-outline-secondary btn-sm']));
        $mform->hideIf('tpl', 'source', 'neq', 'upload');

        $mform->addElement('filepicker', 'questionsfile', get_string('questionsfile', 'local_examwizard'), null, [
            'maxbytes' => 5 * 1024 * 1024,
            'accepted_types' => ['.csv', '.xlsx', '.txt', '.xml', '.gift'],
        ]);
        $mform->hideIf('questionsfile', 'source', 'neq', 'upload');

        // Existing-category branch.
        $mform->addElement('questioncategory', 'category', get_string('w_source_category', 'local_examwizard'), [
            'contexts' => $contexts->all(),
            'top' => false,
        ]);
        $mform->hideIf('category', 'source', 'neq', 'category');

        $this->add_action_buttons(false, get_string('w_next', 'local_examwizard'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (($data['source'] ?? '') === 'upload') {
            $files = empty($data['questionsfile'])
                ? [] : file_get_all_files_in_draftarea((int) $data['questionsfile']);
            if (!$files) {
                $errors['questionsfile'] = get_string('required');
            }
        }
        return $errors;
    }
}
