import Vue from 'vue';
import Vuex from 'vuex';
import moodleAjax from 'core/ajax';
import moodleStorage from 'core/localstorage';
import Notification from 'core/notification';
import _ from 'lodash';
import $ from 'jquery';

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
        state: {
            view: 'intro',

        }
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
    },
    actions: {
        async loadLang(context) {
            const lang = $('html').attr('lang').replace(/-/g, '_');
            context.commit('setLang', lang);
        },
        async loadComponentStrings(context) {
            const lang = $('html').attr('lang').replace(/-/g, '_');
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
        async fetchGameSession(context) {
            const gameSession = await ajax('mod_millionaire_get_current_gamesession');
            context.commit('setGameSession', gameSession);
            context.dispatch('fetchLevels');
        },
        async fetchLevels(context) {
            let args = {};
            if (this.state.gameSession) {
                args['gamesessionid'] = this.state.gameSession.id;
            }
            const levels = await ajax('mod_millionaire_get_levels', args);
            context.commit('setLevels', levels);
        },
        async fetchQuestion(context) {
            let args = {
                gamesessionid: this.state.gameSession.id
            };
            const question = await ajax('mod_millionaire_get_current_question', args);
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
        startNextLevel(context) {
            context.dispatch('fetchQuestion');
        },
        async submitAnswer(context, payload) {
            const result = await ajax('mod_millionaire_submit_answer', payload);
            context.commit('setQuestion', result);
            context.dispatch('fetchGameSession');
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
