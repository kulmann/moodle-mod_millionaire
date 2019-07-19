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
 * Class abstract_model
 *
 * @package    mod_millionaire\model
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class abstract_model extends \stdClass {

    /**
     * @var string
     */
    protected $table_name;
    /**
     * @var int
     */
    protected $id;

    /**
     * abstract_model constructor.
     *
     * @param $table_name
     * @param $id
     */
    function __construct($table_name, $id) {
        $this->table_name = $table_name;
        $this->id = $id ?: 0;
    }

    /**
     * Returns the id of this record.
     *
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Sets the id on this record.
     *
     * @param int $id
     */
    public function set_id(int $id) {
        $this->id = $id;
    }

    /**
     * Loads the data of this model instance by its id.
     *
     * @param int $id
     *
     * @return void The loaded data will be set inside this object.
     * @throws \dml_exception
     */
    public function load_data_by_id(int $id) {
        global $DB;
        $record = $DB->get_record(
            $this->table_name,
            ['id' => $id]
        );
        if ($record) {
            $this->apply($record);
        } else {
            throw new \dml_exception("item not found by id $id in table " . $this->table_name);
        }
    }

    /**
     * Updates or inserts this record.
     *
     * @return void
     * @throws \dml_exception
     */
    public function save() {
        global $DB;
        if (property_exists($this, 'timemodified')) {
            $this->timemodified = \time();
        }
        if ($this->get_id() === null || $this->get_id() === 0) {
            $insertedid = $DB->insert_record($this->get_table_name(), $this->to_data_object());
            $this->set_id($insertedid);
        } else {
            $DB->update_record($this->get_table_name(), $this->to_data_object());
        }
    }

    /**
     * Deletes this record from the db.
     *
     * @return void
     * @throws \dml_exception
     */
    public function delete() {
        global $DB;
        $DB->delete_records($this->table_name, ['id' => $this->get_id()]);
    }

    /**
     * Transforms this object into an array.
     *
     * @return array
     */
    public function to_array(): array {
        return get_object_vars($this);
    }

    /**
     * Transforms this object into a stdClass instance. Required for db inserts/updates.
     *
     * @return \stdClass
     */
    private function to_data_object(): \stdClass {
        $result = new \stdClass();
        $array = $this->to_array();
        foreach ($array as $key => $value) {
            $result->$key = $value;
        }
        return $result;
    }

    /**
     * Applies the data from $data to this object.
     *
     * @param array | \stdClass $data
     *
     * @return void
     */
    public abstract function apply($data);

    /**
     * Returns the name of the sql table this model object is built on.
     *
     * @return string
     */
    public function get_table_name(): string {
        return $this->table_name;
    }
}
