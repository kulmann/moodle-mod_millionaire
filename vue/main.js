import Vue from 'vue';
import Vuikit from 'vuikit';
import '@vuikit/theme';
import 'vue-awesome/icons';
import Icon from 'vue-awesome/components/Icon';
import VueRouter from 'vue-router';
import {store} from './store';
import notFound from './components/not-found';
import gameScreen from './components/game-screen';

function init(coursemoduleid, contextid) {
    // We need to overwrite the variable for lazy loading.
    __webpack_public_path__ = M.cfg.wwwroot + '/mod/vuejsdemo/amd/build/';

    Vue.use(Vuikit);
    Vue.use(VueRouter);
    Vue.component('v-icon', Icon);

    require('./styles/theme.scss');

    store.commit('setCourseModuleID', coursemoduleid);
    store.commit('setContextID', contextid);
    store.dispatch('loadLang').then(() => {
        store.dispatch('loadComponentStrings');
    });

    // You have to use child routes if you use the same component. Otherwise the component's beforeRouteUpdate
    // will not be called.
    const routes = [
        {path: '/', redirect: {name: 'game-screen'}},
        {path: '/game/play', component: gameScreen, name: 'game-screen', meta: {title: 'game_screen_title'}},
        {path: '*', component: notFound, meta: {title: 'route_not_found'}},
    ];

    // base URL is /mod/millionaire/view.php/[course module id]/
    const currenturl = window.location.pathname;
    const base = currenturl.substr(0, currenturl.indexOf('.php')) + '.php/' + coursemoduleid + '/';

    const router = new VueRouter({
        mode: 'history',
        routes,
        base
    });

    router.beforeEach((to, from, next) => {
        // Find a translation for the title.
        if (to.hasOwnProperty('meta') && to.meta.hasOwnProperty('title')) {
            if (store.state.strings.hasOwnProperty(to.meta.title)) {
                document.title = store.state.strings[to.meta.title];
            }
        }
        next()
    });

    new Vue({
        el: '#mod-millionaire-app',
        store,
        router,
    });
}

export {init};
