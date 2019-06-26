<?php

namespace mod_millionaire\form;

use mod_millionaire\model\game;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/formslib.php");

class level_edit extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        /**
         * @var game $game
         */
        $game = $this->_customdata['game'];

        /**
         * @var int $levelid
         */
        $levelid = $this->_customdata['levelid'];

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

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files) {
        return [];
    }
}
