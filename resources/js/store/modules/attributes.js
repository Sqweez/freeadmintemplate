import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {createAttribute, deleteAttribute, editAttribute, getAttributes} from "../../api/attributes";

const attributeModule = {
    state: {
        attributes: [],
    },
    getters: {
        attributes: state => state.attributes,
        attribute: state => id => state.attributes.find(a => a.id === id)
    },
    mutations: {
        [MUTATIONS.SET_ATTRIBUTES] (state, payload) {
            state.attributes = payload;
        },
        [MUTATIONS.CREATE_ATTRIBUTE] (state, payload) {
            state.attributes.push(payload);
        },
        [MUTATIONS.EDIT_ATTRIBUTE] (state, payload) {
            state.attributes = state.attributes.map(a => {
                if (a.id === payload.id) {
                    a = payload;
                }
                return a;
            })
        },
        [MUTATIONS.DELETE_ATTRIBUTE] (state, payload) {
            state.attributes = state.attributes.filter(a => a.id !== payload);
        },
    },
    actions: {
        async [ACTIONS.GET_ATTRIBUTES] ({commit}) {
          const attributes = await getAttributes();
          commit(MUTATIONS.SET_ATTRIBUTES, attributes);
        },
        async [ACTIONS.CREATE_ATTRIBUTE] ({commit}, payload) {
            const attribute = await createAttribute(payload);
            commit(MUTATIONS.CREATE_ATTRIBUTE, attribute);
        },
        async [ACTIONS.EDIT_ATTRIBUTE] ({commit}, payload) {
            const attribute = await editAttribute(payload);
            commit(MUTATIONS.EDIT_ATTRIBUTE, attribute);
        },
        async [ACTIONS.DELETE_ATTRIBUTE] ({commit}, payload) {
            await deleteAttribute(payload);
            commit(MUTATIONS.DELETE_ATTRIBUTE, payload);
        },
    }
};

export default attributeModule;
