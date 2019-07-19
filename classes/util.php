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

namespace mod_millionaire;

use cm_info;
use dml_exception;
use invalid_parameter_exception;
use mod_millionaire\model\game;
use mod_millionaire\model\gamesession;
use mod_millionaire\model\joker;
use mod_millionaire\model\level;
use mod_millionaire\model\question;

class util {

    /**
     * Checks if the logged in user has the given $capability.
     *
     * @param string $capability
     * @param \context $context
     * @param int|null $userid
     *
     * @return bool
     * @throws \coding_exception
     */
    public static function user_has_capability(string $capability, \context $context, $userid = null) : bool {
        return \has_capability($capability, $context, $userid);
    }

    /**
     * Kills the current request if the logged in user doesn't have the required capabilities.
     *
     * @param string $capability
     * @param \context $context
     * @param int|null $userid
     *
     * @return void
     * @throws \required_capability_exception
     */
    public static function require_user_has_capability(string $capability, \context $context, $userid = null) {
        \require_capability($capability, $context, $userid);
    }

    /**
     * Checks that the gamesession belongs to the given $game and the logged in $USER.
     *
     * @param game $game
     * @param gamesession $gamesession
     *
     * @return void
     * @throws invalid_parameter_exception
     */
    public static function validate_gamesession(game $game, gamesession $gamesession) {
        if ($game->get_id() !== $gamesession->get_game()) {
            throw new invalid_parameter_exception("gamesession " . $gamesession->get_id() . " doesn't belong to game " . $game->get_id());
        }
        global $USER;
        if ($gamesession->get_mdl_user() != $USER->id) {
            throw new invalid_parameter_exception("gamesession " . $gamesession->get_id() . " doesn't belong to logged in user");
        }
    }

    /**
     * Checks that the question belongs to the given $gamesession.
     *
     * @param gamesession $gamesession
     * @param question $question
     *
     * @return void
     * @throws invalid_parameter_exception
     */
    public static function validate_question(gamesession $gamesession, question $question) {
        if ($gamesession->get_id() !== $question->get_gamesession()) {
            throw new invalid_parameter_exception("question " . $question->get_id() . " doesn't belong to given gamesession");
        }
    }

    /**
     * Checks that the level belongs to the given $game.
     *
     * @param game $game
     * @param level $level
     *
     * @return void
     * @throws invalid_parameter_exception
     */
    public static function validate_level(game $game, level $level) {
        if ($game->get_id() !== $level->get_game()) {
            throw new invalid_parameter_exception("level " . $level->get_id() . " doesn't belong to given game");
        }
    }

    /**
     * Gets the game instance from the database.
     *
     * @param cm_info $coursemodule
     *
     * @return game
     * @throws dml_exception
     */
    public static function get_game(cm_info $coursemodule): game {
        global $DB;
        $game_data = $DB->get_record('millionaire', ['id' => $coursemodule->instance]);
        $game = new game();
        $game->apply($game_data);
        return $game;
    }

    /**
     * Gets the gamesession instance for the given $gamesessionid from the database.
     *
     * @param int $gamesessionid
     *
     * @return gamesession
     * @throws dml_exception
     */
    public static function get_gamesession($gamesessionid): gamesession {
        $gamesession = new gamesession();
        $gamesession->load_data_by_id($gamesessionid);
        return $gamesession;
    }

    /**
     * Loads a level by its id.
     *
     * @param int $levelid
     *
     * @return level
     * @throws dml_exception
     */
    public static function get_level($levelid): level {
        $level = new level();
        $level->load_data_by_id($levelid);
        return $level;
    }

    /**
     * Loads a question by its id.
     *
     * @param int $questionid
     *
     * @return question
     * @throws dml_exception
     */
    public static function get_question($questionid): question {
        $question = new question();
        $question->load_data_by_id($questionid);
        return $question;
    }

    /**
     * Loads a joker by its id.
     *
     * @param int $jokerid
     *
     * @return joker
     * @throws dml_exception
     */
    public static function get_joker($jokerid): joker {
        $joker = new joker();
        $joker->load_data_by_id($jokerid);
        return $joker;
    }

    /**
     * Gets or creates a gamesession for the current user. Allowed existing gamesessions are either in state
     * PROGRESS or FINISHED.
     *
     * @param game $game
     *
     * @return gamesession
     * @throws dml_exception
     */
    public static function get_or_create_gamesession(game $game): gamesession {
        global $DB, $USER;
        // try to find existing in-progress or finished gamesession
        $sql = "
            SELECT *
              FROM {millionaire_gamesessions}
             WHERE game = :game AND mdl_user = :mdl_user AND state IN (:state_progress, :state_finished)
          ORDER BY timemodified DESC
        ";
        $params = [
            'game' => $game->get_id(),
            'mdl_user' => $USER->id,
            'state_progress' => gamesession::STATE_PROGRESS,
            'state_finished' => gamesession::STATE_FINISHED,
        ];
        $record = $DB->get_record_sql($sql, $params);
        // get or create game session
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
     * @throws dml_exception
     */
    public static function dump_running_gamesessions(game $game) {
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
     * @throws dml_exception
     */
    public static function insert_gamesession(game $game): gamesession {
        global $USER;
        $gamesession = new gamesession();
        $gamesession->set_game($game->get_id());
        $gamesession->set_mdl_user($USER->id);
        $gamesession->set_continue_on_failure($game->is_continue_on_failure());
        $gamesession->save();
        return $gamesession;
    }

}
