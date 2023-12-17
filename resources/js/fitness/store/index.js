import Vue from 'vue';
import Vuex, { Store } from 'vuex';
import authModule from '@/fitness/store/auth';
import createPersistedState from 'vuex-persistedstate';
import vuexPlugins from '@/store/plugins/vuexPlugins';
import frontend from '@/fitness/store/frontend';
import clients from '@/fitness/store/clients';
import users from '@/fitness/store/users';

Vue.use(Vuex);

const store = new Store({
    state: {
        async INIT({ commit, dispatch }) {},
    },
    getters: {},
    mutations: {},
    actions: {},
    modules: [authModule, frontend, clients, users],
    plugins: [
        createPersistedState({
            paths: [],
        }),
        vuexPlugins,
    ],
});

export default store;
