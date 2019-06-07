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
     * Definition of parameters for {@see create_gamesession}.
     *
     * @return external_function_parameters
     */
    public static function create_gamesession_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
        ]);
    }

    /**
     * Definition of return type for {@see create_gamesession}.
     *
     * @return \external_single_structure
     */
    public static function create_gamesession_returns() {
        return gamesession_dto::get_read_structure();
    }

    /**
     * Dumps all previous game sessions of the current user and returns a fresh one.
     *
     * @param int $coursemoduleid
     *
     * @return \stdClass
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function create_gamesession($coursemoduleid) {
        $params = ['coursemoduleid' => $coursemoduleid];
        self::validate_parameters(self::create_gamesession_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = self::get_game($coursemodule);

        // dump existing game sessions
        self::dump_running_gamesessions($game);

        // create a new one
        $gamesession = self::insert_gamesession($game);

        // return it
        $exporter = new gamesession_dto($gamesession, $ctx);
        return $exporter->export($renderer);
    }

    /**
     * Definition of parameters for {@see close_gamesession}.
     *
     * @return external_function_parameters
     */
    public static function close_gamesession_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
            'gamesessionid' => new external_value(PARAM_INT, 'game session id'),
        ]);
    }

    /**
     * Definition of return type for {@see close_gamesession}.
     *
     * @return \external_single_structure
     */
    public static function close_gamesession_returns() {
        return gamesession_dto::get_read_structure();
    }

    /**
     * Sets the state of the given game session to FINISHED.
     *
     * @param int $coursemoduleid
     * @param int $gamesessionid
     *
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function close_gamesession($coursemoduleid, $gamesessionid) {
        $params = ['coursemoduleid' => $coursemoduleid, 'gamesessionid' => $gamesessionid];
        self::validate_parameters(self::close_gamesession_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $USER;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = self::get_game($coursemodule);

        // get gamesession by the provided id
        $gamesession = new gamesession();
        $gamesession->load_data_by_id($gamesessionid);
        if (\intval($gamesession->get_mdl_user()) !== \intval($USER->id)) {
            throw new \invalid_parameter_exception("Game session doesn't belong to current user.");
        }
        if ($gamesession->is_in_progress()) {
            $gamesession->set_state(gamesession::STATE_FINISHED);
            $gamesession->set_won(true);
            $gamesession->save();
        }

        // return the changed game session
        $exporter = new gamesession_dto($gamesession, $ctx);
        return $exporter->export($renderer);
    }

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

        global $PAGE;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = self::get_game($coursemodule);

        // try to find existing in-progress gamesession or create a new one
        $gamesession = self::get_or_create_gamesession($game);
        $exporter = new gamesession_dto($gamesession, $ctx);
        return $exporter->export($renderer);
    }

    /**
     * Definition of parameters for {@see get_current_question}.
     *
     * @return external_function_parameters
     */
    public static function get_question_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
            'gamesessionid' => new external_value(PARAM_INT, 'game session id'),
            'levelindex' => new external_value(PARAM_INT, 'index of the level'),
        ]);
    }

    /**
     * Definition of return type for {@see get_question}.
     *
     * @return \external_single_structure
     */
    public static function get_question_returns() {
        return question_dto::get_read_structure();
    }

    /**
     * Get current question. Selects a new one, if none is currently selected.
     *
     * @param int $coursemoduleid
     * @param int $gamesessionid
     * @param int $levelindex
     *
     * @return \stdClass
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function get_question($coursemoduleid, $gamesessionid, $levelindex) {
        $params = [
            'coursemoduleid' => $coursemoduleid,
            'gamesessionid' => $gamesessionid,
            'levelindex' => $levelindex,
        ];
        self::validate_parameters(self::get_question_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = self::get_game($coursemodule);

        // try to find existing in-progress gamesession or create a new one
        $gamesession = self::get_or_create_gamesession($game);

        // grab the requested level
        $level = $gamesession->get_level_by_index($levelindex);

        // get question or create a new one if necessary.
        $question = $gamesession->get_question_by_level($level->get_id());
        if (!$gamesession->is_level_finished($level->get_id()) && $question === null) {
            $question = new question();
            $question->set_gamesession($gamesessionid);
            $question->set_level($level->get_id());
            $question->set_mdl_question($level->get_random_question()->id);
            $question->save();
        }

        // return
        $exporter = new question_dto($question, $level, $ctx);
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
        $game = self::get_game($coursemodule);

        // try to find existing in-progress gamesession or create a new one
        $gamesession = self::get_or_create_gamesession($game);
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
            // set to finished
            $gamesession->set_state(gamesession::STATE_FINISHED);
            // determine if user won
            $gamesession->set_won($gamesession->is_continue_on_failure() || $question->is_correct());
        }
        $gamesession->save();

        // return result object
        $exporter = new question_dto($question, $level, $ctx);
        return $exporter->export($renderer);
    }

    /**
     * Gets the game instance from the database.
     *
     * @param \cm_info $coursemodule
     *
     * @return game
     * @throws \dml_exception
     */
    private static function get_game(\cm_info $coursemodule) {
        global $DB;
        $game_data = $DB->get_record('millionaire', ['id' => $coursemodule->instance]);
        $game = new game();
        $game->apply($game_data);
        return $game;
    }

    /**
     * Gets or creates a gamesession for the current user. Allowed existing gamesessions are either in state
     * PROGRESS or FINISHED.
     *
     * @param game $game
     *
     * @return gamesession
     * @throws \dml_exception
     */
    private static function get_or_create_gamesession(game $game) {
        global $DB, $USER;
        // try to find existing in-progress or finished gamesession
        $sql = "
            SELECT *
              FROM {millionaire_gamesessions}
             WHERE game = ? AND mdl_user = ? AND state IN (?, ?)
          ORDER BY timemodified DESC
        ";
        $params = [
            $game->get_id(),
            $USER->id,
            gamesession::STATE_PROGRESS,
            gamesession::STATE_FINISHED,
        ];
        $record = $DB->get_record_sql($sql, $params);
        // get or create gamesession
        if ($record === false) {
            $gamesession = self::insert_gamesession($game);
        } else {
            $gamesession = new gamesession();
            $gamesession->apply($record);
        }
        return $gamesession;
    }

    /**
     * Closes all game sessions of the current user, which are in state 'progress'.
     *
     * @param game $game
     *
     * @return void
     * @throws \dml_exception
     */
    private static function dump_running_gamesessions(game $game) {
        global $DB, $USER;
        $conditions = [
            'game' => $game->get_id(),
            'mdl_user' => $USER->id,
            'state' => gamesession::STATE_PROGRESS,
        ];
        $gamesession = new gamesession();
        $DB->set_field($gamesession->get_table_name(), 'state', $gamesession::STATE_DUMPED, $conditions);
    }

    /**
     * Inserts a new game session into the DB (for the current user).
     *
     * @param game $game
     *
     * @return gamesession
     * @throws \dml_exception
     */
    private static function insert_gamesession(game $game) {
        global $USER;
        $gamesession = new gamesession();
        $gamesession->set_game($game->get_id());
        $gamesession->set_mdl_user($USER->id);
        $gamesession->set_continue_on_failure($game->is_continue_on_failure());
        $gamesession->save();
        return $gamesession;
    }
}
