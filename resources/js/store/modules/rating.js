import MUTATIONS from "../mutations";
import ACTIONS from "../actions";
import axios from 'axios';
const ratingModule = {
    state: {
        sellers: [],
        criteria: [],
    },
    getters: {
        SELLERS: state => state.sellers,
        CRITERIA: state => state.criteria
    },
    mutations: {
        [MUTATIONS.SET_SELLERS] (state, payload) {
            state.sellers = payload;
        },
        [MUTATIONS.CREATE_SELLER] (state, payload) {
            state.sellers.push(payload);
        },
        [MUTATIONS.EDIT_SELLER] (state, payload) {
            state.sellers = state.sellers.map(s => {
                if (s.id == payload.id) {
                    s = payload;
                }
                return s;
            })
        },
        [MUTATIONS.DELETE_SELLER] (state, payload) {
            state.sellers = state.sellers.filter(s => s.id != payload);
        },
        [MUTATIONS.SET_CRITERIA] (state, payload) {
            state.criteria = payload;
        },
        [MUTATIONS.CREATE_CRITERIA] (state, payload) {
            state.criteria.push(payload);
        },
        [MUTATIONS.EDIT_CRITERIA] (state, payload) {
            state.criteria = state.criteria.map(s => {
                if (s.id == payload.id) {
                    s = payload;
                }
                return s;
            })
        },
        [MUTATIONS.DELETE_CRITERIA] (state, payload) {
            state.criteria = state.criteria.filter(s => s.id != payload);
        }

    },
    actions: {
        async [ACTIONS.GET_SELLERS] ({commit}) {
            const { data } = await axios.get('/api/rating/sellers');
            commit(MUTATIONS.SET_SELLERS, data);
        },
        async [ACTIONS.CREATE_SELLER] ({commit}, payload) {
            const { data } = await axios.post('/api/rating/sellers', payload);
            commit(MUTATIONS.CREATE_SELLER, data);
        },
        async [ACTIONS.EDIT_SELLER] ({commit}, payload) {
            const { data } = await axios.patch(`/api/rating/sellers/${payload.id}`, payload);
            commit(MUTATIONS.EDIT_SELLER, data);
        },
        async [ACTIONS.DELETE_SELLER] ({commit}, payload) {
            await axios.delete(`/api/rating/sellers/${payload}`);
            commit(MUTATIONS.DELETE_SELLER, payload);
        },
        async [ACTIONS.GET_CRITERIA] ({commit}) {
            const { data } = await axios.get('/api/rating/criteria');
            commit(MUTATIONS.SET_CRITERIA, data);
        },
        async [ACTIONS.CREATE_CRITERIA] ({commit}, payload) {
            const { data } = await axios.post('/api/rating/criteria', payload);
            commit(MUTATIONS.CREATE_CRITERIA, data);
        },
        async [ACTIONS.EDIT_CRITERIA] ({commit}, payload) {
            const { data } = await axios.patch(`/api/rating/criteria/${payload.id}`, payload);
            commit(MUTATIONS.EDIT_CRITERIA, data);
        },
        async [ACTIONS.DELETE_CRITERIA] ({commit}, payload) {
            await axios.delete(`/api/rating/criteria/${payload}`);
            commit(MUTATIONS.DELETE_CRITERIA, payload);
        }
    }}

export default ratingModule;
