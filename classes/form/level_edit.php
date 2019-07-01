<?php

namespace mod_millionaire\form;

use mod_millionaire\model\game;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/formslib.php");

class level_edit extends \moodleform {

    public function definition() {
        $mform = $this->_form;
        $game = $this->_customdata['game'];
        \assert($game instanceof game);
        $levelid = $this->_customdata['levelid'];
        $coursecontext = \context_module::instance($game->get_id())->get_course_context();
        $contexts = new \question_edit_contexts($coursecontext);
        $usable_question_contexts = $contexts->having_cap('moodle/question:useall');

        // Level id
        $mform->addElement('hidden', 'levelid');
        $mform->setType('levelid', PARAM_INT);
        if ($levelid) {
            $mform->setConstant('levelid', $levelid);
        }

        // Name
        $mform->addElement('text', 'name', get_string('admin_level_lbl_name', 'mod_millionaire'));
        $mform->setType('name', PARAM_TEXT);

        // Score
        $mform->addElement('text', 'score', get_string('admin_level_lbl_score', 'mod_millionaire'));
        $mform->addRule('score', get_string('required'), 'required');
        $mform->setType('score', PARAM_INT);

        // Safe spot
        $mform->addElement('advcheckbox', 'safe_spot', get_string('admin_level_lbl_safe_spot', 'mod_millionaire'), '&nbsp;');
        $mform->setDefault('safe_spot', 0);
        $mform->addHelpButton('safe_spot', 'admin_level_lbl_safe_spot', 'mod_millionaire');

        // question category
        $mform->addElement('questioncategory', 'mdl_category', get_string('admin_level_category', 'mod_millionaire'), ['contexts' => $usable_question_contexts]);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files) {
        return [];
    }
}
