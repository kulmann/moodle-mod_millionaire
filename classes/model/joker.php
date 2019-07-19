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

namespace mod_millionaire\model;

use coding_exception;
use moodle_exception;
use function array_map;
use function array_slice;
use function count;
use function implode;
use function random_int;
use function shuffle;

defined('MOODLE_INTERNAL') || die();

/**
 * Class joker
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class joker extends abstract_model {

    /**
     * @var int Timestamp of creation of this joker.
     */
    protected $timecreated;
    /**
     * @var int Timestamp of last update of this joker.
     */
    protected $timemodified;
    /**
     * @var int Id of the gamesession this joker belongs to.
     */
    protected $gamesession;
    /**
     * @var int Id of the question this joker was used on.
     */
    protected $question;
    /**
     * @var string The type of joker.
     */
    protected $joker_type;
    /**
     * @var string The data this joker produced.
     */
    protected $joker_data;

    /**
     * joker constructor.
     */
    function __construct() {
        parent::__construct('millionaire_jokers', 0);
        $this->timecreated = \time();
        $this->timemodified = \time();
        $this->gamesession = 0;
        $this->question = 0;
        $this->joker_type = '';
        $this->joker_data = '';
    }

    /**
     * Apply data to this object from an associative array or an object.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function apply($data) {
        if (\is_object($data)) {
            $data = get_object_vars($data);
        }
        $this->id = isset($data['id']) ? $data['id'] : 0;
        $this->timecreated = isset($data['timecreated']) ? $data['timecreated'] : \time();
        $this->timemodified = isset($data['timemodified']) ? $data['timemodified'] : \time();
        $this->gamesession = isset($data['gamesession']) ? $data['gamesession'] : 0;
        $this->question = isset($data['question']) ? $data['question'] : 0;
        $this->joker_type = isset($data['joker_type']) ? $data['joker_type'] : 'error';
        $this->joker_data = isset($data['joker_data']) ? $data['joker_data'] : '';
    }

    /**
     * Generates content for this joker, based on its type.
     *
     * @param question $question
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function generate_content(question $question) {
        // collect wrong and correct answer ids
        $mdl_question = $question->get_mdl_question_ref();
        $mdl_answers = $mdl_question->answers;
        $wrong_answer_ids = array_map(
            function ($mdl_answer) {
                return $mdl_answer->id;
            },
            array_filter($mdl_answers, function ($mdl_answer) {
                return $mdl_answer->fraction == 0;
            })
        );
        shuffle($wrong_answer_ids);
        $correct_answer_ids = array_map(
            function ($mdl_answer) {
                return $mdl_answer->id;
            },
            array_filter($mdl_answers, function ($mdl_answer) {
                return $mdl_answer->fraction > 0;
            })
        );
        if (count($correct_answer_ids) !== 1) {
            // can't handle multiple choice questions
            throw new moodle_exception("can't handle multiple choice questions!");
        }
        // generate the joker content
        switch ($this->joker_type) {
            case MOD_MILLIONAIRE_JOKER_CHANCE:
                if (count($wrong_answer_ids) > 2) {
                    $wrong_answer_ids = array_slice($wrong_answer_ids, 0, 2);
                }
                $this->set_joker_data(implode(",", $wrong_answer_ids));
                break;
            case MOD_MILLIONAIRE_JOKER_AUDIENCE:
                $remainingPercentage = 100.0;
                $percentages = [];
                // correct answer percentage
                $percentage_correct = random_int(40, 95);
                $percentages[] = current($correct_answer_ids) . '=' . $percentage_correct;
                $remainingPercentage -= $percentage_correct;
                // wrong answer percentages
                foreach ($wrong_answer_ids as $id) {
                    if (end($false_answers_ids) === $id) {
                        $percentage_wrong = $remainingPercentage;
                    } else {
                        // make sure that the false percentage doesn't exceed the correct percentage too much.
                        $percentage_wrong = random_int(0, min($remainingPercentage, $percentage_correct + 3));
                        $remainingPercentage -= $percentage_wrong;
                    }
                    $percentages[] = $id . '=' . $percentage_wrong;
                }
                $this->set_joker_data(implode(',', $percentages));
                break;
            case MOD_MILLIONAIRE_JOKER_FEEDBACK:
                // show a hint from the db if it exists
                $feedback = $mdl_question->generalfeedback;
                if (empty($feedback)) {
                    $feedback = get_string('game_joker_feedback_unavailable', 'millionaire');
                }
                $this->set_joker_data($feedback);
                break;
            default: // do nothing
        }
    }

    /**
     * @return int
     */
    public function get_timecreated(): int {
        return $this->timecreated;
    }

    /**
     * @param int $timecreated
     */
    public function set_timecreated(int $timecreated) {
        $this->timecreated = $timecreated;
    }

    /**
     * @return int
     */
    public function get_timemodified(): int {
        return $this->timemodified;
    }

    /**
     * @param int $timemodified
     */
    public function set_timemodified(int $timemodified) {
        $this->timemodified = $timemodified;
    }

    /**
     * @return int
     */
    public function get_gamesession(): int {
        return $this->gamesession;
    }

    /**
     * @param int $gamesession
     */
    public function set_gamesession(int $gamesession) {
        $this->gamesession = $gamesession;
    }

    /**
     * @return int
     */
    public function get_question(): int {
        return $this->question;
    }

    /**
     * @param int $question
     */
    public function set_question(int $question) {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function get_joker_type(): string {
        return $this->joker_type;
    }

    /**
     * @param string $joker_type
     */
    public function set_joker_type(string $joker_type) {
        $this->joker_type = $joker_type;
    }

    /**
     * @return string
     */
    public function get_joker_data(): string {
        return $this->joker_data;
    }

    /**
     * @param string $joker_data
     */
    public function set_joker_data(string $joker_data) {
        $this->joker_data = $joker_data;
    }
}
