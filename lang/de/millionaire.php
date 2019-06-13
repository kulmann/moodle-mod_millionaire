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
 * German strings for millionaire
 *
 * @package    mod_millionaire
 * @copyright  2019 Benedikt Kulmann <b@kulmann.biz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* system */
$string['modulename'] = 'Millionaire Quiz';
$string['modulenameplural'] = 'Millionaire Quizzes';
$string['modulename_help'] = 'Ein an "Wer wird Millionär" angelehntes Quiz-Spiel. Die Kursteilnehmer können mit Hilfe ihrer erreichten Gewinnstufe in eine Rangliste sortiert werden.';
$string['pluginadministration'] = 'Millionaire Quiz Administration';
$string['pluginname'] = 'Millionaire';
$string['millionaire'] = 'Millionaire';
$string['millionaire:addinstance'] = 'Millionaire Quiz hinzufügen';
$string['millionaire:submit'] = 'Millionaire Quiz speichern';
$string['millionaire:view'] = 'Millionaire Quiz anzeigen';
$string['millionairename'] = 'Name';
$string['millionairename_help'] = 'Bitte vergeben Sie einen Namen für dieses Millionaire Quiz.';
$string['introduction'] = 'Beschreibung';

/* activity admin form: game options */
$string['game_options_fieldset'] = 'Spieloptionen';
$string['currency_for_levels'] = 'Punkte-Währung';
$string['currency_for_levels_help'] = 'Symbol / Text wird an jeden Punktestand angefügt.';
$string['continue_on_failure'] = 'Weiterspielen?';
$string['continue_on_failure_help'] = 'Wenn diese Option aktiviert ist, kann weitergespielt werden, obwohl eine Frage falsch beantwortet wurde.';
$string['question_repeatable'] = 'Fragen wiederholbar?';
$string['question_repeatable_help'] = 'Wenn diese Option aktiviert ist, können Fragen bei mehrfacher Durchführung durch einen Benutzer insgesamt häufiger als einmal vorkommen.';
$string['question_shuffle_answers'] = 'Antworten mischen?';
$string['question_shuffle_answers_help'] = 'Wenn diese Option aktiviert ist, werden die Antworten der Fragen gemischt bevor sie angezeigt werden.';
$string['highscore_count'] = 'Länge der Highscore-Liste';
$string['highscore_count_help'] = 'Hier kann die Anzahl der Einträge definiert werden, die in der Highscore-Tabelle nach einem abgeschlossenen Spiel angezeigt wird (Standard: 5). Wenn Sie 0 eingeben, werden nach einem abgeschlossenen Spiel keine Highscores gezeigt.';
$string['completionrounds'] = 'Teilnehmer/in muss Anzahl Runden spielen';
$string['completionroundslabel'] = 'Gespielte Runden';
$string['completionpoints'] = 'Teilnehmer/in muss einen bestimmten Highscore erreichen';
$string['completionpointslabel'] = 'Erreichter Highscore';
$string['highscore_mode'] = 'Highscore-Modus';
$string['highscore_mode_best'] = 'Beste Gewinnstufe';
$string['highscore_mode_last'] = 'Neueste Gewinnstufe';
$string['highscore_mode_average'] = 'Durchschnittliche Gewinnstufe';
$string['highscore_mode_help'] = 'Bitte wählen Sie den Bewertungs-Modus, wie Ergebnisse in die Bestenliste einfließen sollen.';
$string['highscore_teachers'] = 'Dozenten in Highscore Liste?';
$string['highscore_teachers_help'] = 'Wenn diese Option aktiviert ist, werden die Spiel-Ergebnisse der Dozenten mit in der Highscore Liste angezeigt.';

/* activity edit page: topics */
$string['topics_edit'] = 'Themen bearbeiten';
$string['topic_edit_new'] = 'Thema in &quot;<i>{$a}</i>&quot; erstellen';
$string['topic_edit_x'] = 'Thema in &quot;<i>{$a}</i>&quot; bearbeiten';
$string['topics_edit_x'] = 'Themen in &quot;<i>{$a}</i>&quot; bearbeiten';
$string['topics_edit_empty'] = 'Es wurden noch keine Themen hinzugefügt.';
$string['topics_edit_button_add'] = 'Thema hinzufügen';
$string['topics_edit_button_edit'] = 'Thema bearbeiten';
$string['topics_edit_button_categories'] = 'Kategorien bearbeiten';
$string['topics_edit_button_delete'] = 'Thema löschen';
$string['topics_edit_button_move_up'] = 'Thema nach oben verschieben';
$string['topics_edit_button_move_down'] = 'Theme nach unten verschieben';
$string['topics_edit_category_count_1'] = '1 Kategorie';
$string['topics_edit_category_count_n'] = '{$a} Kategorien';
$string['topics_edit_question_count_1'] = '1 Frage';
$string['topics_edit_question_count_n'] = '{$a} Fragen';
$string['topics_form_submit'] = 'Speichern';
$string['topics_form_cancel'] = 'Abbrechen';
$string['topics_form_name'] = 'Bezeichnung';
$string['topics_form_name_help'] = 'Unter dieser Bezeichnung wird das Thema im Spiel angezeigt.';
$string['topics_form_name_error_required'] = 'Bitte vergeben Sie eine Bezeichnung für dises Thema.';
$string['topics_form_score'] = 'Gewinnstufe';
$string['topics_form_score_help'] = 'Gewinnstufe die bei korrekter Beantwortung erreicht wird.';
$string['topics_form_score_error_required'] = 'Bitte definieren Sie eine Gewinnstufe größer 0.';
$string['topics_form_safe_spot'] = 'Sicherheitsstufe';
$string['topics_form_safe_spot_help'] = 'Gewinnstufe, auf die der Teilnehmer bei falscher Beantwortung einer Frage zurückfällt. Nur relevant, wenn die Spieloption &quot;Weiterspielen&quot; inaktiv ist.';
$string['topics_form_color'] = 'Farbe';
$string['topics_form_color_help'] = 'Statt eines Bildes können Sie eine Farbe auswählen, die auf dem Spielbrett für die Darstellung dieses Themas verwendet wird. Bitte verwenden Sie hierzu einen HTML-Farbcode in Hexadezimal-Schreibweise (z.B. #FF5533, mit oder ohne Raute).';
$string['topics_form_color_invalid'] = 'Der HTML-Farbcode muss in Hexadezimal-Schreibweise angegeben werden und aus 3 oder 6 Zeichen bestehen (z.B. #FF5533 oder #F53). Die führende Raute wird automatisch ergänzt, falls sie fehlt.';
$string['topics_form_preview'] = 'Vorschau';
$string['category_edit_new'] = 'Kategorie für Thema &quot;<i>{$a}</i>&quot; hinzufügen';
$string['category_edit_x'] = 'Kategorie für Thema &quot;<i>{$a}</i>&quot; bearbeiten';
$string['categories_edit_x'] = 'Kategorien für Thema &quot;<i>{$a}</i>&quot; bearbeiten';
$string['categories_edit_error_access_topic'] = 'Das Thema gehört nicht zu dieser Aktivität.';
$string['categories_edit_error_access_category'] = 'Die Kategorie gehört nicht zu diesem Thema.';
$string['categories_edit_empty'] = 'Es wurden noch keine Kategorien hinzugefügt.';
$string['categories_edit_button_add'] = 'Kategorie hinzufügen';
$string['categories_edit_button_edit'] = 'Kategorie bearbeiten';
$string['categories_edit_button_delete'] = 'Kategorie löschen';
$string['categories_edit_button_back_to_topics'] = 'Zur Themen-Liste';
$string['category_form_name'] = 'Name';
$string['category_form_name_help'] = 'Hier kann ein sprechender Bezeichner eingegeben werden, der in der Kategorie-Liste genutzt wird. Dies kann sinnvoll sein, wenn eine Eingrenzung nach Schlagworten erfolgt und Sie diese in der Kategorie-Übersicht des Themas bereits kenntlich machen wollen.';
$string['category_form_category'] = 'Kategorie';
$string['category_form_category_help'] = 'Bitte wählen Sie hier eine Kategorie, deren Fragen für das Thema benutzt werden sollen.';
$string['category_form_subcategories'] = 'Unterkategorien';
$string['category_form_subcategories_help'] = 'Wenn diese Option aktiviert ist, werden Unterkategorien der oben ausgewählten Kategorie mit einbezogen. Wenn sie deaktiviert ist, werden nur Fragen gewählt, die unmittelbar der oben ausgewählten Kategorie zugeordnet sind.';
$string['category_form_tags_all'] = 'Alle Schlagworte';
$string['category_form_tags_all_help'] = 'Wenn diese Option aktiviert ist, werden keine Frage aus der ausgewählten Kategorie gefiltert. Wenn sie deaktiviert ist, können Sie Schlagworte auswählen, die eine Frage besitzen muss, damit sie genutzt wird.';
$string['category_form_tags'] = 'Schlagworte';
$string['category_form_tags_help'] = 'Bitte wählen Sie hier die Schlagworte aus, die eine Frage besitzen muss, um genutzt zu werden. Hinweis: die aufgelisteten Schlagworte sind die, die für Fragen in der ausgewählten Kategorie genutzt wurden.';
$string['topic_delete_confirm_title'] = 'Thema löschen?';
$string['topic_delete_confirm_message'] = 'Möchten Sie dieses Thema wirklich löschen? diese Aktion ist kann nicht rückgängig gemacht werden.';
$string['category_delete_confirm_title'] = 'Kategorie löschen?';
$string['category_delete_confirm_message'] = 'Möchten Sie diese Kategorie wirklich löschen? diese Aktion ist kann nicht rückgängig gemacht werden.';

/* activity edit page: control */
$string['control_edit'] = 'Steuerung';
$string['control_edit_title'] = 'Steuerungs Optionen';
$string['reset_progress_heading'] = 'Fortschritt zurücksetzen';
$string['reset_progress_button'] = 'Fortschritt zurücksetzen';
$string['reset_progress_confirm_title'] = 'Bestätigung Fortschritt zurücksetzen';
$string['reset_progress_confirm_question'] = 'Möchten Sie wirklich den Fortschritt zurücksetzen (Highscores etc.)? Dieser Prozess kann nicht rückgängig gemacht werden.';

/* course reset */
$string['course_reset_include_progress'] = 'Fortschritt zurücksetzen (Highscores etc.)';
$string['course_reset_include_topics'] = 'Eingestellte Themen etc. zurücksetzen (Alles wird gelöscht!)';

/* game gui */
$string['game_screen_title'] = 'Spiele »Wer wird Millionär«';
$string['game_qtype_not_supported'] = 'Der Fragentyp »{$a}« wird nicht unterstützt.';
$string['game_loading_question'] = 'Frage wird geladen';
$string['game_loading_stats'] = 'Bestenliste wird geladen';
$string['game_loading_stats_failed'] = 'Beim Laden der Bestenliste ist ein Fehler aufgetreten.';
$string['game_btn_restart'] = 'Neues Spiel';
$string['game_btn_continue'] = 'Nächste Frage';
$string['game_btn_stats'] = 'Bestenliste';
$string['game_btn_quit'] = 'Beenden';
$string['game_btn_start'] = 'Spiel Starten';
$string['game_btn_game'] = 'Zum Spiel';
$string['game_question_headline'] = 'Frage {$a->number}: {$a->level}';
$string['game_won_headline'] = 'Gewonnen!';
$string['game_lost_headline'] = 'Leider verloren.';
$string['game_intro_message'] = 'Hier müssen wir noch ein Logo und ggf. Text platzieren.';
$string['game_help_headline'] = 'Spiel-Hinweise';
$string['game_help_message'] = '<ul><li>Mit dem Button »Neues Spiel« kann jederzeit ein neues Spiel gestartet werden.</li><li>Auch im laufenden Spiel kann jederzeit zwischen der Bestenliste und dem Spiel gewechselt werden.</li><li>Das Spiel kann mit dem Button »Beenden« (links unter der Frage) auf der aktuellen Gewinnstufe beendet werden.</li><li>Es stehen drei Joker pro Spiel zur Verfügung (rechts oben).</li></ul>';
$string['game_joker_feedback_unavailable'] = 'Leider ist für diese Frage kein Hinweis verfügbar.';
$string['game_joker_feedback_title'] = 'Hinweis:';
$string['game_joker_audience_title'] = 'Publikums-Joker:';
$string['game_stats_empty'] = 'Es gibt noch keine Einträge in der Bestenliste.';
$string['game_stats_rank'] = 'Platz';
$string['game_stats_user'] = 'Nutzer';
$string['game_stats_score'] = 'Punkte';
$string['game_stats_sessions'] = 'Versuche';


$string['topic'] = 'Thema';
$string['highscore_name'] = 'Name';
$string['highscore_sessions'] = 'Runden';
$string['highscore_score'] = 'Highscore';
$string['highscore_rank'] = 'Platz';
$string['highscore_msg_none'] = 'Es gibt noch keinen Highscore.';
$string['question_question'] = 'Frage';
$string['question_score'] = 'Gewinnstufe';
$string['question_jokers'] = 'Joker';
$string['question_joker_used'] = ' - verbraucht';
$string['question_joker_50_50'] = 'Zwei falsche Antwort-Optionen entfernen';
$string['question_joker_audience'] = 'Das Publikum befragen';
$string['question_joker_audience_title'] = 'Publikums-Joker:';
$string['question_joker_hint'] = 'Einen Hinweis erhalten';
$string['question_joker_hint_title'] = 'Hinweis:';
$string['question_joker_hint_unavailable'] = 'Leider ist kein Hinweis verfügbar.';
$string['replay'] = 'Erneut spielen';
$string['next_question'] = 'Weiter';
$string['game_insufficient_questions'] = 'Es stehen nicht genug Fragen für weitere Runden zur Verfügung.';
$string['answer_text_true'] = 'Wahr';
$string['answer_text_false'] = 'Falsch';
