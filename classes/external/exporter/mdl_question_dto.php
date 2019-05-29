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
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class mdl_question_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mdl_question_dto extends exporter {

    /**
     * @var \question_definition
     */
    protected $mdl_question;

    /**
     * mdl_answer_dto constructor.
     *
     * @param \question_definition $mdl_question
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(\question_definition $mdl_question, context $context) {
        $this->mdl_question = $mdl_question;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'moodle question id',
            ],
            'type' => [
                'type' => PARAM_TEXT,
                'description' => 'the qtype of this moodle question'
            ],
            'questiontext' => [
                'type' => PARAM_TEXT,
                'description' => 'the question content',
            ],
            'feedback' => [
                'type' => PARAM_TEXT,
                'description' => 'feedback content',
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
            'id' => $this->mdl_question->id,
            'type' => $this->mdl_question->qtype->name(),
            'questiontext' => $this->mdl_question->questiontext,
            'feedback' => $this->mdl_question->generalfeedback,
        ];
    }
}
