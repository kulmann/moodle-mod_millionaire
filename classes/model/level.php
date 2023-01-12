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

global $CFG;
require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * Class level
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class level extends abstract_model {

    const STATE_PRIVATE = 'private';
    const STATE_ACTIVE = 'active';
    const STATE_DELETED = 'deleted';

    /**
     * @var int The id of the millionaire instance this level belongs to.
     */
    protected $game;
    /**
     * @var string The state of the level, out of [active, deleted].
     */
    protected $state;
    /**
     * @var string The name of the level.
     */
    protected $name;
    /**
     * @var int Position for ordering levels.
     */
    protected $position;
    /**
     * @var int The score the user reaches when answering the question of this level correctly.
     */
    protected $score;
    /**
     * @var bool Whether or not this level is a safe spot.
     */
    protected $safe_spot;

    /**
     * level constructor.
     */
    function __construct() {
        parent::__construct('millionaire_levels', 0);
        $this->game = 0;
        $this->state = self::STATE_ACTIVE;
        $this->name = '';
        $this->position = -1;
        $this->score = 0;
        $this->safe_spot = false;
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
        $this->game = $data['game'];
        $this->state = isset($data['state']) ? $data['state'] : self::STATE_ACTIVE;
        $this->name =  isset($data['name']) ? $data['name'] : '';
        $this->position = isset($data['position']) ? $data['position'] : 0;
        $this->score = isset($data['score']) ? $data['score'] : 0;
        $this->safe_spot = isset($data['safe_spot']) ? ($data['safe_spot'] == 1) : false;
    }

    /**
     * Fetches all categories from the DB which belong to this level.
     *
     * @return category[]
     * @throws \dml_exception
     */
    public function get_categories(): array {
        global $DB;
        $sql_params = ['level' => $this->get_id()];
        $records = $DB->get_records('millionaire_categories', $sql_params);
        return \array_map(function($record) {
            $category = new category();
            $category->apply($record);
            return $category;
        }, $records);
    }

    /**
     * Returns one random question out of the categories that are assigned to this level.
     *
     * @return \question_definition
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function get_random_question(): \question_definition {
        global $DB;

        // collect all moodle question categories
        $mdl_category_ids = [];
        foreach($this->get_categories() as $category) {
            $mdl_category_ids = \array_merge($mdl_category_ids, $category->get_mdl_category_ids());
        }
        list($cat_sql, $cat_params) = $DB->get_in_or_equal($mdl_category_ids);

        // build query for moodle question selection
        $sql = "
        SELECT q.id
        FROM {question} q 
            INNER JOIN {qtype_multichoice_options} qmo ON q.id=qmo.questionid
            JOIN {question_versions} qv ON qv.questionid = q.id
            JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
            JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
        WHERE (qv.version = (SELECT MAX(v.version)
                            FROM {question_versions} v
                                JOIN {question_bank_entries} be ON be.id = v.questionbankentryid
                            WHERE be.id = qbe.id))
                            AND q.qtype = ? AND qmo.single = ? AND qc.id $cat_sql ";
        $params = \array_merge(["multichoice", 1], $cat_params);

        // Get all available questions.
        $available_ids = $DB->get_records_sql($sql, $params);
        if (!empty($available_ids)) {
            // Shuffle here because SQL RAND() can't be used.
            shuffle($available_ids);
            // Take the first one in the array.
            $id = \reset($available_ids)->id;
            return \question_bank::load_question($id, false);
        } else {
            throw new \dml_exception('no question available');
        }
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
    public function set_game(int $game) {
        $this->game = $game;
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
    public function set_state(string $state) {
        $this->state = $state;
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
    public function set_name(string $name) {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function get_position(): int {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function set_position(int $position) {
        $this->position = $position;
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
    public function is_safe_spot(): bool {
        return $this->safe_spot;
    }

    /**
     * @param bool $safe_spot
     */
    public function set_safe_spot(bool $safe_spot) {
        $this->safe_spot = $safe_spot;
    }
}
