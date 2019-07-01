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

use context;
use core\external\exporter;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class mdl_category_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mdl_category_dto extends exporter {

    /**
     * @var int
     */
    protected $contextid;
    /**
     * @var string
     */
    protected $contextname;
    /**
     * @var int
     */
    protected $categoryid;
    /**
     * @var string
     */
    protected $categoryname;
    /**
     * @var string
     */
    protected $ids;

    /**
     * mdl_category_dto constructor.
     *
     * @param string $ids
     * @param string $contextname
     * @param string $categoryname
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(string $ids, string $contextname, string $categoryname, context $context) {
        $tmpids = \explode(",", $ids);
        $this->contextid = \intval($tmpids[1]);
        $this->contextname = $contextname;
        $this->categoryid = \intval($tmpids[0]);
        $this->categoryname = $categoryname;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_TEXT,
                'description' => 'category id and context id concatenated with a comma',
            ],
            'context_id' => [
                'type' => PARAM_INT,
                'description' => 'id of the context',
            ],
            'context_name' => [
                'type' => PARAM_TEXT,
                'description' => 'name of the context (out of course name, course area name and core system)',
            ],
            'category_id' => [
                'type' => PARAM_INT,
                'description' => 'id of the category',
            ],
            'category_name' => [
                'type' => PARAM_TEXT,
                'description' => 'name of the category, including non-breaking whitespaces for hierarchy-like indentation',
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
            'id' => $this->ids,
            'context_id' => $this->contextid,
            'context_name' => $this->contextname,
            'category_id' => $this->categoryid,
            'category_name' => $this->categoryname,
        ];
    }
}
