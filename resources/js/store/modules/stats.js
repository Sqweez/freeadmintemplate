import ACTIONS from "../actions";
import MUTATIONS from "../mutations";
import axios from 'axios';

const statsModule = {
    state: {
        mvp_products: [],
    },
    getters: {
        MVP_CATEGORY_PRODUCTS: state => state.mvp_products.by_category,
    },
    mutations: {
        [MUTATIONS.SET_MVP_PRODUCTS](state, payload) {
            state.mvp_products = payload;
        }
    },
    actions: {
        async [ACTIONS.GET_MVP_PRODUCTS] ({commit}, payload) {
            const { data } = await axios.get('/api/stats/mvp-products');
            commit(MUTATIONS.SET_MVP_PRODUCTS, data);
        }
    }
}

export default statsModule;
