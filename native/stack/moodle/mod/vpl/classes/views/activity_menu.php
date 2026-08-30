<?php
// This file is part of VPL for Moodle - http://vpl.dis.ulpgc.es/
//
// VPL for Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// VPL for Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with VPL for Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Activity menu utility class for VPL.
 *
 * @package mod_vpl
 * @copyright 2026 onwards Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jc.rodriguezdelpino@ulpgc.es>
 */
namespace mod_vpl\views;

/**
 * Activity menu utility class for VPL.
 *
 * @copyright 2026 onwards Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jc.rodriguezdelpino@ulpgc.es>
 */
class activity_menu {
    /**
     * @var array teacher menu options for the activity menu
     */
    const TEACHER_MENU_OPTIONS = [
        'view.php',
        'submissionslist.php',
        'similarity_form.php',
        'submissionview.php',
        'submission.php',
        'edit.php',
        'gradesubmission.php',
        'previoussubmissionslist.php',
    ];
    /**
     * @var array List of similarity options for the activity menu
     */
    const SIMILARITY_OPTIONS = [
        'similarity_form.php',
        'listsimilarity.php',
        'listwatermark.php',
    ];

    /**
     * @var array List of teacher submenu options for the activity menu
     */
    const TEACHER_SUBMENU_OPTIONS = [
        'submission.php',
        'edit.php',
        'submissionview.php',
        'gradesubmission.php',
        'previoussubmissionslist.php',
    ];

    /**
     * @var array List of student menu options for the activity menu
     */
    const STUDENT_MENU_OPTIONS = [
        'view.php',
        'submission.php',
        'edit.php',
        'submissionview.php',
    ];

    /**
     * Create a new tabobject for navigation
     *
     * @param String $id
     * @param string|moodle_url $href
     * @param string $str to be i18n
     * @param string $comp component
     * @return tabobject
     * @codeCoverageIgnore
     */
    public static function create_tab($id, $href, $str, $comp = 'mod_vpl') {
        $stri18n = get_string($str, $comp);
        $strdescription = vpl_get_awesome_icon($str) . $stri18n;
        return new \tabobject($id, $href, $strdescription, $stri18n);
    }

    /**
     * Check if the submission of a given user is gradeable by the current user.
     *
     * @param \mod_vpl $vpl The VPL instance to check the gradeability for.
     * @param int $userid The ID of the user whose last submission is to be checked.
     * @param int|null $submissionid Submission to check if set, or the last submission will be checked.
     * @return bool True if the submission is gradeable by the current user, false otherwise.
     */
    public static function is_grade_able($vpl, $userid, $submissionid = null) {
        global $USER;
        if ($vpl->get_grade() == 0 || ! $vpl->is_teacher()) {
            return false;
        }
        if ($submissionid) {
            try {
                $submission = new \mod_vpl_submission($vpl, $submissionid);
                $subinstance = $submission->get_instance();
            } catch (\dml_exception $e) {
                return false;
            }
        } else {
            $subinstance = $vpl->last_user_submission($userid);
        }
        if (! $subinstance) {
            return false;
        }
        return ($subinstance->dategraded == 0 ||
                $subinstance->grader == $USER->id ||
                $subinstance->grader == 0 ||
                $vpl->has_capability(VPL_EDITOTHERSGRADES_CAPABILITY));
    }

    /**
     * Create menu for teachers
     *
     * @param \mod_vpl $vpl The VPL instance to create the menu for.
     * @param string $active The active options in the menu
     *
     */
    public static function print_teacher_menu($vpl, $active) {
        global $COURSE, $USER, $PAGE, $CFG;
        $cmid = $vpl->get_course_module()->id;
        $userid = \optional_param('userid', null, PARAM_INT);
        $example = $vpl->is_example();
        $similarity = $vpl->has_capability(VPL_SIMILARITY_CAPABILITY);
        $instance = $vpl->get_instance();
        $context = \context_course::instance($COURSE->id);
        // If no userid is provided or the user is not enrolled, show the teacher menu for the current user.
        if (!$userid || !\is_enrolled($context, $userid)) {
            $userid = $USER->id;
        }
        $maintabs = [];
        $tabs = [];
        $parms = ['id' => $cmid];
        $parmsuser = ['id' => $cmid, 'userid' => $userid];
        // Activity description.
        $href = \vpl_mod_href('view.php', $parmsuser);
        $maintabs[] = self::create_tab('view.php', $href, 'description');
        // Submissions list.
        $href = \vpl_mod_href('views/submissionslist.php', $parms);
        $maintabs[] = self::create_tab('submissionslist.php', $href, 'submissionslist');
        // Similarity.
        if ($similarity) {
            if (in_array($active, self::SIMILARITY_OPTIONS)) {
                $tabname = $active;
            } else {
                $tabname = 'similarity';
            }
            $href = \vpl_mod_href('similarity/similarity_form.php', $parms);
            $maintabs[] = self::create_tab($tabname, $href, 'similarity');
        }
        // Submenu options.
        if (in_array($active, self::TEACHER_SUBMENU_OPTIONS)) {
            // A submenu option is active.
            $twolevels = true;
            $tabname = $active;
        } else {
            $twolevels = false;
            $tabname = 'test';
        }
        $href = \vpl_mod_href('forms/submissionview.php', $parmsuser);
        if ($userid == $USER->id) {
            $maintabs[] = self::create_tab($tabname, $href, 'test');
        } else {
            // Show other user submission.
            $user = \mod_vpl::get_db_record('user', $userid);
            $strname = $vpl->is_group_activity() ? 'group' : 'user';
            $text = \get_string($strname) . ' ' . $vpl->fullname($user, false);
            $icon = \vpl_get_awesome_icon($strname);
            $url = $PAGE->url->out(false, [ 'userid' => $USER->id ]);
            // Add button to return to own activity.
            // This is a simili-link because it is located inside an <a> tag, and we cannot put an <a> tag within another.
            $buttonexit = \html_writer::tag('span', vpl_get_awesome_icon('exitrole'), [
                    'class' => 'btn-link',
                    'title' => s(get_string('returntoownactivity', VPL)),
                    'onclick' => 'event.preventDefault(); window.location.href=\'' . addslashes_js($url) . '\';',
            ]);
            $maintabs[] = new \tabobject($tabname, $href, "$icon $text $buttonexit", $text);
        }
        if (! $twolevels) {
            // If similarity results are shown, the active tab is set to similarity, and submenu is similarity.
            if ($similarity && $active != 'similarity_form.php' && in_array($active, self::SIMILARITY_OPTIONS)) {
                $href = \vpl_mod_href('similarity/similarity_form.php', $parms);
                $tabs[] = self::create_tab('similarity_form.php', $href, 'similarity');
                if ($active == 'listsimilarity.php') {
                    $tabs[] = self::create_tab('listsimilarity.php', '', 'listsimilarity');
                }
                $plugincfg = \get_config('mod_vpl');
                $watermark = isset($plugincfg->use_watermarks) && $plugincfg->use_watermarks;
                if ($watermark) {
                    $href = \vpl_mod_href('similarity/listwatermark.php', $parms);
                    $tabs[] = self::create_tab('listwatermark.php', $href, 'listwatermarks');
                }
                \print_tabs([$maintabs, $tabs], $active);
            } else {
                \print_tabs([$maintabs], $active);
            }
        } else {
            require_once($CFG->dirroot . '/mod/vpl/vpl_submission.class.php');
            $subinstance = $vpl->last_user_submission($userid);
            // Submission view.
            $href = \vpl_mod_href('forms/submission.php', $parmsuser);
            $tabs[] = self::create_tab('submission.php', $href, 'submission');
            // IDE access for edit, save, run, debug, and evaluate submission.
            $href = \vpl_mod_href('forms/edit.php', $parmsuser);
            $stredit = 'edit';
            if ($example && ($instance->run || $instance->debug)) {
                $stredit = $instance->run ? 'run' : 'debug';
            }
            $tabs[] = self::create_tab('edit.php', $href, $stredit);
            $href = \vpl_mod_href('forms/submissionview.php', $parmsuser);
            $tabs[] = self::create_tab('submissionview.php', $href, 'submissionview');
            if (self::is_grade_able($vpl, $userid)) {
                $href = \vpl_mod_href('forms/gradesubmission.php', $parmsuser);
                $tabs[] = self::create_tab('gradesubmission.php', $href, vpl_get_gradenoun_str(), 'core');
            }
            if ($subinstance) {
                $href = \vpl_mod_href('views/previoussubmissionslist.php', $parmsuser);
                $tabs[] = self::create_tab('previoussubmissionslist.php', $href, 'previoussubmissionslist');
            }
            print_tabs([$maintabs, $tabs], $active);
        }
    }

    /**
     * Create tabs to view_description/submit/view_submission/edit
     *
     * @param \mod_vpl $vpl The VPL instance to create the menu for.
     * @param string $active The active options in the menu
     *
     */
    public static function print_student_menu($vpl, $active) {
        if (! $vpl->is_visible()) {
            return;
        }
        // TODO refactor using functions.
        global $USER;
        $vpl->require_capability(VPL_VIEW_CAPABILITY);
        $cmid = $vpl->get_course_module()->id;
        $userid = $USER->id;
        $submitable = $vpl->is_submit_able();
        $example = $vpl->is_example();
        $instance = $vpl->get_instance();
        if (! in_array($active, self::STUDENT_MENU_OPTIONS)) {
            $active = 'view.php';
        }
        $tabs = [];
        $parms = ['id' => $cmid, 'userid' => $userid];
        $href = vpl_mod_href('view.php', $parms);
        $tabs[] = self::create_tab('view.php', $href, 'description');
        if ($submitable && !$instance->restrictededitor && !$example) {
            $href = vpl_mod_href('forms/submission.php', $parms);
            $tabs[] = self::create_tab('submission.php', $href, 'submission');
        }
        if ($submitable || $example) {
            $href = \vpl_mod_href('forms/edit.php', $parms);
            $stredit = 'edit';
            if ($example && ($instance->run || $instance->debug)) {
                $stredit = $instance->run ? 'run' : 'debug';
            }
            $tabs[] = self::create_tab('edit.php', $href, $stredit);
        }
        if (! $example) {
            $href = \vpl_mod_href('forms/submissionview.php', $parms);
            $tabs[] = self::create_tab('submissionview.php', $href, 'submissionview');
        }
        // Show user picture if this activity require password.
        if ($instance->password > '') {
            $user = \mod_vpl::get_db_record('user', $userid);
            echo '<div class="vpl_student_picture_in_menu">';
            echo $vpl->user_picture($user);
            echo '</div>';
        }
        \print_tabs([$tabs], $active);
    }

    /**
     * Create tabs to view_description/submit/view_submission/edit
     *
     * @param \mod_vpl $vpl The VPL instance to create the menu for.
     * @param string $active The active options in the menu
     *
     */
    public static function print_menu($vpl, $active) {
        if ($vpl->is_teacher()) {
            self::print_teacher_menu($vpl, $active);
        } else {
            self::print_student_menu($vpl, $active);
        }
    }
}
