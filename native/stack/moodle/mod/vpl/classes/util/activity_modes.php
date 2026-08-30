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
 * Activity mode utility class for VPL.
 *
 * @package mod_vpl
 * @copyright 2026 onwards Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jc.rodriguezdelpino@ulpgc.es>
 */
namespace mod_vpl\util;

/**
 * Activity mode utility class for VPL.
 *
 * @copyright 2026 onwards Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jc.rodriguezdelpino@ulpgc.es>
 */
class activity_modes {
    /** @var int Normal activity mode */
    public const NORMAL = 0;
    /** @var int Example activity mode */
    public const EXAMPLE = 1;
    /** @var int No students activity mode */
    public const NOSTUDENTS = 2;
    /** @var int Based on activity mode */
    public const BASEDON = 3;
    /** @var int Students read-only activity mode */
    public const STUDENTSREADONLY = 4;
    /** @var int VPL question activity mode */
    public const VPLQUESTION = 5;
    /** @var array Map of activity modes to their string names for internationalization */
    private const STRINGS = [
        self::NORMAL => 'activity_mode_normal',
        self::EXAMPLE => 'isexample',
        self::NOSTUDENTS => 'activity_mode_no_students',
        self::BASEDON => 'activity_mode_basedon',
        self::STUDENTSREADONLY => 'activity_mode_students_readonly',
        self::VPLQUESTION => 'activity_mode_vplquestion',
    ];
    /** @var array List of activity modes that prevent students from viewing the activity */
    private const PREVENT_SHOW_MODES = [
        self::NOSTUDENTS,
        self::BASEDON,
        self::VPLQUESTION,
    ];

    /** @var array List of activity modes that prevent students from modifying the activity */
    private const PREVENT_MODIFICATION_MODES = [
        self::EXAMPLE,
        self::NOSTUDENTS,
        self::BASEDON,
        self::STUDENTSREADONLY,
        self::VPLQUESTION,
    ];

    /** @var array List of activity modes that prevent students from receiving a grade */
    public const NO_GRADE = [
        self::EXAMPLE,
        self::BASEDON,
    ];

    /** @var array List of activity modes that control if student can view the activity */
    public const CONTROL_VIEW = [
        self::BASEDON,
        self::NOSTUDENTS,
        self::STUDENTSREADONLY,
        self::VPLQUESTION,
    ];

    /**
     * Get the string name for internationalization given activity mode.
     *
     * @param int $mode activity mode
     * @return string string name for internationalization
     */
    public static function get_i18n_key($mode) {
        if (isset(self::STRINGS[$mode])) {
            return self::STRINGS[$mode];
        } else {
            throw new \InvalidArgumentException('Invalid activity mode: ' . $mode);
        }
    }
    /**
     * Return if the activity mode prevents students from viewing the activity.
     *
     * @param int $mode activity mode to check
     * @return bool
     */
    public static function mode_prevents_viewing($mode) {
        return in_array($mode, self::PREVENT_SHOW_MODES, true);
    }

    /**
     * Return if the activity mode prevents students from modifying the activity.
     *
     * @param int $mode activity mode to check
     * @return bool
     */
    public static function mode_prevents_modification($mode) {
        return in_array($mode, self::PREVENT_MODIFICATION_MODES, true);
    }

    /**
     * Updates a vpl instance fields to the mode set as field values.
     * This is used to set the fields according to the mode when creating or updating an instance.
     *
     * @param Object $instance from the form in mod_form
     */
    public static function update_vpl_instance($instance) {
        switch ($instance->activity_mode) {
            case self::EXAMPLE:
                $instance->grade = 0;
                break;
            case self::BASEDON:
                $instance->grade = 0;
                $instance->visible = 0;
                break;
            case self::NOSTUDENTS:
                $instance->visiblegrade = 0;
                $instance->visible = 0;
                break;
            case self::STUDENTSREADONLY:
                $instance->visible = 1;
                $instance->visiblegrade = 1;
                break;
            case self::VPLQUESTION:
                $instance->startdate = 0;
                $instance->duedate = 0;
                $instance->maxfiles = 1000;
                $instance->run = 1;
                $instance->evaluate = 1;
                $instance->visible = 0;
                break;
        }
    }

    /**
     * Check if the caller was vplquestion.
     * Max depth of 4.
     *
     * @return bool
     */
    public static function called_from_vplquestion() {
        $vplquestionpath = '/question/type/vplquestion/';
        // Get the debug backtrace, max depth of 4.
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        if (count($trace) < 2) {
            return false;
        }
        // Remove the first frame which is this function itself.
        array_shift($trace);
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $framepath = str_replace('\\', '/', $frame['file']);
                if (strpos($framepath, $vplquestionpath) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}
