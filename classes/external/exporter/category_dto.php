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
use mod_millionaire\model\category;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Class level_dto
 *
 * @package    mod_millionaire\external\exporter
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_dto extends exporter {

    /**
     * @var category
     */
    protected $category;

    /**
     * level_dto constructor.
     *
     * @param category $category
     * @param context $context
     *
     * @throws \coding_exception
     */
    public function __construct(category $category, context $context) {
        $this->category = $category;
        parent::__construct([], ['context' => $context]);
    }

    protected static function define_other_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'category id',
            ],
            'level' => [
                'type' => PARAM_INT,
                'description' => 'level id',
            ],
            'mdl_category' => [
                'type' => PARAM_INT,
                'description' => 'moodle category id',
            ],
            'subcategories' => [
                'type' => PARAM_BOOL,
                'description' => 'whether or not to include sub categories',
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
            'id' => $this->category->get_id(),
            'level' => $this->category->get_level(),
            'mdl_category' => $this->category->get_mdl_category(),
            'subcategories' => $this->category->includes_subcategories(),
        ];
    }
}
