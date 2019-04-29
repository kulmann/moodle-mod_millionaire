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

namespace mod_millionaire\external;

use external_function_parameters;
use external_multiple_structure;
use external_value;
use mod_millionaire\external\exporter\level;

defined('MOODLE_INTERNAL') || die();

/**
 * Class levels
 *
 * @package    mod_millionaire\external
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class levels extends \external_api {
    public static function get_levels_parameters() {
        return new external_function_parameters([
            'coursemoduleid' => new external_value(PARAM_INT, 'course module id'),
        ]);
    }

    public static function get_levels_returns() {
        return new external_multiple_structure(
            level::get_read_structure()
        );
    }

    /**
     * Get all levels.
     *
     * @param $coursemoduleid
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \restricted_context_exception
     */
    public static function get_levels($coursemoduleid) {
        $params = ['coursemoduleid' => $coursemoduleid];
        $params = self::validate_parameters(self::get_levels_parameters(), $params);

        list($course, $coursemodule) = get_course_and_cm_from_cmid($params['coursemoduleid'], 'millionaire');
        self::validate_context($coursemodule->context);

        global $PAGE, $DB;
        $renderer = $PAGE->get_renderer('core');
        $ctx = $coursemodule->context;

        $result = [];
        $levels = $DB->get_records('millionaire_levels', ['game' => $coursemodule->instance]);
        foreach ($levels as $level) {
            $exporter = new level($level, $ctx);
            $result[] = $exporter->export($renderer);
        }
        return $result;
    }
}
