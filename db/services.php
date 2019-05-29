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
    'mod_millionaire_get_current_gamesession' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'get_current_gamesession',
        'description' => 'Get the current gamesession for the logged in user, or create a new gamesession.',
        'type' => 'read',
        'ajax' => true,
    ],
    'mod_millionaire_get_current_question' => [
        'classname' => 'mod_millionaire\external\gamesessions',
        'methodname' => 'get_current_question',
        'description' => 'Gets or creates the current question from the current gamesession for the logged in user.',
        'type' => 'read',
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
    ]
];
