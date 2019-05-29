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
use mod_millionaire\model\level;
use mod_millionaire\model\question;

defined('MOODLE_INTERNAL') || die();

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

        global $PAGE, $DB, $USER;
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
            'state' => 'progress'
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
