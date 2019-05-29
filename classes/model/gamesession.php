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

defined('MOODLE_INTERNAL') || die();

/**
 * Class gamesession
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gamesession extends abstract_model {

    const STATE_FINISHED = 'finished';
    const STATE_DUMPED = 'dumped';
    const STATE_PROGRESS = 'progress';

    /**
     * @var int Timestamp of the creation of this gamesession
     */
    protected $timecreated;
    /**
     * @var int Timestamp of the last db update of this gamesession
     */
    protected $timemodified;
    /**
     * @var int The id of the millionaire instance this gamesession belongs to
     */
    protected $game;
    /**
     * @var int The id of the moodle user this gamesession belongs to
     */
    protected $mdl_user;
    /**
     * @var bool Whether or not this gamesession is configured to still the the user play next levels when he answers incorrect
     */
    protected $continue_on_failure;
    /**
     * @var int Score the user reached so far
     */
    protected $score;
    /**
     * @var int Total number of answers in this gamesession
     */
    protected $answers_total;
    /**
     * @var int Number of correct answers in this gamesession
     */
    protected $answers_correct;
    /**
     * @var string The state of the gamesession, out of [progress, finished, dumped].
     */
    protected $state;

    /**
     * gamesession constructor.
     */
    function __construct() {
        parent::__construct('millionaire_gamesessions', 0);
        $this->timecreated = \time();
        $this->timemodified = \time();
        $this->game = 0;
        $this->mdl_user = 0;
        $this->continue_on_failure = false;
        $this->score = 0;
        $this->answers_total = 0;
        $this->answers_correct = 0;
        $this->state = 'progress';
    }

    /**
     * Apply data to this object from an associative array or an object.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function apply($data): void {
        if (\is_object($data)) {
            $data = get_object_vars($data);
        }
        $this->id = isset($data['id']) ? $data['id'] : 0;
        $this->timecreated = isset($data['timecreated']) ? $data['timecreated'] : \time();
        $this->timemodified = isset($data['timemodified']) ? $data['timemodified'] : \time();
        $this->game = $data['game'];
        $this->mdl_user = $data['mdl_user'];
        $this->continue_on_failure = isset($data['continue_on_failure']) ? ($data['continue_on_failure'] == 1) : false;
        $this->score = isset($data['score']) ? $data['score'] : 0;
        $this->answers_total = isset($data['answers_total']) ? $data['answers_total'] : 0;
        $this->answers_correct = isset($data['answers_correct']) ? $data['answers_correct'] : 0;
        $this->state = isset($data['finished']) ? $data['finished'] : 'progress';
    }

    /**
     * If this gamesession is finished, this function returns the highest finished level (doesn't matter if successful or not).
     * If this gamesession is not finished, this function returns the smallest unfinished level.
     *
     * @return level
     * @throws \dml_exception
     */
    public function get_current_level(): level {
        global $DB;
        $sql = "
            SELECT l.id, l.position, q.finished
              FROM {millionaire_levels} AS l
         LEFT JOIN {millionaire_questions} AS q on l.id=q.level 
             WHERE l.game = ? AND (q.gamesession IS NULL OR q.gamesession = ?)
          ORDER BY l.position DESC
        ";
        $levels = $DB->get_records_sql($sql, [$this->get_game(), $this->get_id()]);
        if ($levels === false || empty($levels)) {
            throw new \dml_exception('A game without levels cannot be played. Please configure levels first.');
        }
        $level = new level();
        $last_unfinished_level_id = null;
        if ($this->get_state() === self::STATE_PROGRESS) {
            foreach ($levels as $record) {
                if ($record->finished === 1) {
                    break;
                }
                $last_unfinished_level_id = $record->id;
            }
        } else {
            foreach ($levels as $record) {
                $last_unfinished_level_id = $record->id;
                if ($record->finished === 1) {
                    break;
                }
            }
        }
        $level->load_data_by_id($last_unfinished_level_id);
        return $level;
    }

    /**
     * Looks up in the db, whether the given level is already finished.
     *
     * @param int $id_level
     *
     * @return bool
     * @throws \dml_exception
     */
    public function is_level_finished($id_level) {
        $question = $this->get_question_by_level($id_level);
        return ($question !== null && $question->is_finished());
    }

    /**
     * Tries to find a question for the given $id_level. Returns null if none found.
     *
     * @param int $id_level
     * @return question|null
     * @throws \dml_exception
     */
    public function get_question_by_level($id_level) {
        global $DB;
        $question = new question();
        $record = $DB->get_record_select(
            $question->get_table_name(),
            'gamesession = ? AND level = ?',
            [$this->get_id(), $id_level]
        );
        if ($record) {
            $question->apply($record);
            return $question;
        } else {
            return null;
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
    public function set_timecreated(int $timecreated): void {
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
    public function set_timemodified(int $timemodified): void {
        $this->timemodified = $timemodified;
    }

    /**
     * @return int
     */
    public function get_game(): int {
        return $this->game;
    }

    /**
     * @param int $game
     */
    public function set_game(int $game): void {
        $this->game = $game;
    }

    /**
     * @return int
     */
    public function get_mdl_user(): int {
        return $this->mdl_user;
    }

    /**
     * @param int $mdl_user
     */
    public function set_mdl_user(int $mdl_user): void {
        $this->mdl_user = $mdl_user;
    }

    /**
     * @return bool
     */
    public function is_continue_on_failure(): bool {
        return $this->continue_on_failure;
    }

    /**
     * @param bool $continue_on_failure
     */
    public function set_continue_on_failure(bool $continue_on_failure): void {
        $this->continue_on_failure = $continue_on_failure;
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
    public function set_score(int $score): void {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function get_answers_total(): int {
        return $this->answers_total;
    }

    /**
     * @param int $answers_total
     */
    public function set_answers_total(int $answers_total): void {
        $this->answers_total = $answers_total;
    }

    /**
     * @return int
     */
    public function get_answers_correct(): int {
        return $this->answers_correct;
    }

    /**
     * @param int $answers_correct
     */
    public function set_answers_correct(int $answers_correct): void {
        $this->answers_correct = $answers_correct;
    }

    /**
     * @return string
     */
    public function get_state(): string {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function set_state(string $state): void {
        $this->state = $state;
    }
}
