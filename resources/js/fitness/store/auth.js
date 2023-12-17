import axios from 'axios';
import { getKeyByValue } from '@/utils/objects';
import axiosClient from '@/fitness/utils/fitAxiosClient';

const authModule = {
    state: {
        token: localStorage.getItem('fitness_token') || '',
        user: null,
        checked: false,
    },
    getters: {
        TOKEN: (state) => state.token,
        USER: (state) => state.user,
        LOGGED_IN: (state) => !!(state.user && state.token),
        LOGIN_CHECKED: (state) => state.checked,
        IS_GUEST: (state) => !!!state.user,
        IS_BOSS: (state) => state.user && +state.user.role_id === 1,
        IS_ADMIN: (state) => state.user && +state.user.role_id === 3,
        IS_SELLER: (state) => state.user && +state.user.role_id === 2,
        CURRENT_ROLE: (state, getters) => {
            const roles = {
                boss: getters.IS_BOSS,
                guest: getters.IS_GUEST,
                admin: getters.IS_ADMIN,
                seller: getters.IS_SELLER,
            };

            return getKeyByValue(roles, true);
        },
    },
    mutations: {
        SET_TOKEN(state, token) {
            if (!token) {
                localStorage.removeItem('fitness_token');
            } else {
                localStorage.setItem('fitness_token', token);
            }
            state.token = token;
        },
        SET_USER(state, user) {
            state.user = user;
        },
        SET_CHECKED(state, checked) {
            state.checked = checked;
        },
    },
    actions: {
        async LOGIN({ commit, dispatch }, payload) {
            this.$loading.enable();
            try {
                const response = await axiosClient.post(
                    'fit/v1/auth/login',
                    payload,
                );
                localStorage.setItem('fitness_token', response.data.token);
                await dispatch('AUTH');
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async AUTH({ commit, dispatch }) {
            const token = localStorage.getItem('fitness_token') || null;
            if (!token) {
                commit('SET_CHECKED', true);
                commit('SET_TOKEN', null);
                return;
            }
            try {
                const response = await axiosClient.get(
                    '/fit/v1/auth/me?token=' + token,
                );
                await dispatch('SET_AUTH_DATA', response);
            } catch (e) {
                commit('SET_TOKEN', null);
                this.$toast.error('Данные авторизации устарели');
            } finally {
                commit('SET_CHECKED', true);
            }
        },
        async SET_AUTH_DATA({ commit, dispatch }, response) {
            if (response.data.status === 'success') {
                const token = response.data.token;
                const user = response.data.user;
                commit('SET_TOKEN', token);
                commit('SET_USER', user);
                axios.defaults.headers.Authorization = token;
                axios.defaults.headers.user_id = user.id;
                axios.interceptors.response.use((response) => {
                    console.groupCollapsed(
                        'API response url:' + response.config.url,
                    );
                    console.log(response);
                    console.groupEnd();
                    return response;
                });
            }
        },
        async LOGOUT({ commit }) {
            commit('SET_TOKEN', null);
            commit('SET_USER', null);
            axios.defaults.headers.Authorization = null;
        },
    },
};

export default authModule;
