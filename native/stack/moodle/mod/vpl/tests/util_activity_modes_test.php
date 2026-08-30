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
 * Unit tests for activity modes.
 *
 * @package mod_vpl
 * @copyright  Juan Carlos Rodríguez-del-Pino
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Juan Carlos Rodríguez-del-Pino <jcrodriguez@dis.ulpgc.es>
 */

namespace mod_vpl;

use mod_vpl\util\activity_modes;
use mod_vpl\tests\testable_vpl;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vpl/lib.php');
require_once($CFG->dirroot . '/mod/vpl/locallib.php');

/**
 * Unit tests for activity modes.
 *
 * @group mod_vpl
 * @group mod_vpl_util
 * @group mod_vpl_util_activity_modes
 * @covers \mod_vpl\util\activity_modes
 */
final class util_activity_modes_test extends \advanced_testcase {
    /** @var \stdClass Course used in tests. */
    private $course;

    /** @var \stdClass Student user. */
    private $student;

    /** @var \stdClass Teacher user (editing teacher). */
    private $teacher;

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->course = $this->getDataGenerator()->create_course();
        $this->student = $this->getDataGenerator()->create_user();
        $this->teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, 'student');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, 'editingteacher');
    }

    /**
     * Helper to create a VPL instance with a given mode.
     *
     * @param int $mode Activity mode constant.
     * @param array $extra Extra parameters for the instance.
     * @return testable_vpl
     */
    private function create_instance(int $mode, array $extra = []): testable_vpl {
        $params = array_merge([
            'name' => "VPL mode $mode",
            'course' => $this->course->id,
            'activity_mode' => $mode,
            'grade' => 10,
            'visible' => 1,
            'visiblegrade' => 1,
            'duedate' => time() + DAYSECS,
            'maxfiles' => 3,
        ], $extra);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_vpl');
        $instance = $generator->create_instance($params);
        $this->assertEquals($mode, $instance->activity_mode, "Created instance should have mode $mode");
        $cm = get_coursemodule_from_instance(VPL, $instance->id);
        $vpl = new testable_vpl($cm->id);
        $this->assertEquals($mode, $vpl->get_instance()->activity_mode, "Created instance should have mode $mode");
        return $vpl;
    }

    /**
     * Test activity_modes static helper: mode_prevents_viewing.
     */
    public function test_mode_prevents_viewing_static(): void {
        $preventview = [activity_modes::NOSTUDENTS, activity_modes::BASEDON, activity_modes::VPLQUESTION];
        $allowview = [activity_modes::NORMAL, activity_modes::EXAMPLE, activity_modes::STUDENTSREADONLY];

        foreach ($preventview as $mode) {
            $this->assertTrue(
                activity_modes::mode_prevents_viewing($mode),
                "Mode $mode should prevent viewing"
            );
        }
        foreach ($allowview as $mode) {
            $this->assertFalse(
                activity_modes::mode_prevents_viewing($mode),
                "Mode $mode should not prevent viewing"
            );
        }
    }

    /**
     * Test activity_modes static helper: mode_prevents_modification.
     */
    public function test_mode_prevents_modification_static(): void {
        $preventmod = [
            activity_modes::EXAMPLE, activity_modes::NOSTUDENTS, activity_modes::BASEDON,
            activity_modes::STUDENTSREADONLY, activity_modes::VPLQUESTION,
        ];
        $allowmod = [activity_modes::NORMAL];

        foreach ($preventmod as $mode) {
            $this->assertTrue(
                activity_modes::mode_prevents_modification($mode),
                "Mode $mode should prevent modification"
            );
        }
        foreach ($allowmod as $mode) {
            $this->assertFalse(
                activity_modes::mode_prevents_modification($mode),
                "Mode $mode should not prevent modification"
            );
        }
    }

    /**
     * Test activity_modes::get_i18n_key returns valid strings.
     * @covers \mod_vpl\util\activity_modes::get_i18n_key
     */
    public function test_get_i18n_key(): void {
        $modes = [
            activity_modes::NORMAL, activity_modes::EXAMPLE, activity_modes::NOSTUDENTS,
            activity_modes::BASEDON, activity_modes::STUDENTSREADONLY,
            activity_modes::VPLQUESTION,
        ];
        foreach ($modes as $mode) {
            $name = activity_modes::get_i18n_key($mode);
            $this->assertNotEmpty($name);
            $this->assertIsString($name);
        }
    }

    /**
     * Test activity_modes::get_i18n_key throws exception for invalid mode.
     * @covers \mod_vpl\util\activity_modes::get_i18n_key
     */
    public function test_get_i18n_key_invalid(): void {
        $this->expectException(\InvalidArgumentException::class);
        activity_modes::get_i18n_key(99);
    }

    /**
     * Test vpl_update_mode sets grade to 0 for EXAMPLE mode.
     * @covers \mod_vpl\util\activity_modes::update_vpl_instance
     */
    public function test_update_mode_example(): void {
        $instance = (object)['activity_mode' => activity_modes::EXAMPLE, 'grade' => 10, 'visible' => 1];
        activity_modes::update_vpl_instance($instance);
        $this->assertEquals(0, $instance->grade);
    }

    /**
     * Test vpl_update_mode sets grade=0, visible=0 for BASEDON mode.
     * @covers \mod_vpl\util\activity_modes::update_vpl_instance
     */
    public function test_update_mode_basedon(): void {
        $instance = (object)['activity_mode' => activity_modes::BASEDON, 'grade' => 10, 'visible' => 1];
        activity_modes::update_vpl_instance($instance);
        $this->assertEquals(0, $instance->grade);
        $this->assertEquals(0, $instance->visible);
    }

    /**
     * Test vpl_update_mode sets visiblegrade=0, visible=0 for NOSTUDENTS mode.
     * @covers \mod_vpl\util\activity_modes::update_vpl_instance
     */
    public function test_update_mode_nostudents(): void {
        $instance = (object)['activity_mode' => activity_modes::NOSTUDENTS, 'visiblegrade' => 1, 'visible' => 1];
        activity_modes::update_vpl_instance($instance);
        $this->assertEquals(0, $instance->visiblegrade);
        $this->assertEquals(0, $instance->visible);
    }

    /**
     * Test vpl_update_mode sets visible=1, visiblegrade=1 for STUDENTSREADONLY mode.
     * @covers \mod_vpl\util\activity_modes::update_vpl_instance
     */
    public function test_update_mode_studentsreadonly(): void {
        $instance = (object)['activity_mode' => activity_modes::STUDENTSREADONLY, 'visible' => 0, 'visiblegrade' => 0];
        activity_modes::update_vpl_instance($instance);
        $this->assertEquals(1, $instance->visible);
        $this->assertEquals(1, $instance->visiblegrade);
    }

    /**
     * Test update_vpl_instance for VPLQUESTION mode.
     * @covers \mod_vpl\util\activity_modes::update_vpl_instance
     */
    public function test_update_mode_vplquestion(): void {
        $instance = (object)[
            'activity_mode' => activity_modes::VPLQUESTION,
            'startdate' => time(),
            'duedate' => time(),
            'maxfiles' => 1,
            'run' => 0,
            'evaluate' => 0,
            'visible' => 1,
        ];
        activity_modes::update_vpl_instance($instance);
        $this->assertEquals(0, $instance->startdate);
        $this->assertEquals(0, $instance->duedate);
        $this->assertEquals(1000, $instance->maxfiles);
        $this->assertEquals(1, $instance->run);
        $this->assertEquals(1, $instance->evaluate);
        $this->assertEquals(0, $instance->visible);
    }

    /**
     * Test vpl_update_mode does not modify NORMAL mode instance.
     * @covers \mod_vpl\util\activity_modes::update_vpl_instance
     */
    public function test_update_mode_normal(): void {
        $instance = (object)['activity_mode' => activity_modes::NORMAL, 'grade' => 10, 'visible' => 1, 'visiblegrade' => 1];
        $clone = clone $instance;
        activity_modes::update_vpl_instance($instance);
        $this->assertEquals($clone, $instance);
    }

    /**
     * Test is_mode and is_example on VPL instances.
     * @covers \mod_vpl\util\activity_modes::is_mode
     * @covers \mod_vpl\util\activity_modes::is_example
     */
    public function test_is_mode_and_is_example(): void {
        $vplnormal = $this->create_instance(activity_modes::NORMAL);
        $this->assertTrue($vplnormal->is_mode(activity_modes::NORMAL));
        $this->assertFalse($vplnormal->is_example());

        $vplexample = $this->create_instance(activity_modes::EXAMPLE);
        $this->assertTrue($vplexample->is_mode(activity_modes::EXAMPLE));
        $this->assertTrue($vplexample->is_example());
    }

    /**
     * Test is_vpl_question_mode on VPL instances.
     * @covers \mod_vpl\util\activity_modes::is_vpl_question_mode
     */
    public function test_is_vpl_question_mode(): void {
        $vplq = $this->create_instance(activity_modes::VPLQUESTION);
        $this->assertTrue($vplq->is_vpl_question_mode());

        $vplnormal = $this->create_instance(activity_modes::NORMAL);
        $this->assertFalse($vplnormal->is_vpl_question_mode());
    }

    /**
     * Test mode_prevents_viewing on VPL instance for students vs teachers.
     * @covers \mod_vpl\util\activity_modes::mode_prevents_viewing
     */
    public function test_instance_mode_prevents_viewing(): void {
        $this->setUser($this->student);

        $vplnormal = $this->create_instance(activity_modes::NORMAL);
        $this->assertFalse($vplnormal->mode_prevents_viewing($this->student->id));

        $vplnostudents = $this->create_instance(activity_modes::NOSTUDENTS);
        $this->assertTrue($vplnostudents->mode_prevents_viewing($this->student->id));
        $this->assertFalse($vplnostudents->mode_prevents_viewing($this->teacher->id));

        $vplbasedon = $this->create_instance(activity_modes::BASEDON);
        $this->assertTrue($vplbasedon->mode_prevents_viewing($this->student->id));
        $this->assertFalse($vplbasedon->mode_prevents_viewing($this->teacher->id));

        $vplq = $this->create_instance(activity_modes::VPLQUESTION);
        $this->assertTrue($vplq->mode_prevents_viewing($this->student->id));
        $this->assertFalse($vplq->mode_prevents_viewing($this->teacher->id));
    }

    /**
     * Test mode_prevents_modification on VPL instance for students vs teachers.
     * @covers \mod_vpl\util\activity_modes::mode_prevents_modification
     */
    public function test_instance_mode_prevents_modification(): void {
        $this->setUser($this->student);

        $vplnormal = $this->create_instance(activity_modes::NORMAL);
        $this->assertFalse($vplnormal->mode_prevents_modification($this->student->id));

        $vplexample = $this->create_instance(activity_modes::EXAMPLE);
        $this->assertTrue($vplexample->mode_prevents_modification($this->student->id));
        $this->assertFalse($vplexample->mode_prevents_modification($this->teacher->id));

        $vplreadonly = $this->create_instance(activity_modes::STUDENTSREADONLY);
        $this->assertTrue($vplreadonly->mode_prevents_modification($this->student->id));
        $this->assertFalse($vplreadonly->mode_prevents_modification($this->teacher->id));

        $vplnostudents = $this->create_instance(activity_modes::NOSTUDENTS);
        $this->assertTrue($vplnostudents->mode_prevents_modification($this->student->id));
        $this->assertFalse($vplnostudents->mode_prevents_modification($this->teacher->id));
    }

    /**
     * Test NO_GRADE constant lists correct modes.
     * @covers \mod_vpl\util\activity_modes::NO_GRADE
     */
    public function test_no_grade_modes(): void {
        $expected = [
            activity_modes::EXAMPLE,
            activity_modes::BASEDON,
        ];
        $this->assertEquals($expected, activity_modes::NO_GRADE);
    }

    /**
     * Test CONTROL_VIEW constant lists correct modes.
     * @covers \mod_vpl\util\activity_modes::CONTROL_VIEW
     */
    public function test_control_view_modes(): void {
        $expected = [
            activity_modes::BASEDON,
            activity_modes::NOSTUDENTS,
            activity_modes::STUDENTSREADONLY,
            activity_modes::VPLQUESTION,
        ];
        $this->assertEquals($expected, activity_modes::CONTROL_VIEW);
    }

    /**
     * Test is_visible respects mode for students and teachers.
     * @covers \mod_vpl::is_visible
     */
    public function test_is_visible_by_mode(): void {
        // NORMAL mode: student can see.
        $vplnormal = $this->create_instance(activity_modes::NORMAL);
        $this->setUser($this->student);
        $this->assertTrue($vplnormal->is_visible($this->student->id));

        // STUDENTSREADONLY mode: student can see.
        $vplreadonly = $this->create_instance(activity_modes::STUDENTSREADONLY);
        $this->assertTrue($vplreadonly->is_visible($this->student->id));

        // NOSTUDENTS mode: student cannot see, teacher can.
        $vplnostudents = $this->create_instance(activity_modes::NOSTUDENTS);
        $this->assertFalse($vplnostudents->is_visible($this->student->id));
        $this->assertTrue($vplnostudents->is_visible($this->teacher->id));
    }

    /**
     * Test is_submit_able respects mode for students and teachers.
     * @covers \mod_vpl::is_submit_able
     */
    public function test_is_submit_able_by_mode(): void {
        // NORMAL mode: student can submit.
        $vplnormal = $this->create_instance(activity_modes::NORMAL);
        $this->setUser($this->student);
        $this->assertTrue($vplnormal->is_submit_able($this->student->id));

        // EXAMPLE mode: student cannot submit, teacher can.
        $vplexample = $this->create_instance(activity_modes::EXAMPLE);
        $this->assertFalse($vplexample->is_submit_able($this->student->id));
        $this->assertTrue($vplexample->is_submit_able($this->teacher->id));

        // STUDENTSREADONLY mode: student cannot submit, teacher can.
        $vplreadonly = $this->create_instance(activity_modes::STUDENTSREADONLY);
        $this->assertFalse($vplreadonly->is_submit_able($this->student->id));
        $this->assertTrue($vplreadonly->is_submit_able($this->teacher->id));
    }
}
