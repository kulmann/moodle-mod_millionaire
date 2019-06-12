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

use coding_exception;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_value;
use invalid_parameter_exception;
use mod_millionaire\external\exporter\score_dto;
use mod_millionaire\model\gamesession;
use mod_millionaire\util;
use moodle_exception;
use restricted_context_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class scores
 *
 * @package    mod_millionaire\external
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scores extends external_api {

    /**
     * Definition of parameters for {@see get_scores_global}.
     *
     * @return external_function_parameters
     */
    public static function get_scores_global_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
        ]);
    }

    /**
     * Definition of return type for {@see get_scores_global}.
     *
     * @return external_multiple_structure
     */
    public static function get_scores_global_returns() {
        return new external_multiple_structure(
            score_dto::get_read_structure()
        );
    }

    /**
     * Get scores among all users.
     *
     * @param int $coursemoduleid
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function get_scores_global($coursemoduleid) {
        $params = ['coursemoduleid' => $coursemoduleid];
        self::validate_parameters(self::get_scores_global_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;
        $game = util::get_game($coursemodule);

        // build sub query base on scoring mode
        $highscore_mode = $game->get_highscore_mode();
        $params = ['game' => $game->get_id(), 'state' => gamesession::STATE_FINISHED];
        switch ($highscore_mode) {
            case MOD_MILLIONAIRE_HIGHSCORE_MODE_BEST:
                $sql_sub = "SELECT gs.mdl_user, MAX(gs.score) AS score, COUNT(gs.score) AS sessions
                              FROM {millionaire_gamesessions} AS gs
                             WHERE game = :game AND state = :state
                          GROUP BY gs.mdl_user
                          ORDER BY score DESC";
                break;
            case MOD_MILLIONAIRE_HIGHSCORE_MODE_LAST:
                $sql_sub = "SELECT gs.mdl_user, gs.score AS score, COUNT(gs.score) AS sessions
                              FROM {millionaire_gamesessions} AS gs
                              JOIN (SELECT MAX(timecreated) AS maxtime FROM {millionaire_gamesessions} WHERE game = :game_inner AND state = :state_inner GROUP BY mdl_user) AS maxtimes ON gs.timecreated = maxtimes.maxtime 
                             WHERE game = :game AND state = :state
                          GROUP BY gs.mdl_user
                          ORDER BY gs.timecreated DESC";
                $params = \array_merge($params, ['game_inner' => $game->get_id(), 'state_inner' => gamesession::STATE_FINISHED]);
                break;
            case MOD_MILLIONAIRE_HIGHSCORE_MODE_AVERAGE:
                $sql_sub = "SELECT gs.mdl_user, (SUM(gs.score)/COUNT(gs.score)) AS score, COUNT(gs.score) AS sessions
                              FROM {millionaire_gamesessions} AS gs
                             WHERE game = :game AND state = :state
                          GROUP BY gs.mdl_user
                          ORDER BY score DESC";
                break;
            default:
                throw new coding_exception("highscore mode $highscore_mode is not supported.");
        }

        // collect data and build score dtos
        $sql = "SELECT res.mdl_user, res.score, res.sessions, CONCAT(u.firstname, ' ', u.lastname) AS mdl_user_name
                  FROM ($sql_sub) AS res
                  JOIN {user} AS u ON res.mdl_user=u.id
              ORDER BY score DESC";
        $records = $DB->get_records_sql($sql, $params);
        $score_dtos = [];
        if ($records) {
            $rank = 0;
            foreach ($records as $record) {
                $teacher = has_capability('mod/millionaire:manage', $ctx, $record->mdl_user);
                if (!$teacher || $game->is_highscore_teachers()) {
                    $rank++;
                }
                $score_dtos[] = new score_dto($rank, $record->score, $record->sessions, $record->mdl_user, $record->mdl_user_name, $teacher, $ctx);
            }
        }

        // collect data export
        $result = [];
        foreach ($score_dtos as $score_dto) {
            \assert($score_dto instanceof score_dto);
            $result[] = $score_dto->export($renderer);
        }
        return $result;
    }
}
