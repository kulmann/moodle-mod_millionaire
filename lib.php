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
 * Library of interface functions and constants for module millionaire
 *
 * All the core Moodle functions, needed to integrate this plugin into Moodle at all,
 * should be placed here.
 *
 * All the millionaire specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_completion\api;
use mod_millionaire\model\gamesession;
use mod_millionaire\util;

defined('MOODLE_INTERNAL') || die();

// different highscore modes
define('MOD_MILLIONAIRE_HIGHSCORE_MODE_BEST', 'best');
define('MOD_MILLIONAIRE_HIGHSCORE_MODE_LAST', 'last');
define('MOD_MILLIONAIRE_HIGHSCORE_MODE_AVERAGE', 'average');
define('MOD_MILLIONAIRE_HIGHSCORE_MODES', [
    MOD_MILLIONAIRE_HIGHSCORE_MODE_BEST,
    MOD_MILLIONAIRE_HIGHSCORE_MODE_LAST,
    MOD_MILLIONAIRE_HIGHSCORE_MODE_AVERAGE
]);

// available joker types
define('MOD_MILLIONAIRE_JOKER_CHANCE', 'chance');
define('MOD_MILLIONAIRE_JOKER_AUDIENCE', 'audience');
define('MOD_MILLIONAIRE_JOKER_FEEDBACK', 'feedback');
define('MOD_MILLIONAIRE_JOKERS', [
    MOD_MILLIONAIRE_JOKER_CHANCE,
    MOD_MILLIONAIRE_JOKER_AUDIENCE,
    MOD_MILLIONAIRE_JOKER_FEEDBACK
]);

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function millionaire_supports($feature) {
    switch ($feature) {
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_COMPLETION_HAS_RULES:
        case FEATURE_MOD_INTRO:
        case FEATURE_USES_QUESTIONS:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the millionaire quiz into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $millionaire Submitted data from the form in mod_form.php
 * @param mod_millionaire_mod_form $mform The form instance itself (if needed)
 *
 * @return int The id of the newly inserted millionaire record
 * @throws dml_exception
 */
function millionaire_add_instance(stdClass $millionaire, mod_millionaire_mod_form $mform = null) {
    global $DB;
    // pre-processing
    $millionaire->timecreated = time();
    millionaire_preprocess_form_data($millionaire);
    // insert into db
    $millionaire->id = $DB->insert_record('millionaire', $millionaire);
    // some additional stuff
    millionaire_after_add_or_update($millionaire);
    return $millionaire->id;
}

/**
 * Updates an instance of the millionaire in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $millionaire An object from the form in mod_form.php
 * @param mod_millionaire_mod_form $mform The form instance itself (if needed)
 *
 * @return boolean Success/Fail
 * @throws dml_exception
 */
function millionaire_update_instance(stdClass $millionaire, mod_millionaire_mod_form $mform = null) {
    global $DB;
    // pre-processing
    $millionaire->id = $millionaire->instance;
    millionaire_preprocess_form_data($millionaire);
    // update in db
    $result = $DB->update_record('millionaire', $millionaire);
    // some additional stuff
    millionaire_after_add_or_update($millionaire);
    return $result;
}


/**
 * Pre-process the millionaire options form data, making any necessary adjustments.
 * Called by add/update instance in this file.
 *
 * @param stdClass $millionaire The variables set on the form.
 */
function millionaire_preprocess_form_data(stdClass $millionaire) {
    // update timestamp
    $millionaire->timemodified = time();
    // trim name.
    if (!empty($millionaire->name)) {
        $millionaire->name = trim($millionaire->name);
    }
}

/**
 * This function is called at the end of millionaire_add_instance
 * and millionaire_update_instance, to do the common processing.
 *
 * @param stdClass $millionaire the quiz object.
 */
function millionaire_after_add_or_update(stdClass $millionaire) {
    // nothing to do for now...
}

/**
 * Removes an instance of the millionaire from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 *
 * @return boolean Success/Failure
 * @throws dml_exception
 * @throws coding_exception
 */
function millionaire_delete_instance($id) {
    global $DB;
    if (!$millionaire = $DB->get_record('millionaire', ['id' => $id])) {
        return false;
    }
    if (!$cm = get_coursemodule_from_instance('millionaire', $millionaire->id)) {
        return false;
    }
    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        return false;
    }
    $context = context_module::instance($cm->id);

    // get rid of all files
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    // delete completion data
    api::update_completion_date_event($cm->id, 'millionaire', $millionaire->id, null);

    // now delete actual game data
    $result = true;
    // game sessions, including chosen questions and jokers
    $gamesession_ids = $DB->get_fieldset_select('millionaire_gamesessions', 'id', 'game = :game', ['game' => $millionaire->id]);
    if ($gamesession_ids) {
        $result &= $DB->delete_records_list('millionaire_questions', 'gamesession', $gamesession_ids);
        $result &= $DB->delete_records_list('millionaire_jokers', 'gamesession', $gamesession_ids);
    }
    $result &= $DB->delete_records('millionaire_gamesessions', ['game' => $millionaire->id]);
    // levels and categories
    $levels_ids = $DB->get_fieldset_select('millionaire_levels', 'id', 'game = :game', ['game' => $millionaire->id]);
    if ($levels_ids) {
        $result &= $DB->delete_records_list('millionaire_categories', 'level', $levels_ids);
    }
    $result &= $DB->delete_records('millionaire_levels', ['game' => $millionaire->id]);
    $result &= $DB->delete_records('millionaire', ['id' => $millionaire->id]);
    return $result;
}

/**
 * Obtains the automatic completion state for this forum based on any conditions
 * in forum settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 * @throws dml_exception
 * @throws moodle_exception
 */
function millionaire_get_completion_state($course, $cm, $userid, $type) {
    list($course, $coursemodule) = get_course_and_cm_from_cmid($cm->id, 'millionaire');
    if (!($millionaire = util::get_game($coursemodule))) {
        throw new Exception("Can't find activity instance {$cm->instance}");
    }

    $result = $type;
    if ($millionaire->get_completionrounds()) {
        $value = $millionaire->get_completionrounds() <= $millionaire->count_finished_gamesessions($userid);
        if ($type == COMPLETION_AND) {
            $result &= $value;
        } else {
            $result |= $value;
        }
    }
    if ($millionaire->get_completionpoints()) {
        $value = $millionaire->get_completionrounds() <= $millionaire->calculate_total_score($userid);
        if ($type == COMPLETION_AND) {
            $result &= $value;
        } else {
            $result |= $value;
        }
    }
    return $result;
}

function millionaire_perform_completion($course, $cm, $millionaire) {
    global $USER;
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC && ($millionaire->completionrounds || $millionaire->completionpoints)) {
        $completion->update_state($cm, COMPLETION_COMPLETE, $USER->id);
    }
}
