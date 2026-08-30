<?php
// Exam Control - view / change the Safe Exam Browser quit password
// (the "master" one invigilators use to exit SEB during an exam).

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$baseurl = new moodle_url('/local/examwizard/seb.php');
$PAGE->set_url($baseurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('seb_title', 'local_examwizard'));
$PAGE->set_heading(get_string('seb_title', 'local_examwizard'));
$PAGE->navbar->add(get_string('pluginname', 'local_examwizard'), new moodle_url('/local/examwizard/index.php'));
$PAGE->navbar->add(get_string('seb_title', 'local_examwizard'));

$current = (string) get_config('quizaccess_seb', 'quitpassword');
$sebexams = $DB->count_records_select('quizaccess_seb_quizsettings', 'requiresafeexambrowser > 0');

/** The change form. */
class local_examwizard_seb_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;
        $n = $this->_customdata['sebexams'];

        $mform->addElement('passwordunmask', 'newpw', get_string('seb_newpw', 'local_examwizard'));
        $mform->setType('newpw', PARAM_RAW);
        $mform->addHelpButton('newpw', 'seb_newpw', 'local_examwizard');

        $mform->addElement('passwordunmask', 'newpw2', get_string('seb_newpw2', 'local_examwizard'));
        $mform->setType('newpw2', PARAM_RAW);

        $mform->addElement('advcheckbox', 'applyall',
            get_string('seb_applyall', 'local_examwizard', $n), '', null, [0, 1]);
        $mform->setDefault('applyall', 1);
        if (!$n) {
            $mform->hardFreeze('applyall');
        }

        $this->add_action_buttons(true, get_string('seb_save', 'local_examwizard'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (($data['newpw'] ?? '') !== ($data['newpw2'] ?? '')) {
            $errors['newpw2'] = get_string('seb_err_mismatch', 'local_examwizard');
        }
        return $errors;
    }
}

$mform = new local_examwizard_seb_form($baseurl, ['sebexams' => $sebexams]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/examwizard/index.php'));
}

if ($data = $mform->get_data()) {
    $new = (string) $data->newpw;
    set_config('quitpassword', $new, 'quizaccess_seb');

    $changed = [];
    if (!empty($data->applyall) && $sebexams) {
        $rows = $DB->get_records_select('quizaccess_seb_quizsettings', 'requiresafeexambrowser > 0');
        foreach ($rows as $r) {
            if ((string) $r->quitpassword !== $new) {
                $DB->update_record('quizaccess_seb_quizsettings',
                    (object) ['id' => $r->id, 'quitpassword' => $new, 'timemodified' => time()]);
                if ($cm = get_coursemodule_from_id('quiz', $r->cmid, 0, false, IGNORE_MISSING)) {
                    $changed[] = format_string($DB->get_field('quiz', 'name', ['id' => $r->quizid]));
                }
            }
        }
    }
    purge_all_caches();

    redirect($baseurl, get_string('seb_saved', 'local_examwizard',
        (object) ['count' => count($changed)]), null, \core\output\notification::NOTIFY_SUCCESS);
}

$s = fn($id, $a = null) => get_string($id, 'local_examwizard', $a);

echo $OUTPUT->header();
echo $OUTPUT->heading($s('seb_title'));
echo html_writer::div($s('seb_intro'), 'text-muted mb-3');

echo html_writer::start_div('ew-card');
echo html_writer::tag('h3', $s('seb_current'), ['class' => 'ew-card-h']);
if (trim($current) === '') {
    echo html_writer::div($s('seb_current_none'), 'text-muted');
} else {
    echo html_writer::div(
        html_writer::tag('code', s($current), ['id' => 'ew-cur', 'class' => 'ew-masked']) . ' ' .
        html_writer::link('#', $s('help_reveal'), ['class' => 'ew-reveal', 'data-target' => 'ew-cur']) . ' ' .
        html_writer::tag('button', $s('help_copy'),
            ['type' => 'button', 'class' => 'ew-copy btn btn-sm btn-outline-secondary', 'data-copy' => $current]),
        'mb-2');
}
echo html_writer::end_div();

echo html_writer::start_div('ew-card');
echo html_writer::div($s('seb_warn'), 'alert alert-warning');
$mform->display();
echo html_writer::end_div();

$PAGE->requires->js_amd_inline("
    require(['jquery'], function($){
        $('.ew-copy').on('click', function(){
            navigator.clipboard && navigator.clipboard.writeText($(this).data('copy'));
            $(this).text('" . get_string('copied', 'local_examwizard') . "');
        });
        $('.ew-reveal').on('click', function(e){
            e.preventDefault();
            $('#' + $(this).data('target')).toggleClass('ew-masked');
        });
    });
");

echo $OUTPUT->footer();
