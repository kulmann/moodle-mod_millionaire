import Vue from 'vue';
import Vuex from 'vuex';
import moodleAjax from 'core/ajax';
import moodleStorage from 'core/localstorage';
import Notification from 'core/notification';
import _ from 'lodash';
import $ from 'jquery';
import {MODE_INTRO, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, VALID_MODES} from './constants';
import mixins from './mixins';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        initialized: false,
        lang: null,
        courseModuleID: 0,
        contextID: 0,
        strings: {},
        game: null,
        levels: null,
        levelCategories: null,
        gameSession: null,
        question: null,
        usedJokers: [],
        gameMode: MODE_INTRO,
        scores: [],
        mdl_question: null,
        mdl_answers: [],
        mdl_categories: null,
    },
    //strict: process.env.NODE_ENV !== 'production',
    mutations: {
        setInitialized(state, initialized) {
            state.initialized = initialized;
        },
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
        setGame(state, game) {
            state.game = game;
        },
        setLevels(state, levels) {
            state.levels = levels;
        },
        setLevelCategories(state, levelCategories) {
            state.levelCategories = levelCategories;
        },
        setUsedJokers(state, usedJokers) {
            state.usedJokers = usedJokers;
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
        setMdlCategories(state, mdl_categories) {
            state.mdl_categories = mdl_categories;
        },
        setGameMode(state, gameMode) {
            if (_.includes(VALID_MODES, gameMode)) {
                state.gameMode = gameMode;
            } else {
                console.error("omitted invalid game mode " + gameMode + ".");
            }
        },
        markLevelAsSeen(state, levelIndex) {
            let level = _.find(state.levels, function (level) {
                return level.position === levelIndex;
            });
            if(level) {
                level.seen = true;
            }
        },
        setScores(state, scores) {
            state.scores = scores;
        },
    },
    getters: {
        getUsedJokerByType: (state) => (type) => {
            return _.find(state.usedJokers, function (joker) {
                return joker.joker_type === type;
            });
        },
        getUsedJokerByTypeAndQuestion: (state) => (type, question) => {
            return _.find(state.usedJokers, function (joker) {
                return joker.question === question.id && joker.joker_type === type;
            });
        },
    },
    actions: {
        /**
         * Initializes everything (load language, strings, game, gameSession, gameMode, current question, etc).
         *
         * @param context
         */
        async init(context) {
            context.dispatch('loadLang').then(() => {
                Promise.all([
                    context.dispatch('loadComponentStrings'),
                    context.dispatch('fetchGame'),
                    context.dispatch('fetchGameSession').then(() => {
                        let highestSeenLevel = mixins.methods.findHighestSeenLevel(context.state.levels);
                        if (highestSeenLevel !== null && highestSeenLevel.seen) {
                            // the game was obviously already started. So fetch the last seen question.
                            context.dispatch('showQuestionForLevel', highestSeenLevel.position).then(() => {
                                if (context.state.question.finished) {
                                    context.commit('setGameMode', MODE_QUESTION_ANSWERED);
                                } else {
                                    context.commit('setGameMode', MODE_QUESTION_SHOWN);
                                }
                            });
                        }
                    }),
                ]).then(() => {
                    context.commit('setInitialized', true);
                });
            });
        },
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
         * Fetches game options and active user info.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchGame(context) {
            const game = await ajax('mod_millionaire_get_game');
            context.commit('setGame', game);
        },
        /**
         * Fetches all assigned categories for a certain level.
         *
         * @param context
         * @param payload
         *
         * @returns {Promise<void>}
         */
        async fetchLevelCategories(context, payload) {
            const categories = await ajax('mod_millionaire_get_level_categories', payload);
            context.commit('setLevelCategories', categories);
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
            return Promise.all([
                context.dispatch('fetchLevels'),
                context.dispatch('fetchUsedJokers')
            ]);
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
                context.commit('setUsedJokers', []);
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
            return context.dispatch('fetchLevels');
        },
        /**
         * Closes the current game session (i.e. sets state to DUMPED). This is needed when the user goes to the admin
         * page, to reload the levels without gamesession association.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async cancelGameSession(context) {
            if (this.state.gameSession) {
                let args = {
                    'gamesessionid': this.state.gameSession.id
                };
                const gameSession = await ajax('mod_millionaire_cancel_gamesession', args);
                context.commit('setGameSession', null);
                return context.dispatch('fetchLevels');
            }
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
            return context.dispatch('fetchGameSession');
        },
        /**
         * Submits a joker to the currently loaded question.
         *
         * @param context
         * @param payload
         *
         * @returns {Promise<void>}
         */
        async submitJoker(context, payload) {
            const result = await ajax('mod_millionaire_submit_joker', payload);
            let usedJokers = this.state.usedJokers;
            usedJokers.push(result);
            context.commit('setUsedJokers', usedJokers);
        },
        /**
         * Fetches scores according to the current scoring mode of the game.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchScores(context) {
            const scores = await ajax('mod_millionaire_get_scores_global');
            context.commit('setScores', scores);
        },
        /**
         * Changes the position (+1 or -1) of the level and does the opposite to the other level.
         *
         * @param context
         * @param payload
         *
         * @returns {Promise<void>}
         */
        async changeLevelPosition(context, payload) {
            const result = await ajax('mod_millionaire_set_level_position', payload);
            if (result.result === true) {
                context.dispatch('fetchLevels');
            }
        },
        /**
         * Deletes this level.
         *
         * @param context
         * @param payload
         *
         * @returns {Promise<void>}
         */
        async deleteLevel(context, payload) {
            const result = await ajax('mod_millionaire_delete_level', payload);
            if (result.result === true) {
                context.dispatch('fetchLevels');
            }
        },
        /**
         * Updates the data of this level, including its categories.
         *
         * @param context
         * @param payload
         *
         * @returns {Promise<void>}
         */
        async saveLevel(context, payload) {
            const result = await ajax('mod_millionaire_save_level', payload);
            context.dispatch('fetchLevels');
            return result.result;
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
         * Fetches already used jokers for the current game session.
         * Should not be called directly. Will be called automatically in fetchGameSession.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchUsedJokers(context) {
            let args = {
                gamesessionid: this.state.gameSession.id,
            };
            const usedJokers = await ajax('mod_millionaire_get_used_jokers', args);
            context.commit('setUsedJokers', usedJokers);
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
                    questionid: this.state.question.id
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
                    questionid: this.state.question.id
                };
                const answers = await ajax('mod_millionaire_get_mdl_answers', args);
                let sortedAnswers = _.sortBy(answers, function (answer) {
                    return answer.label;
                });
                context.commit('setMdlAnswers', sortedAnswers);
            } else {
                context.commit('setMdlAnswers', []);
            }
        },
        /**
         * Fetches all moodle question categories which are applicable for this game.
         *
         * @param context
         *
         * @returns {Promise<void>}
         */
        async fetchMdlCategories(context) {
            const categories = await ajax('mod_millionaire_get_mdl_categories');
            context.commit('setMdlCategories', categories);
        }
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
