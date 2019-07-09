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

use function assert;
use function usort;

defined('MOODLE_INTERNAL') || die();

/**
 * Class game
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class game extends abstract_model {

    /**
     * @var int Timestamp of creation of this game.
     */
    protected $timecreated;
    /**
     * @var int Timestamp of last update of this game.
     */
    protected $timemodified;
    /**
     * @var int Id of course.
     */
    protected $course;
    /**
     * @var string Name of this game activity.
     */
    protected $name;
    /**
     * @var string Currency for levels.
     */
    protected $currency_for_levels;
    /**
     * @var bool Whether or not the user is allowed to continue after answering a question wrong.
     */
    protected $continue_on_failure;
    /**
     * @var bool Whether or not questions can be presented more than once per user.
     */
    protected $question_repeatable;
    /**
     * @var bool Whether or not answers should be shuffled when displaying a question.
     */
    protected $question_shuffle_answers;
    /**
     * @var int Number of highscore entries shown in highscore list.
     */
    protected $highscore_count;
    /**
     * @var string The way we calculate highscores.
     */
    protected $highscore_mode;
    /**
     * @var bool Whether or not teachers will be shown in the highscore list.
     */
    protected $highscore_teachers;

    /**
     * game constructor.
     */
    function __construct() {
        parent::__construct('millionaire', 0);
        $this->timecreated = \time();
        $this->timemodified = \time();
        $this->course = 0;
        $this->name = '';
        $this->currency_for_levels = '€';
        $this->continue_on_failure = false;
        $this->question_repeatable = true;
        $this->question_shuffle_answers = true;
        $this->highscore_count = 5;
        $this->highscore_mode = MOD_MILLIONAIRE_HIGHSCORE_MODE_BEST;
        $this->highscore_teachers = false;
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
        $this->course = isset($data['course']) ? $data['course'] : 0;
        $this->name = isset($data['name']) ? $data['name'] : '';
        $this->currency_for_levels = isset($data['currency_for_levels']) ? $data['currency_for_levels'] : '€';
        $this->continue_on_failure = isset($data['continue_on_failure']) ? ($data['continue_on_failure'] == 1) : false;
        $this->question_repeatable = isset($data['question_repeatable']) ? ($data['question_repeatable'] == 1) : true;
        $this->question_shuffle_answers = isset($data['question_shuffle_answers']) ? ($data['question_shuffle_answers'] == 1) : true;
        $this->highscore_count = isset($data['highscore_count']) ? $data['highscore_count'] : 5;
        $this->highscore_mode = isset($data['highscore_mode']) ? $data['highscore_mode'] : MOD_MILLIONAIRE_HIGHSCORE_MODE_BEST;
        $this->highscore_teachers = isset($data['highscore_teachers']) ? ($data['highscore_teachers'] == 1) : false;
    }

    /**
     * Counts the active levels of this game.
     *
     * @return int
     * @throws \dml_exception
     */
    public function count_active_levels(): int {
        global $DB;
        $sql = "
            SELECT COUNT(id)
              FROM {millionaire_levels}
             WHERE game = :game AND state = :state
        ";
        $count = $DB->get_field_sql($sql, ['game' => $this->get_id(), 'state' => level::STATE_ACTIVE]);
        return $count === false ? 0 : $count;
    }

    /**
     * Gets an active level which belongs to this game and has the given $position value. Will return null
     * if no such level exists.
     *
     * @param int $position
     *
     * @return level|null
     * @throws \dml_exception
     */
    public function get_active_level_by_position($position) {
        global $DB;
        $sql = "
            SELECT *
              FROM {millionaire_levels}
             WHERE game = :game AND state = :state AND position = :position
        ";
        $record = $DB->get_record_sql($sql, ['game' => $this->get_id(), 'state' => level::STATE_ACTIVE, 'position' => $position]);
        if ($record === false) {
            return null;
        } else {
            $level = new level();
            $level->apply($record);
            return $level;
        }
    }

    /**
     * Gets all active levels for this game from the DB.
     *
     * @return level[]
     * @throws \dml_exception
     */
    public function get_active_levels() {
        global $DB;
        $sql_params = ['game' => $this->get_id(), 'state' => level::STATE_ACTIVE];
        $records = $DB->get_records('millionaire_levels', $sql_params, 'position ASC');
        $result = [];
        foreach ($records as $level_data) {
            $level = new level();
            $level->apply($level_data);
            $result[] = $level;
        }
        return $result;
    }

    /**
     * Goes through all active levels, fixing their individual position.
     *
     * @return void
     * @throws \dml_exception
     */
    public function fix_level_positions() {
        $levels = $this->get_active_levels();
        // sort levels ascending
        usort($levels, function(level $level1, level $level2) {
            $pos1 = $level1->get_position();
            $pos2 = $level2->get_position();
            if ($pos1 === $pos2) {
                return 0;
            }
            return ($pos1 < $pos2) ? -1 : 1;
        });
        // walk through sorted list and set new positions
        $pos = 0;
        foreach($levels as $level) {
            assert($level instanceof level);
            $level->set_position($pos++);
            $level->save();
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
    public function get_course(): int {
        return $this->course;
    }

    /**
     * @param int $course
     */
    public function set_course(int $course): void {
        $this->course = $course;
    }

    /**
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function set_name(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function get_currency_for_levels(): string {
        return $this->currency_for_levels;
    }

    /**
     * @param string $currency_for_levels
     */
    public function set_currency_for_levels(string $currency_for_levels): void {
        $this->currency_for_levels = $currency_for_levels;
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
     * @return bool
     */
    public function is_question_repeatable(): bool {
        return $this->question_repeatable;
    }

    /**
     * @param bool $question_repeatable
     */
    public function set_question_repeatable(bool $question_repeatable): void {
        $this->question_repeatable = $question_repeatable;
    }

    /**
     * @return bool
     */
    public function is_question_shuffle_answers(): bool {
        return $this->question_shuffle_answers;
    }

    /**
     * @param bool $question_shuffle_answers
     */
    public function set_question_shuffle_answers(bool $question_shuffle_answers): void {
        $this->question_shuffle_answers = $question_shuffle_answers;
    }

    /**
     * @return int
     */
    public function get_highscore_count(): int {
        return $this->highscore_count;
    }

    /**
     * @param int $highscore_count
     */
    public function set_highscore_count(int $highscore_count): void {
        $this->highscore_count = $highscore_count;
    }

    /**
     * @return string
     */
    public function get_highscore_mode(): string {
        return $this->highscore_mode;
    }

    /**
     * @param string $highscore_mode
     */
    public function set_highscore_mode(string $highscore_mode): void {
        $this->highscore_mode = $highscore_mode;
    }

    /**
     * @return bool
     */
    public function is_highscore_teachers(): bool {
        return $this->highscore_teachers;
    }

    /**
     * @param bool $highscore_teachers
     */
    public function set_highscore_teachers(bool $highscore_teachers): void {
        $this->highscore_teachers = $highscore_teachers;
    }
}
