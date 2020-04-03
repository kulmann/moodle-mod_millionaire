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
 * Defines backup structure steps for millionaire content.
 *
 * @package    mod_millionaire
 * @copyright  2020 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete millionaire structure for backup, with id annotations
 */
class backup_millionaire_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines backup element's structure
     *
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define main activity element
        $activity = new backup_nested_element('millionaire', ['id'], [
            'timecreated', 'timemodified', 'course', 'name', 'intro', 'introformat', 'grade',
            'currency_for_levels', 'continue_on_failure', 'question_repeatable', 'question_shuffle_answers',
            'highscore_count', 'highscore_mode', 'highscore_teachers', 'completionrounds', 'completionpoints'
        ]);
        $activity->set_source_table('millionaire', ['id' => backup::VAR_ACTIVITYID]);

        // define game structure: levels and categories
        $levels = new backup_nested_element('levels');
        $activity->add_child($levels);
        $level = new backup_nested_element('level', ['id'], [
            'game', 'state', 'name', 'position', 'score', 'safe_spot'
        ]);
        $levels->add_child($level);
        $categories = new backup_nested_element('categories');
        $level->add_child($categories);
        $category = new backup_nested_element('category', ['id'], [
            'level', 'mdl_category', 'subcategories'
        ]);
        $categories->add_child($category);

        // define sources for structural game data
        $level->set_source_table('millionaire_levels', ['game' => backup::VAR_ACTIVITYID], 'id ASC');
        $category->set_source_table('millionaire_categories', ['level' => backup::VAR_PARENTID], 'id ASC');

        // define user data structure: gamesessions, questions and jokers
        $gamesessions = new backup_nested_element('gamesessions');
        $activity->add_child($gamesessions);
        $gamesession = new backup_nested_element('gamesession', ['id'], [
            'timecreated', 'timemodified', 'game', 'mdl_user', 'continue_on_failure', 'score',
            'answers_total', 'answers_correct', 'state', 'won'
        ]);
        $gamesessions->add_child($gamesession);
        $questions = new backup_nested_element('questions');
        $gamesession->add_child($questions);
        $question = new backup_nested_element('question', ['id'], [
            'timecreated', 'timemodified', 'gamesession', 'level', 'mdl_question', 'mdl_answers_order', 'mdl_answer',
            'score', 'correct', 'finished'
        ]);
        $questions->add_child($question);
        $jokers = new backup_nested_element('jokers');
        $question->add_child($jokers);
        $joker = new backup_nested_element('joker', ['id'], [
            'timecreated', 'timemodified', 'gamesession', 'question', 'joker_type', 'joker_data'
        ]);
        $jokers->add_child($joker);

        // define sources for user data
        if ($userinfo) {
            $gamesession->set_source_table('millionaire_gamesessions', ['game' => backup::VAR_ACTIVITYID], 'id ASC');
            $question->set_source_table('millionaire_questions', ['gamesession' => backup::VAR_PARENTID], 'id ASC');
            $joker->set_source_table('millionaire_jokers', ['question' => backup::VAR_PARENTID], 'id ASC');
        }

        // Define id annotations
        $gamesession->annotate_ids('user', 'mdl_user');

        // Define file annotations
        $activity->annotate_files('mod_millionaire', 'intro', null, null);

        // Return the root element (millionaire), wrapped into standard activity structure.
        return $this->prepare_activity_structure($activity);
    }
}
