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
use mod_millionaire\util;
use renderer_base;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class game_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class game_dto extends exporter {

    /**
     * @var game
     */
    protected $game;
    /**
     * @var stdClass
     */
    protected $user;
    /**
     * @var context
     */
    protected $ctx;

    /**
     * game_dto constructor.
     *
     * @param game $game
     * @param stdClass $user
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(game $game, stdClass $user, context $context) {
        $this->game = $game;
        $this->user = $user;
        $this->ctx = $context;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'gamesession id',
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'activity title',
            ],
            'currency_for_levels' => [
                'type' => PARAM_TEXT,
                'description' => 'currency shown for scores',
            ],
            'highscore_count' => [
                'type' => PARAM_INT,
                'description' => 'the number of entries in the leader board',
            ],
            'highscore_teachers' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not teachers are shown in the leader board',
            ],
            'mdl_user' => [
                'type' => PARAM_INT,
                'description' => 'the id of the currently active moodle user',
            ],
            'mdl_user_teacher' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the logged in user has game editing capabilities',
            ],
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        return [
            'id' => $this->game->get_id(),
            'name' => $this->game->get_name(),
            'currency_for_levels' => $this->game->get_currency_for_levels(),
            'highscore_count' => $this->game->get_highscore_count(),
            'highscore_teachers' => $this->game->is_highscore_teachers(),
            'mdl_user' => $this->user->id,
            'mdl_user_teacher' => util::user_has_capability('mod/millionaire:manage', $this->ctx, $this->user->id),
        ];
    }
}
