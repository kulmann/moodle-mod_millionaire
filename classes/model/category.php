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
require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/engine/bank.php');

/**
 * Class category
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends abstract_model {

    /**
     * @var int The id of the level this question was chosen for.
     */
    protected $level;
    /**
     * @var int The id of the moodle question category.
     */
    protected $mdl_category;
    /**
     * @var bool Whether or not to include subcategories when choosing a question.
     */
    protected $subcategories;

    /**
     * category constructor.
     */
    function __construct() {
        parent::__construct('millionaire_categories', 0);
        $this->level = 0;
        $this->mdl_category = 0;
        $this->subcategories = true;
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
        $this->level = $data['level'];
        $this->mdl_category = $data['mdl_category'];
        $this->subcategories = isset($data['subcategories']) ? ($data['subcategories'] == 1) : false;
    }

    /**
     * Finds a random question id from this category (and its subcategories if allowed) and
     * returns the moodle question definition for it.
     *
     * @return \question_definition
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_random_question(): \question_definition {
        global $DB;
        // build sql for category id(s)
        if ($this->includes_subcategories()) {
            $category_ids = \question_categorylist($this->get_mdl_category());
        } else {
            $category_ids = [$this->get_mdl_category()];
        }
        list($cat_sql, $cat_params) = $DB->get_in_or_equal($category_ids);
        // build sql for question type(s)
        list($qtype_sql, $qtype_params) = $DB->get_in_or_equal(MOD_MILLIONAIRE_VALID_QTYPES_DB);
        // actual query
        $sql = "
            SELECT id
              FROM {question}
             WHERE category $cat_sql AND qtype $qtype_sql 
        ";

        $params = \array_merge($cat_params, $qtype_params);
        // Get all available questions.
        $availableids = $DB->get_records_sql($sql, $params);
        if ($availableids) {
            // Shuffle here because SQL RAND() can't be used.
            shuffle($availableids);
            // Take the first one in the array.
            $id = $availableids[0]->id;
            return \question_bank::load_question($id, false);
        } else {
            throw new \dml_exception('not found');
        }
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
    public function get_mdl_category(): int {
        return $this->mdl_category;
    }

    /**
     * @param int $mdl_category
     */
    public function set_mdl_category(int $mdl_category) {
        $this->mdl_category = $mdl_category;
    }

    /**
     * @return bool
     */
    public function includes_subcategories(): bool {
        return $this->subcategories;
    }

    /**
     * @param bool $subcategories
     */
    public function set_includes_subcategories(bool $subcategories) {
        $this->subcategories = $subcategories;
    }
}
