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
 * This file defines available ajax calls for mod_millionaire.
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = [
    'mod_millionaire_get_levels' => [
        'classname' => 'mod_millionaire\external\levels',
        'methodname' => 'get_levels',
        'description' => 'Get levels for the game overview.',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_get_level_categories' => [
        'classname' => 'mod_millionaire\external\levels',
        'methodname' => 'get_level_categories',
        'description' => 'Get categories for one specific level.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_set_level_position' => [
        'classname' => 'mod_millionaire\external\levels',
        'methodname' => 'set_level_position',
        'description' => 'Modify the level position by 1 (up or down).',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_delete_level' => [
        'classname' => 'mod_millionaire\external\levels',
        'methodname' => 'delete_level',
        'description' => 'Delete a certain level',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_save_level' => [
        'classname' => 'mod_millionaire\external\levels',
        'methodname' => 'save_level',
        'description' => 'Save a certain level and its categories',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_get_game' => [
        'classname' => 'mod_millionaire\external\game',
        'methodname' => 'get_game',
        'description' => 'Get options of the game and logged in user',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_create_gamesession' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'create_gamesession',
        'description' => 'Dumps all running game sessions of the current user and creates a new one.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_close_gamesession' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'close_gamesession',
        'description' => 'Sets the state of the given game session to FINISHED.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_cancel_gamesession' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'cancel_gamesession',
        'description' => 'Sets the state of the given game session to DUMPED.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_get_current_gamesession' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'get_current_gamesession',
        'description' => 'Get the current gamesession for the logged in user, or create a new gamesession.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_get_question' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'get_question',
        'description' => 'Gets or creates the current question from the current gamesession for the logged in user.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_submit_answer' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'submit_answer',
        'description' => 'Submit answer for the current question',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_get_mdl_question' => [
        'classname' => 'mod_millionaire\external\questionbank',
        'methodname' => 'get_mdl_question',
        'description'  => 'Retrieves the data of the given moodle question.',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_get_mdl_answers' => [
        'classname' => 'mod_millionaire\external\questionbank',
        'methodname' => 'get_mdl_answers',
        'description'  => 'Retrieves the moodle answers for a given moodle question.',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_get_mdl_categories' => [
        'classname' => 'mod_millionaire\external\questionbank',
        'methodname' => 'get_mdl_categories',
        'description'  => 'Retrieves the moodle question categories which are applicable for this game.',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_get_used_jokers' => [
        'classname' => 'mod_millionaire\external\jokers',
        'methodname' => 'get_used_jokers',
        'description' => 'Retrieves the used jokers for a given gamesession.',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_submit_joker' => [
        'classname' => 'mod_millionaire\external\jokers',
        'methodname' => 'submit_joker',
        'description' => 'Submits a joker type for a given gamesession and question.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_millionaire_get_scores_global' => [
        'classname' => 'mod_millionaire\external\scores',
        'methodname' => 'get_scores_global',
        'description' => 'Gets a list of scores',
        'type' => 'read',
        'ajax' => true,
    ],
];
