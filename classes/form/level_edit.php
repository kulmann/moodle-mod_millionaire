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

        // General section header.
        $header_lang_key = 'admin_level_title_' . ($levelid ? 'edit' : 'add');
        $mform->addElement('header', 'general', get_string($header_lang_key, 'mod_millionaire'));

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
        $mform->addRule('admin_level_lbl_score', get_string('required'), 'required');
        $mform->setType('admin_level_lbl_score', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files) {
        return [];
    }
}
