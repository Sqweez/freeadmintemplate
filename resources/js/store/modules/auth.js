import {auth, login} from "../../api/auth";
import axios from 'axios';
import showToast from "../../utils/toast";
import _ from 'lodash';
import {getKeyByValue} from "../../utils/objects";
import {TOAST_TYPE} from "../../config/consts";

const authModule = {
    state: {
        token: localStorage.getItem('token') || '',
        user: null,
        checked: false
    },
    getters: {
        TOKEN: state => state.token,
        USER: state => state.user,
        LOGGED_IN: state => !!(state.user && state.token),
        LOGIN_CHECKED: state => state.checked,
        IS_ADMIN: state => state.user && +state.user.role_id === 1,
        IS_OBSERVER: state => state.user && +state.user.role_id === 3,
        IS_SELLER: state => state.user && +state.user.role_id === 2,
        IS_MODERATOR: state => state.user && +state.user.role_id === 4,
        IS_GUEST: state => !!!state.user,
        CAN_SALE: (state, getters) => (getters.IS_ADMIN || getters.IS_SELLER),
        IS_MALOY: (state, getters) => !!(getters.IS_MODERATOR && state.user.login === 'maloy'),
        CURRENT_ROLE: (state, getters) => {
            const roles = {
                observer: getters.IS_OBSERVER,
                moderator: getters.IS_MODERATOR,
                admin: getters.IS_ADMIN,
                guest: getters.IS_GUEST,
                seller: getters.IS_SELLER
            };

            return getKeyByValue(roles, true);
        },
    },
    mutations: {
        SET_TOKEN(state, token) {
            if (!token) {
                localStorage.removeItem('token');
            } else {
                localStorage.setItem('token', token);
            }
            state.token = token
        },
        SET_USER(state, user) {
            state.user = user;
        },
        SET_CHECKED(state, checked) {
            state.checked = checked;
        }
    },
    actions: {
        async LOGIN ({commit, dispatch}, payload) {
            commit('enableLoading');
            try {
                const response = await login(payload);
                localStorage.setItem('token', response.data.user.token);
                window.location = "/";
            }
            catch (e) {
                showToast(e.response.data.message, TOAST_TYPE.ERROR);
            } finally {
                commit('disableLoading');
            }
        },
        async AUTH({commit, dispatch}) {
            const token = localStorage.getItem('token') || null;
            if (!token) {
                commit("SET_CHECKED", true);
                commit("SET_TOKEN", null);
                return;
            }
            try {
                const response = await auth({token});
                await dispatch('SET_AUTH_DATA', response);
            } catch (e) {
                commit("SET_TOKEN", null);
                showToast('Данные авторизации устарели', 'warning');
            } finally {
                commit("SET_CHECKED", true);
            }
        },
        async SET_AUTH_DATA({commit}, response) {
            if (response.data.status === 'success') {
                const token = response.data.user.token;
                const user = response.data.user;
                commit('SET_TOKEN', token);
                commit('SET_USER', user);
                axios.defaults.headers.authorization = token;
                axios.defaults.headers.store_id = user.store_id;
            }
        },
        async LOGOUT({commit}) {
            commit('SET_TOKEN', null);
            commit("SET_USER", null);
            axios.defaults.headers.authorization = null;
        }
    }
};

export default authModule;
