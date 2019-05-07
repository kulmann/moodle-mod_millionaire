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
abstract class abstract_model {

    /**
     * Transforms this object into an array.
     *
     * @return array
     */
    public function to_array() {
        return get_object_vars($this);
    }

    /**
     * Transforms this object into a stdClass instance. Required for db inserts/updates.
     *
     * @return \stdClass
     */
    public function to_data_object() {
        $result = new \stdClass();
        $array = $this->to_array();
        foreach ($array as $key => $value) {
            $result->$key = $value;
        }
        return $result;
    }
}
