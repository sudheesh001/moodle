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
 * Quiz attempt walk through tests.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Quiz attempt walk through.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_attempt_walkthrough_testcase extends advanced_testcase {

    /**
     * Create a quiz with questions and walk through a quiz attempt.
     */
    public function test_quiz_attempt_walkthrough() {
        global $SITE;

        $this->resetAfterTest(true);

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(array('course'=>$SITE->id, 'questionsperpage' => 0, 'grade' => 100.0,
                                                      'sumgrades' => 2));

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        $quizobj = quiz::create($quiz->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);

        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $prefix1 = $quba->get_field_prefix(1);
        $prefix2 = $quba->get_field_prefix(2);

        $tosubmit = array();
        $tosubmit['slots'] = '1,2';
        $tosubmit[$prefix1.':sequencecheck'] = 1;
        $tosubmit[$prefix1.'answer'] = 'frog';

        $tosubmit[$prefix2.':sequencecheck'] = 1;
        $tosubmit[$prefix2.'answer'] = '3.14';

        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_finish($timenow, false);

        // Re-load quiz attempt data.
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check that results are stored as expected.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(2, $attemptobj->get_sum_marks());
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());

        // Check quiz grades.
        $grades = quiz_get_user_grades($quiz, $user1->id);
        $grade = array_shift($grades);
        $this->assertEquals(100.0, $grade->rawgrade);

        // Check grade book.
        $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $quiz->id, $user1->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $gradebookgrade = array_shift($gradebookitem->grades);
        $this->assertEquals(100, $gradebookgrade->grade);
    }
}
