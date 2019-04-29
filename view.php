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
 * Prints a particular instance of millionaire
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('lib.php');

// if url has form view.php?id=xy then redirect to to view.php/xy
$coursemoduleid = optional_param('id', 0, PARAM_INT);
if ($coursemoduleid > 0) {
    $path = '/mod/millionaire/view.php/' . $coursemoduleid . '/';
    redirect(new \moodle_url($path));
}
// Support for Vue.js Router and its URL structure.
$paths = explode('/', $_SERVER['REQUEST_URI']);
$baseindex = array_search('view.php', $paths);
if (count($paths) > $baseindex + 1) {
    $coursemoduleid = intval($paths[$baseindex + 1]);
}

list($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'millionaire');

require_login($course, true, $coursemodule);

$title = get_string('modulename', 'mod_millionaire');
$url = new moodle_url('/mod/millionaire/view.php', ['id' => $coursemoduleid]);

$PAGE->set_context($coursemodule->context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

$PAGE->requires->js_call_amd('mod_millionaire/app-lazy','init', [
    'coursemoduleid' => $coursemodule->id,
    'contextid' => $coursemodule->context->id,
]);

echo $OUTPUT->header();
?>
    <div id="mod-millionaire-app">
        <router-view></router-view>
    </div>
<?php
echo $OUTPUT->footer($course);
