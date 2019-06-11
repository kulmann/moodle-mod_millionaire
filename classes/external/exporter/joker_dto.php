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

use function array_merge;
use coding_exception;
use context;
use core\external\exporter;
use mod_millionaire\model\joker;
use mod_millionaire\model\level;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class joker_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class joker_dto extends exporter {

    /**
     * @var joker
     */
    protected $joker;
    /**
     * @var level
     */
    protected $level;

    /**
     * joker_dto constructor.
     *
     * @param joker $joker
     * @param level $level
     * @param context $context
     *
     * @throws coding_exception
     */
    public function __construct(joker $joker, level $level, context $context) {
        $this->joker = $joker;
        $this->level = $level;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'level id',
            ],
            'gamesession' => [
                'type' => PARAM_INT,
                'description' => 'gamesession id this joker belongs to',
            ],
            'question' => [
                'type' => PARAM_INT,
                'description' => 'question id this joker was used on',
            ],
            'joker_type' => [
                'type' => PARAM_TEXT,
                'description' => 'the type of joker',
            ],
            'joker_data' => [
                'type' => PARAM_TEXT,
                'description' => 'the content the joker produced',
            ],
            'level_id' => [
                'type' => PARAM_INT,
                'description' => 'the id of the level this joker was used on',
            ],
            'level_index' => [
                'type' => PARAM_INT,
                'description' => 'the index of the level this joker was used on',
            ],
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        return array_merge(
            $this->joker->to_array(),
            [
                'level_id' => $this->level->get_id(),
                'level_index' => $this->level->get_position(),
            ]
        );
    }
}
