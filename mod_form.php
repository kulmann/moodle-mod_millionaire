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
 * The main millionaire configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/lib/questionlib.php');

/**
 * Module instance settings form
 */
class mod_millionaire_mod_form extends moodleform_mod {

    private $completionModes = ['completionrounds', 'completionpoints'];

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('millionairename', 'millionaire'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'millionairename', 'millionaire');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements(get_string('introduction', 'millionaire'));

        // Game options
        $mform->addElement('header', 'game_options_fieldset', get_string('game_options_fieldset', 'millionaire'));
        // ... currency symbol for levels
        $mform->addElement('text', 'currency_for_levels', get_string('currency_for_levels', 'millionaire'));
        $mform->setType('currency_for_levels', PARAM_TEXT);
        $mform->setDefault('currency_for_levels', '€');
        $mform->addHelpButton('currency_for_levels', 'currency_for_levels', 'millionaire');
        // ... continue game on wrong answers?
        $mform->addElement('advcheckbox', 'continue_on_failure', get_string('continue_on_failure', 'millionaire'), '&nbsp;');
        $mform->setDefault('continue_on_failure', 0);
        $mform->addHelpButton('continue_on_failure', 'continue_on_failure', 'millionaire');
        // ... question repeatable?
        $mform->addElement('advcheckbox', 'question_repeatable', get_string('question_repeatable', 'millionaire'), '&nbsp;');
        $mform->setDefault('question_repeatable', 1);
        $mform->addHelpButton('question_repeatable', 'question_repeatable', 'millionaire');
        // ... shuffle answers?
        $mform->addElement('advcheckbox', 'question_shuffle_answers', get_string('question_shuffle_answers', 'millionaire'), '&nbsp;');
        $mform->setDefault('question_shuffle_answers', 1);
        $mform->addHelpButton('question_shuffle_answers', 'question_shuffle_answers', 'millionaire');
        // ... highscore count
        $mform->addElement('text', 'highscore_count', get_string('highscore_count', 'millionaire'), array('size' => 5));
        $mform->setType('highscore_count', PARAM_INT);
        $mform->setDefault('highscore_count', 5);
        $mform->addHelpButton('highscore_count', 'highscore_count', 'millionaire');
        // ... highscore mode
        $highscore_modes = [];
        foreach (MOD_MILLIONAIRE_HIGHSCORE_MODES as $mode) {
            $highscore_modes[$mode] = get_string('highscore_mode_' . $mode, 'millionaire');
        }
        $mform->addElement('select', 'highscore_mode', get_string('highscore_mode', 'millionaire'), $highscore_modes);
        $mform->setDefault('highscore_mode', MOD_MILLIONAIRE_HIGHSCORE_MODE_BEST);
        $mform->addHelpButton('highscore_mode', 'highscore_mode', 'millionaire');

        $mform->addElement('advcheckbox', 'highscore_teachers', get_string('highscore_teachers', 'millionaire'), '&nbsp;');
        $mform->setDefault('highscore_teachers', 0);
        $mform->addHelpButton('highscore_teachers', 'highscore_teachers', 'millionaire');

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        parent::data_preprocessing($default_values);
        foreach ($this->completionModes as $mode) {
            $default_values[$mode . 'enabled'] = !empty($default_values[$mode]) ? 1 : 0;
            if (empty($default_values[$mode])) {
                $default_values[$mode] = 1;
            }
        }
    }

    function add_completion_rules() {
        $mform = $this->_form;
        $result = [];
        foreach ($this->completionModes as $mode) {
            $group = array();
            $group[] = $mform->createElement('checkbox', $mode . 'enabled', '', get_string($mode, 'millionaire'));
            $group[] = $mform->createElement('text', $mode, '', array('size' => 3));
            $mform->setType($mode, PARAM_INT);
            $mform->addGroup($group, $mode . 'group', get_string($mode . 'label', 'millionaire'), array(' '), false);
            $mform->disabledIf($mode, $mode . 'enabled', 'notchecked');
            $result[] = $mode . 'group';
        }
        return $result;
    }

    function completion_rule_enabled($data) {
        foreach ($this->completionModes as $mode) {
            if (!empty($data[$mode . 'enabled']) && $data[$mode] !== 0) {
                return true;
            }
        }
        return false;
    }

    function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Turn off completion settings if the checkboxes aren't ticked
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionroundsenabled) || !$autocompletion) {
                $data->completionrounds = 0;
            }
            if (empty($data->completionpointsenabled) || !$autocompletion) {
                $data->completionpoints = 0;
            }

        }
        return $data;
    }


}
