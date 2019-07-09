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

namespace mod_millionaire\external\exporter;

use context;
use core\external\exporter;
use mod_millionaire\model\game;
use mod_millionaire\model\gamesession;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class gamesession_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gamesession_dto extends exporter {

    /**
     * @var gamesession
     */
    protected $gamesession;
    /**
     * @var game
     */
    protected $game;

    /**
     * gamesession_dto constructor.
     *
     * @param gamesession $gamesession
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(gamesession $gamesession, game $game, context $context) {
        $this->gamesession = $gamesession;
        $this->game = $game;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'gamesession id',
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'timestamp of the creation of the gamesession',
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'timestamp of the last modification of the gamesession',
            ],
            'game' => [
                'type' => PARAM_INT,
                'description' => 'millionaire instance id',
            ],
            'mdl_user' => [
                'type' => PARAM_INT,
                'description' => 'id of the moodle user who owns this gamesession',
            ],
            'continue_on_failure' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the gamesession should continue when the user gives an incorrect answer',
            ],
            'score' => [
                'type' => PARAM_INT,
                'description' => 'the current score of the user in this gamesession',
            ],
            'score_name' => [
                'type' => PARAM_TEXT,
                'description' => 'the label of the level the player has reached',
            ],
            'answers_total' => [
                'type' => PARAM_INT,
                'description' => 'the total number of answers the user has already given in this gamesession',
            ],
            'answers_correct' => [
                'type' => PARAM_INT,
                'description' => 'the number of correct answers the user has already given in this gamesession',
            ],
            'state' => [
                'type' => PARAM_TEXT,
                'description' => 'progress, finished or dumped',
            ],
            'won' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the game session is won',
            ],
            'current_level' => [
                'type' => PARAM_INT,
                'description' => 'the index of the current level',
            ]
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        if ($this->gamesession->get_answers_correct() === 0) {
            $level_for_score_name = '0 ' . $this->game->get_currency_for_levels();
        } else {
            $level = $this->game->get_active_level_by_position($this->gamesession->get_answers_correct() - 1);
            if (empty($level->get_name())) {
                $level_for_score_name = $level->get_score() . ' ' . $this->game->get_currency_for_levels();
            } else {
                $level_for_score_name = $level->get_name();
            }
        }
        // collect data
        $result = \array_merge(
            $this->gamesession->to_array(),
            [
                'current_level' => $this->gamesession->get_answers_total(),
                'score_name' => $level_for_score_name,
            ]
        );
        // make sure that only finished game sessions can be shown as won.
        $result['won'] &= ($result['state'] === gamesession::STATE_FINISHED);
        // return
        return $result;
    }
}
