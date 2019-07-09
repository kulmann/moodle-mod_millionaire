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
$string['modulename'] = 'Millionärs-Quiz';
$string['modulenameplural'] = '»Millionärs-Quiz« Instanzen';
$string['modulename_help'] = 'Ein Quiz-Spiel, das in aufsteigenden Gewinnstufen gespielt wird. Die Kursteilnehmer können mit Hilfe ihrer erreichten Gewinnstufe in eine Bestenliste sortiert werden.';
$string['pluginadministration'] = 'Millionärs-Quiz« Administration';
$string['pluginname'] = 'Millionärs-Quiz';
$string['millionaire'] = 'Millionärs-Quiz';
$string['millionaire:addinstance'] = '»Millionärs-Quiz« hinzufügen';
$string['millionaire:submit'] = '»Millionärs-Quiz« speichern';
$string['millionaire:manage'] = '»Millionärs-Quiz« verwalten';
$string['millionaire:view'] = '»Millionärs-Quiz« anzeigen';
$string['millionairename'] = 'Name';
$string['millionairename_help'] = 'Bitte vergeben Sie einen Namen für dieses »Millionärs-Quiz«.';
$string['introduction'] = 'Beschreibung';
$string['route_not_found'] = 'Die aufgerufene Seite gibt es nicht.';

/* main admin form: game options */
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

/* admin screen in vue app */
$string['admin_screen_title'] = 'Spiel-Inhalte bearbeiten';
$string['admin_not_allowed'] = 'Sie haben nicht die nötigen Zugriffsrechte, um diese Seite zu betrachten.';
$string['admin_levels_title'] = 'Levels bearbeiten';
$string['admin_levels_none'] = 'Sie haben noch keine Levels angelegt.';
$string['admin_levels_intro'] = 'Sie haben die folgenden Levels für dieses Spiel angelegt. Sie können hier die Daten und Reihenfolge der Levels verändern, oder Levels löschen. Bitte beachten Sie, dass Sie damit bei einem Spiel, das bereits Teilnehmer hat, die Rangliste wertlos machen könnten.';
$string['admin_btn_save'] = 'Speichern';
$string['admin_btn_cancel'] = 'Abbrechen';
$string['admin_btn_add'] = 'Hinzufügen';
$string['admin_btn_confirm_delete'] = 'Wirklich Löschen';
$string['admin_level_delete_confirm'] = 'Möchten Sie das Level »{$a}« wirklich löschen?';
$string['admin_level_title_add'] = 'Level {$a} erstellen';
$string['admin_level_title_edit'] = 'Level {$a} bearbeiten';
$string['admin_level_loading'] = 'Lade Level-Daten';
$string['admin_level_lbl_name'] = 'Name';
$string['admin_level_lbl_score'] = 'Gewinnstufe';
$string['admin_level_lbl_safe_spot'] = 'Sicherheitsstufe';
$string['admin_level_lbl_safe_spot_help'] = 'Wenn aktiviert, fällt der Teilnehmer bei falscher Beantwortung einer Frage auf diese Gewinnstufe zurück.';
$string['admin_level_lbl_categories'] = 'Fragen-Zuweisungen';
$string['admin_level_lbl_category'] = 'Kategorie {$a}';
$string['admin_level_lbl_category_please_select'] = 'Kategorie auswählen';
$string['admin_level_msg_saving'] = 'Das Level wird gespeichert, bitte warten';

/* game screen in vue app */
$string['game_screen_title'] = 'Spiele »Millionärs-Quiz«';
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
$string['game_over_score'] = 'Sie haben die Gewinnstufe {$a} erreicht';
$string['game_intro_message'] = 'Hier müssen wir noch ein Logo und ggf. Text platzieren.';
$string['game_help_headline'] = 'Spiel-Hinweise';
$string['game_help_message'] = '<ul><li>Mit dem Button »Neues Spiel« kann jederzeit ein neues Spiel gestartet werden.</li><li>Auch im laufenden Spiel kann jederzeit zwischen der Bestenliste und dem Spiel gewechselt werden.</li><li>Das Spiel kann mit dem Button »Beenden« (links unter der Frage) auf der aktuellen Gewinnstufe beendet werden.</li><li>Es stehen drei Joker pro Spiel zur Verfügung (rechts oben). 1: Einen Hinweis bekommen. 2: Das Publikum befragen. 3: Zwei falsche Antworten eliminieren.</li></ul>';
$string['game_joker_feedback_unavailable'] = 'Leider ist für diese Frage kein Hinweis verfügbar.';
$string['game_joker_feedback_title'] = 'Hinweis:';
$string['game_joker_audience_title'] = 'Publikums-Joker:';
$string['game_stats_empty'] = 'Es gibt noch keine Einträge in der Bestenliste.';
$string['game_stats_rank'] = 'Platz';
$string['game_stats_user'] = 'Nutzer';
$string['game_stats_score'] = 'Punkte';
$string['game_stats_sessions'] = 'Versuche';
