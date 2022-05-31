import {auth, login} from '@/api/auth';
import axios from 'axios';
import {getKeyByValue} from '@/utils/objects';
import ACTIONS from "@/store/actions";

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
        IS_SUPPLIER: state => state.user && +state.user.role_id === 5,
        IS_PARTNER_SELLER: state => state.user && +state.user.role_id === 6,
        IS_STOREKEEPER: state => state.user && +state.user.role_id === 7,
        IS_BOSS: state => state.user && +state.user.role_id === 8,
        IS_SENIOR_SELLER: state => state.user && +state.user.role_id === 9,
        IS_GUEST: state => !!!state.user,
        IS_MARKETOLOG: state => state.user && +state.user.role_id === 10,
        IS_FRANCHISE: (state) => (state.user && +state.user.role_id === 11),
        CAN_SALE: (state, getters) => (getters.IS_ADMIN || getters.IS_SELLER || getters.IS_BOSS || getters.IS_SENIOR_SELLER || getters.IS_MARKETOLOG || getters.IS_FRANCHISE),
        IS_MALOY: (state, getters) => !!(getters.IS_MODERATOR && state.user.login === 'maloy'),
        CURRENT_ROLE: (state, getters) => {
            const roles = {
                observer: getters.IS_OBSERVER,
                moderator: getters.IS_MODERATOR,
                admin: getters.IS_ADMIN,
                guest: getters.IS_GUEST,
                seller: getters.IS_SELLER,
                supplier: getters.IS_SUPPLIER,
                partner_sellers: getters.IS_PARTNER_SELLER,
                storekeeper: getters.IS_STOREKEEPER,
                boss: getters.IS_BOSS,
                seniorSeller: getters.IS_SENIOR_SELLER,
                marketolog: getters.IS_MARKETOLOG,
                franchise: getters.IS_FRANCHISE
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
            this.$loading.enable();
            try {
                const response = await login(payload);
                localStorage.setItem('token', response.data.token);
                window.location = '/';
            }
            catch (e) {
                this.$toast.error(e.response.data.message);
            } finally {
                this.$loading.disable();
            }
        },
        async AUTH({commit, dispatch}) {
            const token = localStorage.getItem('token') || null;
            if (!token) {
                commit('SET_CHECKED', true);
                commit('SET_TOKEN', null);
                return;
            }
            try {
                const response = await auth({token});
                await dispatch('SET_AUTH_DATA', response);
            } catch (e) {
                commit('SET_TOKEN', null);
                this.$toast.error('Данные авторизации устарели');
            } finally {
                commit('SET_CHECKED', true);
            }
        },
        async SET_AUTH_DATA({commit, dispatch}, response) {
            if (response.data.status === 'success') {
                const token = response.data.token;
                const user = response.data.user;
                commit('SET_TOKEN', token);
                commit('SET_USER', user);
                axios.defaults.headers.Authorization = token;
                axios.defaults.headers.store_id = user.store_id;
                axios.defaults.headers.user_id = user.id;
                axios.interceptors.response.use((response) => {
                    console.groupCollapsed('API response url:' + response.config.url);
                    console.log(response);
                    console.groupEnd();
                    return response
                })
                dispatch(ACTIONS.OPEN_SHIFT, user);
            }
        },
        async LOGOUT({commit}) {
            commit('SET_TOKEN', null);
            commit('SET_USER', null);
            axios.defaults.headers.Authorization = null;
        }
    }
};

export default authModule;
