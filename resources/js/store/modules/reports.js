import {cancelSale, getReports, getStoreReports} from '../../api/sale'
import ACTIONS from '../actions/index';

const reportsModule = {
    state: {
        storesReports: [],
        reports: [],
    },
    getters: {
        STORES_REPORTS: state => state.storesReports,
        REPORTS: state => state.reports,
    },
    mutations: {
        setStoresReport(state, payload) {
            state.storesReports = payload;
        },
        setReports(state, payload) {
            state.reports = payload;
        },
        cancelSale(state, id) {
            state.reports = state.reports.filter(s => s.id !== id);
        },
        changeSale(state, payload) {
            state.reports = state.reports.map(s => {
                if (s.id == payload.id) {
                    s = payload;
                }
                return s;
            })
        }
    },
    actions: {
        async getStoresReport({commit}, payload = 'today') {
            const {data} = await getStoreReports(payload);
            commit('setStoresReport', data.data);
        },
        async [ACTIONS.GET_REPORTS] ({commit}, payload) {
            const data = await getReports(payload);
            commit('setReports', data);
        },
        async [ACTIONS.CANCEL_SALE] ({commit}, payload) {
            const { data } = await cancelSale(payload.canceled, payload.id);
            console.log(data);
            if (!data) {
                commit('cancelSale', payload.id);
            } else {
                commit('changeSale', data.data);
            }
        }
    }
};

export default reportsModule;
