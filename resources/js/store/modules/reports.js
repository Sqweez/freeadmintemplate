import {getStoreReports} from '../../api/sale'

const reportsModule = {
    state: {
        storesReports: [],
    },
    getters: {
        STORES_REPORTS: state => state.storesReports
    },
    mutations: {
        setStoresReport(state, payload) {
            state.storesReports = payload;
        }
    },
    actions: {
        async getStoresReport({commit}, payload = 'today') {
            const {data} = await getStoreReports(payload);
            commit('setStoresReport', data.data);
        }
    }
};

export default reportsModule;
