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
use mod_millionaire\model\question;
use renderer_base;
use function array_search;
use function intval;

defined('MOODLE_INTERNAL') || die();

/**
 * Class mdl_answer_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mdl_answer_dto extends exporter {

    /**
     * @var \question_answer
     */
    protected $mdl_answer;
    /**
     * @var question
     */
    protected $question;

    /**
     * mdl_answer_dto constructor.
     *
     * @param \question_answer $mdl_answer
     * @param question $question
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(\question_answer $mdl_answer, question $question, context $context) {
        $this->mdl_answer = $mdl_answer;
        $this->question = $question;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'moodle answer id',
            ],
            'fraction' => [
                'type' => PARAM_FLOAT,
                'description' => 'percentage of correctness, out of range [0,1]'
            ],
            'answer' => [
                'type' => PARAM_TEXT,
                'description' => 'answer content',
            ],
            'feedback' => [
                'type' => PARAM_TEXT,
                'description' => 'feedback content',
            ],
            'label' => [
                'type' => PARAM_TEXT,
                'description' => 'label of the answer as (to be) shown in the game ui'
            ],
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $orderedAnswerIds = $this->question->get_mdl_answer_ids_ordered();
        $index = array_search($this->mdl_answer->id, $orderedAnswerIds);
        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        return [
            'id' => intval($this->mdl_answer->id),
            'fraction' => $this->mdl_answer->fraction,
            'answer' => $this->mdl_answer->answer,
            'feedback' => $this->mdl_answer->feedback,
            'label' => $labels[$index]
        ];
    }
}
