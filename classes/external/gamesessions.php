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

namespace mod_millionaire\external;

use external_api;
use external_function_parameters;
use external_value;
use mod_millionaire\external\exporter\gamesession_dto;
use mod_millionaire\external\exporter\question_dto;
use mod_millionaire\model\game;
use mod_millionaire\model\gamesession;
use mod_millionaire\model\question;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * Class gamesessions
 *
 * @package    mod_millionaire\external
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gamesessions extends external_api {

    /**
     * Definition of parameters for {@see get_current_gamesession}.
     *
     * @return external_function_parameters
     */
    public static function get_current_gamesession_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
        ]);
    }

    /**
     * Definition of return type for {@see get_current_gamesession}.
     *
     * @return \external_single_structure
     */
    public static function get_current_gamesession_returns() {
        return gamesession_dto::get_read_structure();
    }

    /**
     * Get current gamesession.
     *
     * @param int $coursemoduleid
     *
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function get_current_gamesession($coursemoduleid) {
        $params = ['coursemoduleid' => $coursemoduleid];
        self::validate_parameters(self::get_current_gamesession_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game_data = $DB->get_record('millionaire', ['id' => $coursemodule->instance]);
        $game = new game();
        $game->apply($game_data);

        // try to find existing in-progress gamesession or create a new one
        $gamesession = self::get_or_create_gamesession($coursemodule, $game);
        $exporter = new gamesession_dto($gamesession, $ctx);
        return $exporter->export($renderer);
    }

    /**
     * Definition of parameters for {@see get_current_question}.
     *
     * @return external_function_parameters
     */
    public static function get_current_question_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
            'gamesessionid' => new external_value(PARAM_INT, 'game session id')
        ]);
    }

    /**
     * Definition of return type for {@see get_current_question}.
     *
     * @return \external_single_structure
     */
    public static function get_current_question_returns() {
        return question_dto::get_read_structure();
    }

    /**
     * Get current question. Selects a new one, if none is currently selected.
     *
     * @param int $coursemoduleid
     * @param int $gamesessionid
     *
     * @return \stdClass
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function get_current_question($coursemoduleid, $gamesessionid) {
        $params = ['coursemoduleid' => $coursemoduleid, 'gamesessionid' => $gamesessionid];
        self::validate_parameters(self::get_current_question_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game_data = $DB->get_record('millionaire', ['id' => $coursemodule->instance]);
        $game = new game();
        $game->apply($game_data);

        // try to find existing in-progress gamesession or create a new one
        $gamesession = self::get_or_create_gamesession($coursemodule, $game);

        // grab the most recent level in that gamesession
        $level = $gamesession->get_current_level();
        if ($level === null) {
            $question = new question();
        } else {
            // get question or create a new one if necessary.
            $question = $gamesession->get_question_by_level($level->get_id());
            if (!$gamesession->is_level_finished($level->get_id()) && $question === null) {
                $question = new question();
                $question->set_gamesession($gamesessionid);
                $question->set_level($level->get_id());
                $question->set_mdl_question($level->get_random_question()->id);
                $question->save();
            }
        }

        // return
        $exporter = new question_dto($question, $ctx);
        return $exporter->export($renderer);
    }

    /**
     * Definition of parameters for {@see submit_answer}.
     *
     * @return external_function_parameters
     */
    public static function submit_answer_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
            'gamesessionid' => new external_value(PARAM_INT, 'game session id'),
            'levelid' => new external_value(PARAM_INT, 'level id'),
            'questionid' => new external_value(PARAM_INT, 'question id'),
            'mdlanswerid' => new external_value(PARAM_INT, 'id of the selected moodle answer'),
        ]);
    }

    /**
     * Definition of return type for {@see submit_answer}.
     *
     * @return \external_single_structure
     */
    public static function submit_answer_returns() {
        return question_dto::get_read_structure();
    }

    /**
     * Applies the submitted answer to the question record in our DB.
     *
     * @param int $coursemoduleid
     * @param int $gamesessionid
     * @param int $levelid
     * @param int $questionid
     * @param int $mdlanswerid
     *
     * @return \stdClass
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function submit_answer($coursemoduleid, $gamesessionid, $levelid, $questionid, $mdlanswerid) {
        $params = [
            'coursemoduleid' => $coursemoduleid,
            'gamesessionid' => $gamesessionid,
            'levelid' => $levelid,
            'questionid' => $questionid,
            'mdlanswerid' => $mdlanswerid,
        ];
        self::validate_parameters(self::submit_answer_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game_data = $DB->get_record('millionaire', ['id' => $coursemodule->instance]);
        $game = new game();
        $game->apply($game_data);

        // try to find existing in-progress gamesession or create a new one
        $gamesession = self::get_or_create_gamesession($coursemodule, $game);
        if (!$gamesession->is_in_progress()) {
            throw new \moodle_exception('gamesession is not available anymore.');
        }

        // try to find level and validate against input data
        $level = $gamesession->get_current_level();
        if ($level->get_id() !== $levelid) {
            throw new \invalid_parameter_exception('inconsistent input data. given level is not the current level of this gamesession.');
        }

        // try to find question and validate against input data
        $question = $gamesession->get_question_by_level($levelid);
        if ($question === null || $question->get_id() !== $questionid) {
            throw new \invalid_parameter_exception('inconsistent input data. question doesn\'t belong to this gamesession.');
        }
        if ($question->is_finished()) {
            throw new \moodle_exception('question has already been answered.');
        }
        $mdl_question = $question->get_mdl_question_ref();
        if (!property_exists($mdl_question, 'answers')) {
            throw new \coding_exception('property »answers« doesn\'t exist on the moodle question with id ' . $question->get_mdl_question() . '.');
        }

        // submit the answer
        $correct_mdl_answers = \array_filter(
            $mdl_question->answers,
            function (\question_answer $mdlanswer) {
                return $mdlanswer->fraction == 1;
            }
        );
        if (count($correct_mdl_answers) !== 1) {
            throw new \moodle_exception('The moodle question with id ' . $question->get_mdl_question() . ' seems to be unapplicable for this activity.');
        }
        $correct_mdl_answer = \array_pop($correct_mdl_answers);
        \assert($correct_mdl_answer instanceof \question_answer);
        $question->set_mdl_answer($mdlanswerid);
        $question->set_finished(true);
        $question->set_correct($correct_mdl_answer->id == $mdlanswerid);
        if ($question->is_correct()) {
            $question->set_score($level->get_score());
        } else {
            if ($gamesession->is_continue_on_failure()) {
                // find the level that represents the reached score and set those points
                if ($gamesession->get_answers_correct() === 0) {
                    $question->set_score(0);
                } else {
                    $level = $gamesession->get_level_by_index($gamesession->get_answers_correct());
                    $question->set_score($level->get_score());
                }
            } else {
                // game over! find last reached safe spot and set those points
                $safe_spot_level = $gamesession->find_reached_safe_spot_level();
                $question->set_score($safe_spot_level->get_score());
                $gamesession->set_state(gamesession::STATE_FINISHED);
            }
        }
        $question->save();

        // update stats in the gamesession
        $gamesession->set_score($question->get_score());
        $gamesession->increment_answers_total();
        if ($question->is_correct()) {
            $gamesession->increment_answers_correct();
        }
        if ($gamesession->get_answers_total() === $game->count_active_levels()) {
            $gamesession->set_state(gamesession::STATE_FINISHED);
        }
        $gamesession->save();

        // return result object
        $exporter = new question_dto($question, $ctx);
        return $exporter->export($renderer);
    }

    /**
     * Gets or creates a gamesession for the current user.
     *
     * @param \stdClass $coursemodule
     * @param game $game
     *
     * @return gamesession
     * @throws \dml_exception
     */
    private static function get_or_create_gamesession($coursemodule, game $game) {
        global $DB, $USER;
        // try to find existing in-progress gamesession
        $record = $DB->get_record('millionaire_gamesessions', [
            'game' => $coursemodule->instance,
            'mdl_user' => $USER->id,
            'state' => gamesession::STATE_PROGRESS,
        ]);
        // get or create gamesession
        $gamesession = new gamesession();
        if ($record === false) {
            $gamesession->set_game($coursemodule->instance);
            $gamesession->set_mdl_user($USER->id);
            $gamesession->set_continue_on_failure($game->is_continue_on_failure());
            $gamesession->save();
        } else {
            $gamesession->apply($record);
        }
        return $gamesession;
    }
}
