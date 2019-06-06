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
use mod_millionaire\model\level;
use mod_millionaire\model\question;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class question_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_dto extends exporter {

    /**
     * @var question
     */
    protected $question;
    /**
     * @var level
     */
    protected $level;

    /**
     * question_dto constructor.
     *
     * @param question $question
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(question $question, level $level, context $context) {
        $this->question = $question;
        $this->level = $level;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'question id',
            ],
            'index' => [
                'type' => PARAM_INT,
                'description' => 'index of the level',
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'timestamp when this question was created',
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'timestamp when this question was modified',
            ],
            'gamesession' => [
                'type' => PARAM_INT,
                'description' => 'id of the game session this question belongs to',
            ],
            'level' => [
                'type' => PARAM_INT,
                'description' => 'id of the level this question was chosen for',
            ],
            'mdl_question' => [
                'type' => PARAM_INT,
                'description' => 'id of the moodle question instance',
            ],
            'mdl_answer' => [
                'type' => PARAM_INT,
                'description' => 'id of the moodle answer the user has chosen',
            ],
            'score' => [
                'type' => PARAM_INT,
                'description' => 'score the user has reached by answering this question',
            ],
            'correct' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the question was answered correctly',
            ],
            'finished' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the question was answered at all',
            ],
            'mdl_question_id' => [
                'type' => PARAM_INT,
                'description' => 'id of the associated moodle question',
            ],
            'mdl_question_type' => [
                'type' => PARAM_TEXT,
                'description' => 'type of the associated moodle question',
            ],
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $mdl_question = $this->question->get_mdl_question_ref();
        return \array_merge(
            $this->question->to_array(),
            [
                'index' => $this->level->get_position(),
                'mdl_question_id' => $mdl_question->id,
                'mdl_question_type' => \get_class($mdl_question),
            ]
        );
    }
}
