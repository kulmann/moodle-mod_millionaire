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

/**
 * Restore structure step for millionaire content
 *
 * @package    mod_millionaire
 * @copyright  2020 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Structure step to restore one millionaire activity
 */
class restore_millionaire_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines restore element's structure
     *
     * @return array
     * @throws base_step_exception
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('millionaire', '/activity/millionaire');
        $paths[] = new restore_path_element('millionaire_level', '/activity/millionaire/levels/level');
        $paths[] = new restore_path_element('millionaire_category', '/activity/millionaire/levels/level/categories/category');
        if ($userinfo) {
            $paths[] = new restore_path_element('millionaire_gamesession', '/activity/millionaire/gamesessions/gamesession');
            $paths[] = new restore_path_element('millionaire_question', '/activity/millionaire/gamesessions/gamesession/questions/question');
            $paths[] = new restore_path_element('millionaire_joker', '/activity/millionaire/gamesessions/gamesession/questions/question/jokers/joker');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process millionaire, inserting the record into the database.
     *
     * @param $data
     *
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_millionaire($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;

        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the millionaire record.
        $newitemid = $DB->insert_record('millionaire', $data);
        $this->set_mapping('millionaire', $oldid, $newitemid, true);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * @param $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_millionaire_level($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;

        $data->game = $this->get_new_parentid('millionaire');

        $newitemid = $DB->insert_record('millionaire_levels', $data);
        $this->set_mapping('millionaire_level', $oldid, $newitemid, true);
    }

    /**
     * @param $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_millionaire_category($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;

        $data->level = $this->get_new_parentid('millionaire_level');

        $newitemid = $DB->insert_record('millionaire_categories', $data);
        $this->set_mapping('millionaire_category', $oldid, $newitemid);
    }

    /**
     * @param $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_millionaire_gamesession($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;

        $data->game = $this->get_new_parentid('millionaire');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->mdl_user = $this->get_mappingid('user', $data->mdl_user);

        $newitemid = $DB->insert_record('millionaire_gamesessions', $data);
        $this->set_mapping('millionaire_gamesession', $oldid, $newitemid);
    }

    /**
     * @param $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_millionaire_question($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->level = $this->get_mappingid('millionaire_level', $data->level);

        $newitemid = $DB->insert_record('millionaire_questions', $data);
        $this->set_mapping('millionaire_question', $oldid, $newitemid);
    }

    /**
     * @param $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_millionaire_joker($data) {
        global $DB;
        $data = (object) $data;
        $oldid = $data->id;

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->gamesession = $this->get_mappingid('millionaire_gamesession', $data->gamesession);
        $data->question = $this->get_mappingid('millionaire_question', $data->question);

        $newitemid = $DB->insert_record('millionaire_jokers', $data);
        $this->set_mapping('millionaire_joker', $oldid, $newitemid);
    }

    /**
     * Additional work that needs to be done after steps executions.
     */
    protected function after_execute() {
        // Add files for intro field.
        $this->add_related_files('mod_millionaire', 'intro', null);
    }
}
