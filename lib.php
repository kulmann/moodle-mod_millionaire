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
define('MOD_MILLIONAIRE_JOKER_50_50', 'joker_50_50');
define('MOD_MILLIONAIRE_JOKER_AUDIENCE', 'joker_audience');
define('MOD_MILLIONAIRE_JOKER_HINT', 'joker_hint');
define('MOD_MILLIONAIRE_JOKERS', [
    MOD_MILLIONAIRE_JOKER_50_50,
    MOD_MILLIONAIRE_JOKER_AUDIENCE,
    MOD_MILLIONAIRE_JOKER_HINT
]);

// implemented question types
define('MOD_MILLIONAIRE_QTYPE_SINGLE_CHOICE_DB', 'multichoice');
define('MOD_MILLIONAIRE_VALID_QTYPES_DB', [
    MOD_MILLIONAIRE_QTYPE_SINGLE_CHOICE_DB,
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
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
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
 */
function millionaire_delete_instance($id) {
    global $DB;
    $result = true;
    $millionaire = $DB->get_record('millionaire', ['id' => $id], '*', MUST_EXIST);
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

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function millionaire_get_file_areas($course, $cm, $context) {
    return [];
}

/**
 * File browsing support for millionaire file areas
 *
 * @package mod_millionaire
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function millionaire_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the millionaire file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the millionaire's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 * @throws coding_exception
 * @throws moodle_exception
 * @throws require_login_exception
 * @package mod_millionaire
 * @category files
 */
function millionaire_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = []) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if (!has_capability('mod/millionaire:view', $context)) {
        return false;
    }

    $itemid = array_shift($args);

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/' . implode('/', $args) . '/'; // $args contains elements of the filepath
    }

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'mod_millionaire', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 8400, 0, $forcedownload, $options);
}

/* Navigation API */
/**
 * Extends the settings navigation with the millionaire settings
 *
 * This function is called when the context for the page is a millionaire module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $millionairenode millionaire administration node
 * @throws coding_exception
 * @throws moodle_exception
 */
function millionaire_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $millionairenode = null) {
    global $PAGE;
    // determine location of first custom nav element (after "edit settings" node and before "Locally assigned roles" node).
    $keys = $millionairenode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }
    // add custom nav elements
//    if (has_capability('mod/millionaire:manage', $PAGE->cm->context)) {
//        $node = navigation_node::create(get_string('levels_edit', 'millionaire'),
//            new moodle_url('/mod/millionaire/edit_levels.php', ['cmid' => $PAGE->cm->id]),
//            navigation_node::TYPE_SETTING, null, 'mod_millionaire_edit',
//            new pix_icon('t/edit', ''));
//        $millionairenode->add_node($node, $beforekey);
//        $node = navigation_node::create(get_string('control_edit', 'millionaire'),
//            new moodle_url('/mod/millionaire/edit_control.php', ['cmid' => $PAGE->cm->id]),
//            navigation_node::TYPE_SETTING, null, 'mod_millionaire_control',
//            new pix_icon('t/edit', ''));
//        $millionairenode->add_node($node, $beforekey);
//    }
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
 */
function millionaire_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    if (!($millionaire = $DB->get_record('millionaire', ['id' => $cm->instance]))) {
        throw new Exception("Can't find millionaire {$cm->instance}");
    }
    $result = $type;
    if ($millionaire->completionrounds) {
        $sqlParams = ['game' => $millionaire->id, 'mdl_user' => $userid, 'finished' => true];
        $value = $millionaire->completionrounds <= $DB->count_records('millionaire_gamesessions', $sqlParams);
        if ($type == COMPLETION_AND) {
            $result &= $value;
        } else {
            $result |= $value;
        }
    }
    if ($millionaire->completionpoints) {
        $value = $millionaire->completionpoints <= \mod_millionaire\util\highscore_utils::calculate_score($millionaire, $userid);
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

function millionaire_reset_progress($course, $cm, $millionaire) {
    global $DB;
    $gamesession_ids = $DB->get_fieldset_select('millionaire_gamesessions', 'id', 'game = :game', ['game' => $millionaire->id]);
    if ($gamesession_ids) {
        $DB->delete_records_list('millionaire_questions', 'gamesession', $gamesession_ids);
        $DB->delete_records_list('millionaire_jokers', 'gamesession', $gamesession_ids);
    }
    $DB->delete_records('millionaire_gamesessions', ['game' => $millionaire->id]);
}

function millionaire_reset_levels($millionaire) {
    global $DB;
    $level_ids = $DB->get_fieldset_select('millionaire_levels', 'id', 'game=:game', ['game' => $millionaire->id]);
    if ($level_ids) {
        $DB->delete_records_list('millionaire_categories', 'level', $level_ids);
        $DB->delete_records_list('millionaire_levels', 'id', $level_ids);
    }
    millionaire_reset_progress(null, null, $millionaire);
}

function millionaire_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'millionaireheader', get_string('modulenameplural', 'millionaire'));
    $mform->addElement('checkbox', 'reset_millionaire_progress', get_string('course_reset_include_progress', 'millionaire'));
    $mform->disabledIf('reset_millionaire_progress', 'reset_millionaire_levels', 'checked');
    $mform->addElement('checkbox', 'reset_millionaire_levels', get_string('course_reset_include_levels', 'millionaire'));
}

function millionaire_reset_userdata($data) {
    global $DB;
    $status = [];
    $componentstr = get_string('modulenameplural', 'millionaire');
    $wwIds = $DB->get_fieldset_select('millionaire', 'id', 'course=:course', ['course' => $data->courseid]);
    if (!empty($data->reset_millionaire_levels)) {
        foreach ($wwIds as $id) {
            $ww = new stdClass();
            $ww->id = $id;
            $ww->course = $data->courseid;
            millionaire_reset_levels($ww);
        }
        $status[] = ['component' => $componentstr, 'item' => get_string('course_reset_include_progress', 'millionaire'), 'error' => false];
        $status[] = ['component' => $componentstr, 'item' => get_string('course_reset_include_levels', 'millionaire'), 'error' => false];
    } else {
        if (!empty($data->reset_millionaire_progress)) {
            foreach ($wwIds as $id) {
                $ww = new stdClass();
                $ww->id = $id;
                $ww->course = $data->courseid;
                millionaire_reset_progress(null, null, $ww);
            }
            $status[] = ['component' => $componentstr, 'item' => get_string('course_reset_include_progress', 'millionaire'), 'error' => false];
        }
    }
    return $status;
}

function millionaire_reset_course_form_defaults($course) {
    return ['reset_millionaire_progress' => 1, 'reset_millionaire_levels' => 0];
}
