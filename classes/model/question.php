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

use function array_map;
use function explode;
use function intval;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * Class question
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question extends abstract_model {

    /**
     * @var int The timestamp of the creation of this question.
     */
    protected $timecreated;
    /**
     * @var int The timestamp of the last update of this question.
     */
    protected $timemodified;
    /**
     * @var int The id of the gamesession instance this question belongs to.
     */
    protected $gamesession;
    /**
     * @var int The id of the level this question was chosen for.
     */
    protected $level;
    /**
     * @var int The id of the moodle question.
     */
    protected $mdl_question;
    /**
     * @var string The ids of the moodle answers in their correct order, separated by commas.
     */
    protected $mdl_answers_order;
    /**
     * @var int The id of the moodle answer the user has chosen.
     */
    protected $mdl_answer;
    /**
     * @var int The score the user has reached by answering this question.
     */
    protected $score;
    /**
     * @var bool Whether or not the question was answered correctly.
     */
    protected $correct;
    /**
     * @var bool Whether or not the question was answered at all.
     */
    protected $finished;
    /**
     * @var \question_definition
     */
    protected $_mdl_question;

    /**
     * question constructor.
     */
    function __construct() {
        parent::__construct('millionaire_questions', 0);
        $this->timecreated = \time();
        $this->timemodified = \time();
        $this->gamesession = 0;
        $this->level = 0;
        $this->mdl_question = 0;
        $this->mdl_answers_order = '';
        $this->mdl_answer = 0;
        $this->score = 0;
        $this->correct = 0;
        $this->finished = 0;
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
        $this->gamesession = $data['gamesession'];
        $this->level = $data['level'];
        $this->mdl_question = $data['mdl_question'];
        $this->mdl_answers_order = $data['mdl_answers_order'];
        $this->mdl_answer = isset($data['mdl_answer']) ? $data['mdl_answer'] : null;
        $this->score = isset($data['score']) ? $data['score'] : 0;
        $this->correct = isset($data['correct']) ? ($data['correct'] == 1) : false;
        $this->finished = isset($data['finished']) ? ($data['finished'] == 1) : false;
    }

    /**
     * Returns the moodle question from the question bank.
     *
     * @return \question_definition
     */
    public function get_mdl_question_ref(): \question_definition {
        if ($this->_mdl_question === null) {
            $this->_mdl_question = \question_bank::load_question($this->mdl_question, false);
        }
        return $this->_mdl_question;
    }

    /**
     * Returns the ids of the moodle answer ids in their stored order.
     *
     * @return int[]
     */
    public function get_mdl_answer_ids_ordered(): array {
        return array_map(
            function ($strId) {
                return intval($strId);
            },
            explode(",", $this->get_mdl_answers_order())
        );
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
    public function get_level(): int {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function set_level(int $level) {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function get_mdl_question(): int {
        return $this->mdl_question;
    }

    /**
     * @param int $mdl_question
     */
    public function set_mdl_question(int $mdl_question) {
        $this->mdl_question = $mdl_question;
    }

    /**
     * @return string
     */
    public function get_mdl_answers_order(): string {
        return $this->mdl_answers_order;
    }

    /**
     * @param string $mdl_answers_order
     */
    public function set_mdl_answers_order(string $mdl_answers_order) {
        $this->mdl_answers_order = $mdl_answers_order;
    }

    /**
     * @return int
     */
    public function get_mdl_answer(): int {
        return $this->mdl_answer;
    }

    /**
     * @param int $mdl_answer
     */
    public function set_mdl_answer(int $mdl_answer) {
        $this->mdl_answer = $mdl_answer;
    }

    /**
     * @return int
     */
    public function get_score(): int {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function set_score(int $score) {
        $this->score = $score;
    }

    /**
     * @return bool
     */
    public function is_correct(): bool {
        return $this->correct;
    }

    /**
     * @param bool $correct
     */
    public function set_correct(bool $correct) {
        $this->correct = $correct;
    }

    /**
     * @return bool
     */
    public function is_finished(): bool {
        return $this->finished;
    }

    /**
     * @param bool $finished
     */
    public function set_finished(bool $finished) {
        $this->finished = $finished;
    }
}
