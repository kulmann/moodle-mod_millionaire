import Vue from 'vue';
import Vuex from 'vuex';
import moodleAjax from 'core/ajax';
import moodleStorage from 'core/localstorage';
import Notification from 'core/notification';
import _ from 'lodash';
import $ from 'jquery';
import {MODE_GAME_FINISHED, MODE_INTRO, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, VALID_MODES} from './constants';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        lang: null,
        courseModuleID: 0,
        contextID: 0,
        strings: {},
        levels: null,
        gameSession: null,
        question: null,
        mdl_question: null,
        mdl_answers: [],
        gameMode: MODE_INTRO,
        gameCurrentLevel: null,
    },
    //strict: process.env.NODE_ENV !== 'production',
    mutations: {
        setLang(state, lang) {
            state.lang = lang;
        },
        setCourseModuleID(state, id) {
            state.courseModuleID = id;
        },
        setContextID(state, id) {
            state.contextID = id;
        },
        setStrings(state, strings) {
            state.strings = strings;
        },
        setLevels(state, levels) {
            state.levels = levels;
        },
        setGameSession(state, gameSession) {
            state.gameSession = gameSession;
        },
        setQuestion(state, question) {
            state.question = question;
        },
        setMdlQuestion(state, mdl_question) {
            state.mdl_question = mdl_question;
        },
        setMdlAnswers(state, mdl_answers) {
            state.mdl_answers = mdl_answers;
        },
        setGameMode(state, gameMode) {
            if (_.includes(VALID_MODES, gameMode)) {
                state.gameMode = gameMode;
            } else {
                console.error("omitted invalid game mode " + gameMode + ".");
            }
        },
        markLevelAsSeen(state, levelIndex) {
            let level = _.find(state.levels, function(level) {
                return level.position === levelIndex;
            });
            level.seen = true;
        }
    },
    actions: {
        /**
         * Determines the current language.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async loadLang(context) {
            const lang = $('html').attr('lang').replace(/-/g, '_');
            context.commit('setLang', lang);
        },
        /**
         * Fetches the i18n data for the current language.
         *
         * @param context
         * @returns {Promise<void>}
         */
        async loadComponentStrings(context) {
            let lang = this.state.lang;
            const cacheKey = 'mod_millionaire/strings/' + lang;
            const cachedStrings = moodleStorage.get(cacheKey);
            if (cachedStrings) {
                context.commit('setStrings', JSON.parse(cachedStrings));
            } else {
                const request = {
                    methodname: 'core_get_component_strings',
                    args: {
                        'component': 'mod_millionaire',
                        lang,
                    },
                };
                const loadedStrings = await moodleAjax.call([request])[0];
                let strings = {};
                loadedStrings.forEach((s) => {
                    strings[s.stringid] = s.string;
                });
                context.commit('setStrings', strings);
                moodleStorage.set(cacheKey, JSON.stringify(strings));
            }
        },
        /**
         * Fetches the current game session from the server. If none exists, a new one will be created, so this
         * always returns a valid game session.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchGameSession(context) {
            const gameSession = await ajax('mod_millionaire_get_current_gamesession');
            context.commit('setGameSession', gameSession);
            return context.dispatch('fetchLevels');
        },
        /**
         * Forces that a new game session gets created. Dumps all old in progress game sessions.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async createGameSession(context) {
            const gameSession = await ajax('mod_millionaire_create_gamesession');
            context.commit('setGameSession', gameSession);
            return context.dispatch('fetchLevels').then(() => {
                context.commit('setQuestion', null);
                context.commit('setGameMode', MODE_INTRO);
            });
        },
        /**
         * Closes the current game session (i.e. sets state to FINISHED). Should only be called when the current state
         * of the game session allows it, i.e. current question is already answered / no question is currently shown
         * to be answered.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async closeGameSession(context) {
            let args = {
                'gamesessionid': this.state.gameSession.id
            };
            const gameSession = await ajax('mod_millionaire_close_gamesession', args);
            context.commit('setGameSession', gameSession);
            return context.dispatch('fetchLevels').then(() => {
                context.commit('setGameMode', MODE_GAME_FINISHED);
            });
        },
        /**
         * Loads the question for the given level index. Doesn't matter if it's already answered.
         *
         * @param context
         * @param levelIndex
         *
         * @returns {Promise<void>}
         */
        async showQuestionForLevel(context, levelIndex) {
            return context.dispatch('fetchQuestion', levelIndex).then(() => {
                context.commit('markLevelAsSeen', levelIndex);
                context.commit('setGameMode', MODE_QUESTION_SHOWN);
            });
        },
        /**
         * Submit an answer to the currently loaded question.
         *
         * @param context
         * @param payload
         *
         * @returns {Promise<void>}
         */
        async submitAnswer(context, payload) {
            context.commit('setGameMode', MODE_QUESTION_ANSWERED);
            const result = await ajax('mod_millionaire_submit_answer', payload);
            context.commit('setQuestion', result);
            return context.dispatch('fetchGameSession').then(() => {
                if (this.state.gameSession.finished) {
                    context.commit('setGameMode', MODE_GAME_FINISHED);
                }
            });
        },

        // INTERNAL FUNCTIONS. these shouldn't be called from outside the store.
        // TODO: would be nice to be able to actually prevent these actions from being called from outside the store.
        /**
         * Fetches levels, including information on whether or not a level is finished.
         * Should not be called directly. Will be called automatically in fetchGameSession.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchLevels(context) {
            let args = {};
            if (this.state.gameSession) {
                args['gamesessionid'] = this.state.gameSession.id;
            }
            const levels = await ajax('mod_millionaire_get_levels', args);
            context.commit('setLevels', levels);
        },
        /**
         * Fetches the question, moodle question and moodle answers for the given level index.
         *
         * @param context
         * @param levelIndex
         *
         * @returns {Promise<void>}
         */
        async fetchQuestion(context, levelIndex) {
            let args = {
                gamesessionid: this.state.gameSession.id,
                levelindex: levelIndex
            };
            const question = await ajax('mod_millionaire_get_question', args);
            if (question.id === 0) {
                context.commit('setQuestion', null);
                context.commit('setMdlQuestion', null);
                context.commit('setMdlAnswers', []);
            } else {
                context.commit('setQuestion', question);
                context.dispatch('fetchMdlQuestion');
                context.dispatch('fetchMdlAnswers');
            }
        },
        /**
         * Fetches the moodle question for the currently loaded question.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchMdlQuestion(context) {
            if (this.state.question) {
                let args = {
                    mdlquestionid: this.state.question.mdl_question_id
                };
                const question = await ajax('mod_millionaire_get_mdl_question', args);
                context.commit('setMdlQuestion', question);
            } else {
                context.commit('setMdlQuestion', null);
            }
        },
        /**
         * Fetches the moodle answers for the currently loaded question.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchMdlAnswers(context) {
            if (this.state.question) {
                let args = {
                    mdlquestionid: this.state.question.mdl_question_id
                };
                const answers = await ajax('mod_millionaire_get_mdl_answers', args);
                context.commit('setMdlAnswers', _.shuffle(answers));
            } else {
                context.commit('setMdlAnswers', []);
            }
        },
    }
});

/**
 * Wrapper for ajax call to Moodle.
 */
export async function ajax(method, args) {
    const request = {
        methodname: method,
        args: Object.assign({
            coursemoduleid: store.state.courseModuleID
        }, args),
    };

    try {
        return await moodleAjax.call([request])[0];
    } catch (e) {
        Notification.exception(e);
        throw e;
    }
}
