import ACTIONS from '../actions';
import MUTATIONS from '../mutations';
import {
    createAttribute,
    deleteAttribute,
    editAttribute,
    getAttributes,
} from '../../api/attributes';
import axiosClient from '@/utils/axiosClient';

const attributeModule = {
    state: {
        attributes: [],
        currencies: [],
    },
    getters: {
        attributes: (state) => state.attributes,
        attribute: (state) => (id) => state.attributes.find((a) => a.id === id),
        currencies: (state) => state.currencies,
    },
    mutations: {
        [MUTATIONS.SET_ATTRIBUTES](state, payload) {
            state.attributes = payload;
        },
        [MUTATIONS.CREATE_ATTRIBUTE](state, payload) {
            state.attributes.push(payload);
        },
        [MUTATIONS.EDIT_ATTRIBUTE](state, payload) {
            state.attributes = state.attributes.map((a) => {
                if (a.id === payload.id) {
                    a = payload;
                }
                return a;
            });
        },
        [MUTATIONS.DELETE_ATTRIBUTE](state, payload) {
            state.attributes = state.attributes.filter((a) => a.id !== payload);
        },
        [MUTATIONS.SET_CURRENCIES](state, payload) {
            state.currencies = payload;
        },
    },
    actions: {
        async [ACTIONS.GET_ATTRIBUTES]({ commit }) {
            const attributes = await getAttributes();
            commit(MUTATIONS.SET_ATTRIBUTES, attributes);
        },
        async [ACTIONS.CREATE_ATTRIBUTE]({ commit }, payload) {
            const attribute = await createAttribute(payload);
            commit(MUTATIONS.CREATE_ATTRIBUTE, attribute);
        },
        async [ACTIONS.EDIT_ATTRIBUTE]({ commit }, payload) {
            const attribute = await editAttribute(payload);
            commit(MUTATIONS.EDIT_ATTRIBUTE, attribute);
        },
        async [ACTIONS.DELETE_ATTRIBUTE]({ commit }, payload) {
            await deleteAttribute(payload);
            commit(MUTATIONS.DELETE_ATTRIBUTE, payload);
        },
        async [ACTIONS.GET_CURRENCIES]({ commit }) {
            const { data } = await axiosClient.get('attributes/currency');
            console.log(data);
            commit(MUTATIONS.SET_CURRENCIES, data.currencies);
        },
    },
};

export default attributeModule;
