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

use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use function in_array;
use invalid_parameter_exception;
use mod_millionaire\external\exporter\joker_dto;
use mod_millionaire\model\joker;
use mod_millionaire\util;
use moodle_exception;
use restricted_context_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class jokers
 *
 * @package    mod_millionaire\external
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jokers extends external_api {

    /**
     * Definition of parameters for {@see get_used_jokers}.
     *
     * @return external_function_parameters
     */
    public static function get_used_jokers_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
            'gamesessionid' => new external_value(PARAM_INT, 'the id of the current game session')
        ]);
    }

    /**
     * Definition of return type for {@see get_used_jokers}.
     *
     * @return external_multiple_structure
     */
    public static function get_used_jokers_returns() {
        return new external_multiple_structure(
            joker_dto::get_read_structure()
        );
    }

    /**
     * Get all used jokers.
     *
     * @param int $coursemoduleid
     * @param int $gamesessionid The id of the current game session
     *
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function get_used_jokers($coursemoduleid, $gamesessionid) {
        $params = ['coursemoduleid' => $coursemoduleid, 'gamesessionid' => $gamesessionid];
        $params = self::validate_parameters(self::get_used_jokers_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($params['coursemoduleid'], 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = util::get_game($coursemodule);
        $gamesession = util::get_gamesession($gamesessionid);
        util::validate_gamesession($game, $gamesession);

        // collect jokers
        $result = [];
        $sql_params = ['gamesession' => $gamesessionid];
        $records = $DB->get_records('millionaire_jokers', $sql_params);
        foreach ($records as $joker_data) {
            $joker = util::get_joker($joker_data->id);
            $question = util::get_question($joker->get_question());
            $level = util::get_level($question->get_level());
            $exporter = new joker_dto($joker, $level, $ctx);
            $result[] = $exporter->export($renderer);
        }
        return $result;
    }

    /**
     * Definition of parameters for {@see submit_joker}.
     *
     * @return external_function_parameters
     */
    public static function submit_joker_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
            'gamesessionid' => new external_value(PARAM_INT, 'the id of the current game session'),
            'questionid' => new external_value(PARAM_INT, 'the id of the question the joker is to be used on'),
            'jokertype' => new external_value(PARAM_ALPHA, 'the type of joker'),
        ]);
    }

    /**
     * Definition of return type for {@see submit_joker}.
     *
     * @return external_single_structure
     */
    public static function submit_joker_returns() {
        return joker_dto::get_read_structure();
    }

    /**
     * Submits that a joker is to be used on the given question.
     *
     * @param int $coursemoduleid
     * @param int $gamesessionid
     * @param int $questionid
     * @param string $jokertype
     *
     * @return stdClass
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function submit_joker($coursemoduleid, $gamesessionid, $questionid, $jokertype) {
        $params = [
            'coursemoduleid' => $coursemoduleid,
            'gamesessionid' => $gamesessionid,
            'questionid' => $questionid,
            'jokertype' => $jokertype,
        ];
        $params = self::validate_parameters(self::submit_joker_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($params['coursemoduleid'], 'millionaire');
        self::validate_context($coursemodule->context);

        if (!in_array($jokertype, MOD_MILLIONAIRE_JOKERS)) {
            throw new invalid_parameter_exception("The given joker type '$jokertype' is invalid.");
        }

        global $PAGE;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = util::get_game($coursemodule);
        $gamesession = util::get_gamesession($gamesessionid);
        util::validate_gamesession($game, $gamesession);
        $question = util::get_question($questionid);
        util::validate_question($gamesession, $question);
        $level = util::get_level($question->get_level());

        // check if allowed to use the joker
        if ($gamesession->is_joker_used($jokertype)) {
            throw new invalid_parameter_exception("The given joker type '$jokertype' was already used in the game session with id $gamesessionid.");
        }

        // still allowed, so use it
        $joker = new joker();
        $joker->set_gamesession($gamesessionid);
        $joker->set_question($questionid);
        $joker->set_joker_type($jokertype);
        $joker->generate_content($question);
        $joker->save();

        // return the created joker
        $exporter = new joker_dto($joker, $level, $ctx);
        return $exporter->export($renderer);
    }
}
