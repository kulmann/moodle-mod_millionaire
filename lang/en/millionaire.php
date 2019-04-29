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
 * English strings for millionaire
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* system */
$string['modulename'] = 'Millionaire Quiz';
$string['modulenameplural'] = 'Millionaire Quizzes';
$string['modulename_help'] = 'This is a "who wants to be a millionaire" style quiz game. Course participants get ranked by their highscore.';
$string['pluginadministration'] = 'Millionaire Administration';
$string['pluginname'] = 'Millionaire';
$string['millionaire'] = 'Millionaire';
$string['millionaire:addinstance'] = 'Add a new Millionaire quiz';
$string['millionaire:submit'] = 'Submit Millionaire quiz';
$string['millionaire:view'] = 'View Millionaire quiz';
$string['millionairename'] = 'Name';
$string['millionairename_help'] = 'Please provide a name for your millionaire quiz.';
$string['introduction'] = 'Description';

/* activity admin form: game options */
$string['game_options_fieldset'] = 'Game options';
$string['currency_for_levels'] = 'Score currency';
$string['currency_for_levels_help'] = 'Symbol / Text will be appended after each level score.';
$string['continue_on_failure'] = 'Continue Game?';
$string['continue_on_failure_help'] = 'If enabled, the user will continue the game although a wrong answer has been given.';
$string['question_repeatable'] = 'Questions repeatable?';
$string['question_repeatable_help'] = 'If enabled, questions might be repeated on consecutive runs of the game by te same user. This feature makes use of the individual setting of a question about whether or not its answers may be shuffled.';
$string['question_shuffle_answers'] = 'Shuffle answers?';
$string['question_shuffle_answers_help'] = 'If enabled, the answers of questions will be shuffled.';
$string['highscore_count'] = 'Highscore entries';
$string['highscore_count_help'] = 'Please decide how many rows the highscore table will show (default: 5). If you select 0, no highscores will be shown at all.';
$string['completionrounds'] = 'Student has to play a number of rounds';
$string['completionroundslabel'] = 'Played rounds';
$string['completionpoints'] = 'Student has to achieve a certain highscore';
$string['completionpointslabel'] = 'Achieved highscore';
$string['highscore_mode'] = 'Highscore mode';
$string['highscore_mode_best'] = 'Best score';
$string['highscore_mode_last'] = 'Most recent score';
$string['highscore_mode_average'] = 'Average score';
$string['highscore_mode_help'] = 'Please select which scoring mode you want to use for the highscore calculation of a user.';
$string['highscore_teachers'] = 'Teachers appear in highscore list?';
$string['highscore_teachers_help'] = 'If enabled teachers scores will appear in highscore list.';

/* activity edit page: topics */
$string['topics_edit'] = 'Edit topics';
$string['topic_edit_new'] = 'Add new topic in &quot;<i>{$a}</i>&quot;';
$string['topic_edit_x'] = 'Edit topic in &quot;<i>{$a}</i>&quot;';
$string['topics_edit_x'] = 'Edit topics in &quot;<i>{$a}</i>&quot;';
$string['topics_edit_empty'] = 'No topics added, yet.';
$string['topics_edit_button_add'] = 'Add topic';
$string['topics_edit_button_edit'] = 'Edit topic';
$string['topics_edit_button_categories'] = 'Edit categories';
$string['topics_edit_button_delete'] = 'Delete topic';
$string['topics_edit_button_move_up'] = 'Move topic up';
$string['topics_edit_button_move_down'] = 'Move topic down';
$string['topics_edit_category_count_1'] = '1 category';
$string['topics_edit_category_count_n'] = '{$a} categories';
$string['topics_edit_question_count_1'] = '1 question';
$string['topics_edit_question_count_n'] = '{$a} questions';
$string['topics_form_submit'] = 'Save';
$string['topics_form_cancel'] = 'Cancel';
$string['topics_form_name'] = 'Name';
$string['topics_form_name_help'] = 'The topic will be shown to the user with this name.';
$string['topics_form_name_error_required'] = 'Please provide a name for this topic.';
$string['topics_form_score'] = 'Score';
$string['topics_form_score_help'] = 'Score to be reached on correct answer.';
$string['topics_form_score_error_required'] = 'Please define a score greater than 0.';
$string['topics_form_safe_spot'] = 'Safe level';
$string['topics_form_safe_spot_help'] = 'When game option &quot;Continue Game?&quot; is inactive, the player falls back to the nearest safe level when giving an incorrect answer.';
$string['topics_form_color'] = 'Color';
$string['topics_form_color_help'] = 'Instead of an image you may want to select a color which represents this topic on the game board. Please use a html color code in hexadecimal format for this (e.g. #FF5733, with or without the hash sign).';
$string['topics_form_color_invalid'] = 'The html color code needs to be in hexadecimal format (e.g. #FF5533 or #F53). The hash sign will be prefixed if it is missing.';
$string['topics_form_preview'] = 'Preview';
$string['category_edit_new'] = 'Add category for topic &quot;<i>{$a}</i>&quot;';
$string['category_edit_x'] = 'Edit category of topic &quot;<i>{$a}</i>&quot;';
$string['categories_edit_x'] = 'Edit categories of topic &quot;<i>{$a}</i>&quot;';
$string['categories_edit_error_access_topic'] = 'The topic doesn\'t belong to the activity.';
$string['categories_edit_error_access_category'] = 'The category doesn\'t belong to the topic.';
$string['categories_edit_empty'] = 'No categories added, yet.';
$string['categories_edit_button_add'] = 'Add category';
$string['categories_edit_button_edit'] = 'Edit category';
$string['categories_edit_button_delete'] = 'Delete category';
$string['categories_edit_button_back_to_topics'] = 'To topics list';
$string['category_form_name'] = 'Name';
$string['category_form_name_help'] = 'This allows to specify an identifier for this category in the category list of your topic. This might come in handy when you specify certain tags and want to show this within the category list.';
$string['category_form_category'] = 'Category';
$string['category_form_category_help'] = 'Please select a category to use questions from within the topic.';
$string['category_form_subcategories'] = 'Subcategories';
$string['category_form_subcategories_help'] = 'If enabled, the question from subcategories of the selected category will be used, too. If disabled, only questions which are assigned directly to the selected category will be used.';
$string['category_form_tags_all'] = 'Use all tags';
$string['category_form_tags_all_help'] = 'If enabled, the questions will not be filtered by tags. If disabled, only questions which have been tagged with the specified tags will be used.';
$string['category_form_tags'] = 'Tags';
$string['category_form_tags_help'] = 'Please select the tags a question needs to be taggged with in order to be used. Note: the listed tags are those which have been used in the selected category.';
$string['topic_delete_confirm_title'] = 'Delete topic?';
$string['topic_delete_confirm_message'] = 'Are you sure you want to delete this topic? this action is irreversible.';
$string['category_delete_confirm_title'] = 'Delete category?';
$string['category_delete_confirm_message'] = 'Are you sure you want to delete this category? this action is irreversible.';

/* activity edit page: control */
$string['control_edit'] = 'Control';
$string['control_edit_title'] = 'Control Options';
$string['reset_progress_heading'] = 'Reset Progress';
$string['reset_progress_button'] = 'Reset Progress';
$string['reset_progress_confirm_title'] = 'Confirm Reset Progress';
$string['reset_progress_confirm_question'] = 'Are you sure you want to reset the progress? this will delete all the results and is irreversible';

/* course reset */
$string['course_reset_include_progress'] = 'Reset progress (Highscores etc.)';
$string['course_reset_include_topics'] = 'Reset topics etc. (Everything will be deleted!)';

/* game gui */
$string['game_screen_title'] = 'Play »Who wants to be a millionaire«';
$string['topic'] = 'Topic';
$string['highscore_name'] = 'Name';
$string['highscore_sessions'] = 'Sessions';
$string['highscore_score'] = 'Highscore';
$string['highscore_rank'] = 'Rank';
$string['highscore_msg_none'] = 'There is no highscore, yet.';
$string['question_question'] = 'Question';
$string['question_score'] = 'Score';
$string['question_jokers'] = 'Lifelines';
$string['question_joker_used'] = ' - used';
$string['question_joker_50_50'] = 'Erase two wrong answers';
$string['question_joker_audience'] = 'Ask the audience';
$string['question_joker_audience_title'] = 'Audience lifeline:';
$string['question_joker_hint'] = 'Get a hint';
$string['question_joker_hint_title'] = 'Hint:';
$string['question_joker_hint_unavailable'] = 'Unfortunately there is no hint available.';
$string['replay'] = 'Play again';
$string['next_question'] = 'Continue';
$string['game_insufficient_questions'] = 'There are not enough questions for further rounds.';
$string['answer_text_true'] = 'True';
$string['answer_text_false'] = 'False';
