<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_vpl\plugininfo;

/**
 * Base class for VPL evaluators.
 * This class is used to define the interface for VPL evaluators.
 *
 * @package   mod_vpl
 * @copyright 2024 Juan Carlos Rodriguez del Pino
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vplevaluator_base {
    /**
     * Default script to start the evaluation.
     * @var string
     */
    public const DEFAULT_EXECUTION_SCRIPT = 'vpl_evaluate.sh';

    /**
     * Name of the evaluator.
     * @var string
     */
    protected $name;

    /**
     * VPL activity object.
     * @var \mod_vpl
     */
    protected $vpl;

    /**
     * Data of current evaluation
     * @var object|null
     */
    protected $evaluationdata;

    /**
     * Cache for help content.
     * @var string|null
     */
    private ?string $helpcache = null;

    /**
     * Constructor.
     * @param string $name of the evaluator.
     * @param \mod_vpl $vpl VPL activity object.
     * @param object|null $evaluationdata Evaluation data.
     */
    public function __construct($name, $vpl = null, $evaluationdata = null) {
        $this->name = preg_replace('/[^a-z0-9_]/', '', $name);
        $this->vpl = $vpl;
        $this->evaluationdata = $evaluationdata;
    }

    /**
     * Returns the files to add to the execution files.
     * Commonly include at least the file 'vpl_evaluate.sh',
     * if not default vpl_evaluate.sh is used.
     * These files contain the evaluation framework and how to run it.
     * @return array<string, string> Array of files [filename => content]
     */
    public function get_execution_files(): array {
        return [];
    }

    /**
     * Returns the path to the script to start the evaluation.
     * @return string path to the start script
     */
    public function get_execution_script(): string {
        return self::DEFAULT_EXECUTION_SCRIPT;
    }

    /**
     * Files to use as base for setting test cases.
     * These files will be saved in the execution files section.
     * Contains the initial values for test cases.
     * Names must not collide with other execution files.
     * @return array<string, string> Array of files [file_name => contents]
     */
    public function get_test_files(): array {
        return [];
    }

    /**
     * Returns the help for the evaluator in MD format.
     * This help is shown in the evaluator settings.
     * @return string
     */
    public function get_help(): string {
        if ($this->helpcache !== null) {
            return $this->helpcache;
        }
        global $CFG;
        $help = '';
        $helpfilenamebase = $CFG->dirroot . "/mod/vpl/evaluator/{$this->name}/lang";
        foreach ([current_language(), 'en'] as $lang) {
            $lang = preg_replace('/[^a-z0-9_]/', '', $lang);
            $helpfilename = "{$helpfilenamebase}/{$lang}/help.md";
            if (file_exists($helpfilename)) {
                $help = file_get_contents($helpfilename);
                break;
            }
        }
        $this->helpcache = $help === false ? '' : $help;
        return $this->helpcache;
    }

    /**
     * Get printable evaluator name with help link
     *
     * @param \mod_vpl $vpl VPL object class instance
     * @param bool $ifhelp if true return '' if no help available
     * @return string HTML formatted string
     */
    public function get_printable_help_link(\mod_vpl $vpl, bool $ifhelp = false): string {
        global $OUTPUT;
        $help = $this->get_help();
        if ($ifhelp && empty($help)) {
            return '';
        }
        $subplugname = 'vplevaluator_' . $this->name;
        $title = vpl_get_awesome_icon('advancedsettings');
        $title .= get_string('pluginname', $subplugname);
        if ($help !== '') {
            $parms = ['id' => $vpl->get_course_module()->id, 'evaluator' => $this->name];
            $url = new \moodle_url('/mod/vpl/views/evaluator_help.php', $parms);
            $icon = $OUTPUT->pix_icon('help', get_string('help'));
            $attr = ['target' => '_blank', 'class' => 'btn btn-link p-0', 'title' => get_string('help') ];
            $html = \html_writer::link($url, $title . $icon, $attr);
        } else {
            $html = $title;
        }
        return "$html<br>\n";
    }

    /**
     * Get printable evaluator help documentation.
     * @param \mod_vpl $vpl object class instance
     * @return string HTML formatted string
     */
    public function get_printable_help(\mod_vpl $vpl): string {
        $help = $this->get_help();
        if ($help !== '') {
            return format_text($help, FORMAT_MARKDOWN, ['context' => $vpl->get_context()]);
        }
        return '';
    }

    /**
     * Return the files to keep when running after compiling
     * @return array of file_names
     */
    public function get_files_to_keep_when_running(): array {
        return [];
    }

    /**
     * Return the files to exclude from being sent when not evaluating.
     * Removing these files can improve security by preventing
     * sensitive data from being sent to execution servers.
     * @return array of file_names
     */
    public function get_files_to_exclude_when_not_evaluating(): array {
        return [];
    }

    /**
     * Get i18n strings for the evaluator.
     * The strings are sent as bash variables to the evaluator.
     * The bash variables will be sent in the vpl_environment.sh file.
     * The variables will have form: VPLEVALUATOR_STR_<string_key>
     * @return array of strings: string_key => string_value
     */
    public function get_strings(): array {
        global $CFG;
        $stringsfilename = $CFG->dirroot . "/mod/vpl/evaluator/{$this->name}/lang/en/vplevaluator_{$this->name}.php";
        $strlist = [];
        if (file_exists($stringsfilename)) {
            $string = [];
            include($stringsfilename);
            $modname = 'vplevaluator_' . $this->name;
            foreach (array_keys($string) as $key) {
                // Ignore key with : => generate bad variable names.
                if (strpos($key, ':') === false) {
                    $strlist[$key] = get_string($key, $modname);
                }
            }
        }
        return $strlist;
    }

    /**
     * Evaluator has effect on different actions.
     * @param string $action the action to check the effect on. Possible values: 'run', 'debug'.
     * @return bool true if the evaluator affects the actions
     */
    public function has_effect_on(string $action): bool {
        return false;
    }

    /**
     * Apply the effect of the evaluator on user actions.
     * This method applies the effect on an action changing evaluationdata attribute.
     * @param string $action the action to apply the effect on. Possible values: 'run', 'debug'.
     */
    public function apply_effect_on(string $action): void {
        // By default, do nothing.
    }

    /**
     * Evaluator has settings to show in execution options form.
     * @return bool true if the evaluator has settings to show in execution options form
     */
    public function has_settings(): bool {
        return false;
    }

    /**
     * Evaluator add own settings to form.
     * @param \mod_vpl $vpl the VPL instance for which the execution options are being set.
     * @param \moodleform $form the form to add the settings on.
     */
    public function add_form_settings(\mod_vpl $vpl, \moodleform $form): void {
        // By default, do nothing.
    }

    /**
     * Evaluator save own settings from form.
     * @param \mod_vpl $vpl the VPL instance for which the settings are being saved.
     * @param object $data from the form with the settings to save.
     */
    public function save_form_settings(\mod_vpl $vpl, object $data): void {
        // By default, do nothing.
    }
}
