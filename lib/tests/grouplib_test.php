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

/**
 * Tests events subsystems
 *
 * @package    core
 * @subpackage group
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class grouplib_testcase extends advanced_testcase {

    public function test_groups_get_group_by_idnumber() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $idnumber1 = 'idnumber1';
        $idnumber2 = 'idnumber2';

        /**
         * Test with an empty and a null idnumber
         */
        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        // Even when a group exists which also has an empty idnumber
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        /**
         * Test with a valid idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber1));

        // We should now have a valid group returned by the idnumber search
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber1), $group);

        // An empty idnumber should still return false
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        /**
         * Test with another idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber2));

        // We should now have a valid group returned by the idnumber search
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber2), $group);

        /**
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course
         */

        // Create a second course
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        // Our existing idnumbers shouldn't be returned here as we're in a different course
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber1));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber2));

        // We should be able to reuse the idnumbers again since this is a different course
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber1), $group);

        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_group_by_idnumber($course->id, $idnumber2), $group);
    }

    public function test_groups_get_grouping_by_idnumber() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $idnumber1 = 'idnumber1';
        $idnumber2 = 'idnumber2';

        /**
         * Test with an empty and a null idnumber
         */
        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        // Even when a grouping exists which also has an empty idnumber
        $generator->create_grouping(array('courseid' => $course->id));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        /**
         * Test with a valid idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber1));

        // We should now have a valid group returned by the idnumber search
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber1), $grouping);

        // An empty idnumber should still return false
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        /**
         * Test with another idnumber
         */
        // There is no matching idnumber at present
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber2));

        // We should now have a valid grouping returned by the idnumber search
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber2), $grouping);

        /**
         * Grouping idnumbers are unique within a course so test that we don't
         * retrieve groupings for the first course
         */

        // Create a second course
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty idnumber should always return a false value
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        // Our existing idnumbers shouldn't be returned here as we're in a different course
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber1));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber2));

        // We should be able to reuse the idnumbers again since this is a different course
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber1), $grouping);

        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals(groups_get_grouping_by_idnumber($course->id, $idnumber2), $grouping);
    }

    public function test_groups_get_group_by_name() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $name1 = 'Name 1';
        $name2 = 'Name 2';

        // Test with an empty and a null idnumber
        $this->assertFalse(groups_get_group_by_name($course->id, ''));
        $this->assertFalse(groups_get_group_by_name($course->id, null));

        // Even when a group exists.
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_group_by_name($course->id, ''));
        $this->assertFalse(groups_get_group_by_name($course->id, null));

        // Test with a valid name, but one that doesn't exist yet.
        $this->assertFalse(groups_get_group_by_name($course->id, $name1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name2));

        // We should now have a valid group returned by the name search.
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals(groups_get_group_by_name($course->id, $name1), $group1->id);
        $this->assertFalse(groups_get_group_by_name($course->id, $name2));

        // We should now have a two valid groups returned by the name search.
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals(groups_get_group_by_name($course->id, $name1), $group1->id);
        $this->assertEquals(groups_get_group_by_name($course->id, $name2), $group2->id);

        // Delete a group.
        $this->assertTrue(groups_delete_group($group1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name1));
        $this->assertEquals(groups_get_group_by_name($course->id, $name2), $group2->id);

        /**
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course
         */

        // Create a second course
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty name should always return a false value
        $this->assertFalse(groups_get_group_by_name($course->id, ''));
        $this->assertFalse(groups_get_group_by_name($course->id, null));

        // Our existing names shouldn't be returned here as we're in a different course
        $this->assertFalse(groups_get_group_by_name($course->id, $name1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name2));

        // We should be able to reuse the idnumbers again since this is a different course
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals(groups_get_group_by_name($course->id, $name1), $group1->id);

        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals(groups_get_group_by_name($course->id, $name2), $group2->id);
    }

    public function test_groups_get_grouping() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $name1 = 'Grouping 1';
        $name2 = 'Grouping 2';

        // Test with an empty and a null idnumber
        $this->assertFalse(groups_get_grouping_by_name($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_name($course->id, null));

        // Even when a group exists.
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_grouping_by_name($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_name($course->id, null));

        // Test with a valid name, but one that doesn't exist yet.
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name2));

        // We should now have a valid group returned by the name search.
        $group1 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals(groups_get_grouping_by_name($course->id, $name1), $group1->id);
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name2));

        // We should now have a two valid groups returned by the name search.
        $group2 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals(groups_get_grouping_by_name($course->id, $name1), $group1->id);
        $this->assertEquals(groups_get_grouping_by_name($course->id, $name2), $group2->id);

        // Delete a group.
        $this->assertTrue(groups_delete_grouping($group1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name1));
        $this->assertEquals(groups_get_grouping_by_name($course->id, $name2), $group2->id);

        /**
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course
         */

        // Create a second course
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty name should always return a false value
        $this->assertFalse(groups_get_grouping_by_name($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_name($course->id, null));

        // Our existing names shouldn't be returned here as we're in a different course
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name2));

        // We should be able to reuse the idnumbers again since this is a different course
        $group1 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals(groups_get_grouping_by_name($course->id, $name1), $group1->id);

        $group2 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals(groups_get_grouping_by_name($course->id, $name2), $group2->id);
    }

    public function test_groups_get_course_data() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $grouping1 = $generator->create_grouping(array('courseid' => $course->id, 'name' => 'Grouping 1'));
        $grouping2 = $generator->create_grouping(array('courseid' => $course->id, 'name' => 'Grouping 2'));
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 1'));
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 2'));
        $group3 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 3'));
        $group4 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 4'));

        // Assign the groups to groupings.
        $this->assertTrue(groups_assign_grouping($grouping1->id, $group1->id));
        $this->assertTrue(groups_assign_grouping($grouping1->id, $group2->id));
        $this->assertTrue(groups_assign_grouping($grouping2->id, $group3->id));
        $this->assertTrue(groups_assign_grouping($grouping2->id, $group4->id));

        // Get the data.
        $data = groups_get_course_data($course->id);
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('groups', $data);
        $this->assertObjectHasAttribute('groupings', $data);
        $this->assertObjectHasAttribute('mappings', $data);

        // Test we have the expected items returns.
        $this->assertCount(4, $data->groups);
        $this->assertCount(2, $data->groupings);
        $this->assertCount(4, $data->mappings);

        // Check we have the expected groups.
        $this->assertContains($group1->id, array_keys($data->groups));
        $this->assertContains($group2->id, array_keys($data->groups));
        $this->assertContains($group3->id, array_keys($data->groups));
        $this->assertContains($group4->id, array_keys($data->groups));

        // Test a group-id is mapped correctly.
        $this->assertEquals($group3->name, $data->groups[$group3->id]->name);

        // Check we have the expected number of groupings.
        $this->assertContains($grouping1->id, array_keys($data->groupings));
        $this->assertContains($grouping2->id, array_keys($data->groupings));

        // Test a grouping-id is mapped correctly.
        $this->assertEquals($grouping2->name, $data->groupings[$grouping2->id]->name);

        // Test that all of the mappings are correct.
        $grouping1maps = 0;
        $grouping2maps = 0;
        $group1maps = 0;
        $group2maps = 0;
        $group3maps = 0;
        $group4maps = 0;
        foreach ($data->mappings as $mapping) {
            if ($mapping->groupingid === $grouping1->id) {
                $grouping1maps++;
                $this->assertContains($mapping->groupid, array($group1->id, $group2->id));
            } else if ($mapping->groupingid === $grouping2->id) {
                $grouping2maps++;
                $this->assertContains($mapping->groupid, array($group3->id, $group4->id));
            } else {
                $this->fail('Unexpected groupingid');
            }
            switch ($mapping->groupid) {
                case $group1->id : $group1maps++; break;
                case $group2->id : $group2maps++; break;
                case $group3->id : $group3maps++; break;
                case $group4->id : $group4maps++; break;
            }
        }
        $this->assertEquals(2, $grouping1maps);
        $this->assertEquals(2, $grouping2maps);
        $this->assertEquals(1, $group1maps);
        $this->assertEquals(1, $group2maps);
        $this->assertEquals(1, $group3maps);
        $this->assertEquals(1, $group4maps);

        // Test the groups_get_all_groups which uses this functionality.
        $groups  = groups_get_all_groups($course->id);
        $groupkeys = array_keys($groups);
        $this->assertCount(4, $groups);
        $this->assertContains($group1->id, $groupkeys);
        $this->assertContains($group2->id, $groupkeys);
        $this->assertContains($group3->id, $groupkeys);
        $this->assertContains($group4->id, $groupkeys);

        $groups  = groups_get_all_groups($course->id, null, $grouping1->id);
        $groupkeys = array_keys($groups);
        $this->assertCount(2, $groups);
        $this->assertContains($group1->id, $groupkeys);
        $this->assertContains($group2->id, $groupkeys);
        $this->assertNotContains($group3->id, $groupkeys);
        $this->assertNotContains($group4->id, $groupkeys);

        $groups  = groups_get_all_groups($course->id, null, $grouping2->id);
        $groupkeys = array_keys($groups);
        $this->assertCount(2, $groups);
        $this->assertNotContains($group1->id, $groupkeys);
        $this->assertNotContains($group2->id, $groupkeys);
        $this->assertContains($group3->id, $groupkeys);
        $this->assertContains($group4->id, $groupkeys);
    }

    /**
     * Tests for groups_group_visible.
     */
    public function test_groups_group_visible() {
        global $CFG, $DB;

        $generator = $this->getDataGenerator();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course category, course and groups.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $coursecontext = context_course::instance($course->id);
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 1'));
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 2'));
        $group3 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 3'));
        $group4 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 4'));

        // Create cm.
        $assign = $generator->create_module("assign", array('course' => $course->id));
        $cm = get_coursemodule_from_instance("assign", $assign->id);

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Enrol users into the course.
        $generator->enrol_user($user1->id, $course->id);
        $generator->enrol_user($user2->id, $course->id);

        // Assign groups.
        groups_add_member($group1, $user2);

        // Give capability at course level to the user to access all groups.
        $role = $DB->get_field("role", "id", array("shortname" => "manager"));
        $generator->enrol_user($user3->id, $course->id, $role);
        // Make sure the user has the capability.
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $role, $coursecontext->id);

        // No groups , not forced.
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // No groups, forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, not forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertFalse($result);
        $result = groups_group_visible($group1->id, $course, null, $user2->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user2->id);
        $this->assertFalse($result); // Requesting all groups.
        $result = groups_group_visible(0, $course, null, $user3->id);
        $this->assertTrue($result); // Requesting all groups.
        $result = groups_group_visible($group1->id, $course, null, $user3->id);
        $this->assertTrue($result); // Make sure user with access to all groups can see any group.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user3->id);
        $this->assertTrue($result); // Make sure user with access to all groups can see any group.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with visible groups.

        // Separate groups, not forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertFalse($result);
        $result = groups_group_visible($group1->id, $course, null, $user2->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user2->id);
        $this->assertFalse($result); // Requesting all groups.
        $result = groups_group_visible(0, $course, null, $user3->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.
    }
}
