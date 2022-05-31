import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {createCategory, deleteCategory, editCategory, getCategories} from "../../api/category";

const categoryModule = {
    state: {
        categories: []
    },
    getters: {
        categories: state => state.categories,
    },
    mutations: {
        async [MUTATIONS.CREATE_CATEGORY] (state, payload) {
            state.categories.push(payload)
        },
        async [MUTATIONS.EDIT_CATEGORY] (state, payload) {
            state.categories = state.categories.map(c => {
                if (c.id === payload.id) {
                    c = payload;
                }
                return c;
            })
        },
        async [MUTATIONS.DELETE_CATEGORY] (state, payload) {
            state.categories = state.categories.filter(c => c.id !== payload);
        },
        async [MUTATIONS.CREATE_SUBCATEGORY] (state, payload) {},
        async setCategories(state, payload) {
            state.categories = payload;
        }
    },
    actions: {
        async [ACTIONS.GET_CATEGORIES] ({commit}) {
            const categories = await getCategories();
            commit('setCategories', categories);
        },
        async [ACTIONS.CREATE_CATEGORY] ({commit}, payload) {
            const response = await createCategory(payload);
            commit(MUTATIONS.CREATE_CATEGORY, response);
        },
        async [ACTIONS.EDIT_CATEGORY] ({commit}, payload) {
            const category = await editCategory(payload);
            commit(MUTATIONS.EDIT_CATEGORY, category);
        },
        async [ACTIONS.DELETE_CATEGORY] ({commit}, payload) {
            await deleteCategory(payload);
            commit(MUTATIONS.DELETE_CATEGORY, payload);
        },
        async [ACTIONS.CREATE_SUBCATEGORY] ({commit}, payload) {},
    }
};

export default categoryModule;
