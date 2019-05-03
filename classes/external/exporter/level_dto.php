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
use mod_millionaire\model\level;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class level
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class level_dto extends exporter {

    /**
     * @var level
     */
    protected $level;
    /**
     * @var game
     */
    protected $game;

    /**
     * level constructor.
     *
     * @param level $level
     * @param game $game
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(level $level, game $game, context $context) {
        $this->level = $level;
        $this->game = $game;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'level id',
            ],
            'game' => [
                'type' => PARAM_INT,
                'description' => 'millionaire instance id',
            ],
            'state' => [
                'type' => PARAM_TEXT,
                'description' => 'private, active, deleted',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'name of the level',
            ],
            'position' => [
                'type' => PARAM_INT,
                'description' => 'order of the levels within a game session is defined by their indices.'
            ],
            'score' => [
                'type' => PARAM_INT,
                'description' => 'the score a user will reach when answering the question of this level correctly.'
            ],
            'safe_spot' => [
                'type' => PARAM_BOOL,
                'description' => 'when answering a question wrong, the user will fall back to the most recent safe spot.',
            ],
            'currency' => [
                'type' => PARAM_TEXT,
                'description' => 'the currency of the level, inherited from the game config',
            ]
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        return [
            'id' => $this->level->get_id(),
            'game' => $this->level->get_game(),
            'state' => $this->level->get_state(),
            'name' => $this->level->get_name() ?: $this->level->get_score(),
            'position' => $this->level->get_position(),
            'score' => $this->level->get_score(),
            'safe_spot' => $this->level->is_safe_spot(),
            'currency' => $this->game->get_currency_for_levels()
        ];
    }
}
