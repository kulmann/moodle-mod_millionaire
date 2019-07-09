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
use mod_millionaire\model\question;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class level_dto
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
     * @var string
     */
    protected $forced_name;
    /**
     * @var question
     */
    protected $question;
    /**
     * @var game
     */
    protected $game;

    /**
     * level_dto constructor.
     *
     * @param level $level
     * @param string $forced_name
     * @param question|null $question
     * @param game $game
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(level $level, $forced_name, $question, game $game, context $context) {
        $this->level = $level;
        $this->forced_name = $forced_name;
        $this->question = $question;
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
                'description' => 'active, deleted',
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
            ],
            'finished' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the level is already finished',
            ],
            'correct' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the question for this level was answered correctly',
            ],
            'reached_score' => [
                'type' => PARAM_INT,
                'description' => 'the score that was reached by answering this question',
            ],
            'seen' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not this level has been seen by the user (i.e. if a question was shown)',
            ],
            'title' => [
                'type' => PARAM_TEXT,
                'description' => 'if the level has a set name, it will be returned here. If there is none, we will return the score and the currency sign instead.'
            ],
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $result = \array_merge(
            $this->level->to_array(),
            [
                'currency' => $this->game->get_currency_for_levels(),
                'finished' => $this->question ? $this->question->is_finished() : false,
                'correct' => $this->question ? $this->question->is_correct() : false,
                'reached_score' => $this->question ? $this->question->get_score() : -1,
                'seen' => $this->question !== null,
            ]
        );
        $result['title'] = $this->forced_name;
        return $result;
    }
}
