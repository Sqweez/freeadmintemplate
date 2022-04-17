import {
    cancelSale,
    createBrandsMotivation, editSaleList,
    getBrandsMotivation,
    getPlanReports,
    getReports,
    getStoreReports,
    updateSale
} from '@/api/sale'
import ACTIONS from '../actions/index';
import moment from 'moment';

const reportsModule = {
    state: {
        storesReports: [],
        reports: [],
        planReports: [],
        brandsMotivation: [],
    },
    getters: {
        STORES_REPORTS: state => state.storesReports,
        REPORTS: state => state.reports.map(report => {
            report.search = report.products.map(product => {
                return `${product.product_name } ${product.manufacturer.manufacturer_name} ${product.attributes.join(' ')}`;
            }).join(' ');
            return report;
        }),
        PLAN_REPORTS: state => state.planReports,
        BRANDS_MOTIVATION: s => s.brandsMotivation
    },
    mutations: {
        setStoresReport(state, payload) {
            state.storesReports = payload;
        },
        setReports(state, payload) {
            state.reports = payload.map(p => {
                p._products = [...p.products];
                return p;
            });
        },
        cancelSale(state, id) {
            state.reports = state.reports.filter(s => s.id !== id);
        },
        changeSale(state, payload) {
            state.reports = state.reports.map(r => {
                if (r.id == payload.id) {
                    r = payload;
                    r._products = [...payload.products];
                }
                return r;
            })
        },
        setPlanReports(state, payload) {
            state.planReports = payload;
        },
        setBrandsMotivation(state, payload) {
            state.brandsMotivation = payload;
        }
    },
    actions: {
        async getStoresReport({commit, getters}, payload = moment().format('YYYY-MM-DD')) {
            const _payload = {
                date_filter: payload,
                role: getters.CURRENT_ROLE,
                store_id: getters.USER.store_id
            };
            const {data} = await getStoreReports(_payload);
            commit('setStoresReport', data);
        },
        async [ACTIONS.GET_REPORTS] ({commit, getters}, payload) {
            if (!(getters.IS_ADMIN || getters.IS_BOSS || getters.IS_SENIOR_SELLER || getters.IS_MARKETOLOG || getters.IS_FRANCHISE)) {
                payload.user_id = getters.USER.id;
            }
            if (getters.IS_SUPPLIER) {
                payload.is_supplier = 1;
            }

            if (getters.IS_SENIOR_SELLER || getters.IS_FRANCHISE) {
                payload.store_id = getters.USER.store_id;
            }

            const data = await getReports(payload);
            commit('setReports', data);
        },
        async [ACTIONS.CANCEL_SALE] ({commit}, payload) {
            const { data } = await cancelSale(payload.canceled, payload.id);
            if (!data) {
                commit('cancelSale', payload.id);
            } else {
                commit('changeSale', data.data);
            }
        },
        async getPlanReports({commit}) {
            const { data } = await getPlanReports();
            commit('setPlanReports', data);
        },
        async updateSale({commit}, payload) {
            const data = await updateSale(payload);
            commit('changeSale', data);
        },
        async getBrandsMotivation({commit}) {
            const data = await getBrandsMotivation();
            commit('setBrandsMotivation', data);
        },
        async [ACTIONS.EDIT_SALE_LIST]({commit}, payload) {
            try {
                this.$loading.enable();
                const report = await editSaleList(payload);
                commit('changeSale', report.data);
            } catch (e) {
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        }
    }
};

export default reportsModule;
