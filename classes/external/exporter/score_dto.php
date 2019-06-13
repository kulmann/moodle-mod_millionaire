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

namespace mod_millionaire\external\exporter;

use coding_exception;
use context;
use core\external\exporter;
use mod_millionaire\model\game;
use mod_millionaire\model\level;
use mod_millionaire\model\question;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class score_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class score_dto extends exporter {

    /**
     * @var int
     */
    private $rank;
    /**
     * @var float
     */
    private $score;
    /**
     * @var int
     */
    private $sessions;
    /**
     * @var int
     */
    private $mdl_user;
    /**
     * @var string
     */
    private $mdl_user_name;
    /**
     * @var bool
     */
    private $teacher;

    /**
     * score_dto constructor.
     *
     * @param int $rank
     * @param float $score
     * @param int $sessions
     * @param int $mdl_user
     * @param string $mdl_user_name
     * @param bool $teacher
     * @param context $context
     *
     * @throws coding_exception
     */
    public function __construct($rank, $score, $sessions, $mdl_user, $mdl_user_name, $teacher, context $context) {
        $this->rank = $rank;
        $this->score = $score;
        $this->sessions = $sessions;
        $this->mdl_user = $mdl_user;
        $this->mdl_user_name = $mdl_user_name;
        $this->teacher = $teacher;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'rank' => [
                'type' => PARAM_INT,
                'description' => 'rank',
            ],
            'score' => [
                'type' => PARAM_FLOAT,
                'description' => 'score',
            ],
            'sessions' => [
                'type' => PARAM_INT,
                'description' => 'the number of game sessions of this user',
            ],
            'mdl_user' => [
                'type' => PARAM_INT,
                'description' => 'moodle user id',
            ],
            'mdl_user_name' => [
                'type' => PARAM_TEXT,
                'description' => 'the name of this user',
            ],
            'teacher' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not the moodle user is a teacher in the course',
            ],
        ];
    }

    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    protected function get_other_values(renderer_base $output) {
        return [
            'rank' => $this->rank,
            'score' => $this->score,
            'sessions' => $this->sessions,
            'mdl_user' => $this->mdl_user,
            'mdl_user_name' => $this->mdl_user_name,
            'teacher' => $this->teacher,
        ];
    }
}
