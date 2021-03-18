import ACTIONS from "../actions";
import MUTATIONS from "../mutations";
import axios from 'axios';

const statsModule = {
    state: {
        mvp_products: [],
        partner_stats: [],
    },
    getters: {
        MVP_CATEGORY_PRODUCTS: state => state.mvp_products.best_products,
        WORST_CATEGORY_PRODUCTS: state => state.mvp_products.worst_products,
        PARTNERS_STATS: state => state.partner_stats,
    },
    mutations: {
        [MUTATIONS.SET_MVP_PRODUCTS](state, payload) {
            state.mvp_products = payload;
        },
        setPartnersStats(state, payload) {
            state.partner_stats = payload;
        }
    },
    actions: {
        async [ACTIONS.GET_MVP_PRODUCTS] ({commit}, {store = 1, time = 'last_30_days'}) {
            const { data } = await axios.get(`/api/stats/mvp-products?store=${store}&time=${time}`);
            commit(MUTATIONS.SET_MVP_PRODUCTS, data);
        },
        async getPartnersStats({commit}) {
            const { data } = await axios.get(`/api/analytics/partners`);
            commit('setPartnersStats', data);
        }
    }
}

export default statsModule;
