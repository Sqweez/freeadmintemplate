import MUTATIONS from '@/store/mutations';
import ACTIONS from '@/store/actions';
import {getArrivalAnalytics, getSaleAnalytics, getSaleAnalyticsSellers} from "@/api/analytics";

const analyticsModule = {
    state: {
        sale_analytics: [],
        arrival_analytics: [],
        sale_analytics_sellers: [],
    },
    getters: {
        SALE_ANALYTICS: s => s.sale_analytics.map(i => i.amount),
        SALE_ANALYTIC_LABELS: s => s.sale_analytics.map(i => i.date_name),
        ARRIVAL_ANALYTICS: s => s.arrival_analytics.map(i => i.amount),
        ARRIVAL_ANALYTICS_LABELS: s => s.arrival_analytics.map(i => i.date_name),
        SALE_ANALYTICS_SELLERS: s => s.sale_analytics_sellers,
    },
    mutations: {
        [MUTATIONS.SET_SALE_ANALYTICS](state, payload) {
            state.sale_analytics = payload;
        },
        [MUTATIONS.SET_ARRIVAL_ANALYTICS](state, payload) {
            state.arrival_analytics = payload;
        },
        [MUTATIONS.SET_SALE_ANALYTICS_SELLERS](state, payload) {
            state.sale_analytics_sellers = payload;
        }
    },
    actions: {
        async [ACTIONS.GET_SALE_ANALYTICS]({commit}, payload) {
            try {
                this.$loading.enable();
                const { data } = await getSaleAnalytics(payload);
                commit(MUTATIONS.SET_SALE_ANALYTICS, data);
            } catch (e) {
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.GET_SALE_ANALYTICS_SELLERS]({commit}, payload) {
            try {
                this.$loading.enable();
                const { data } = await getSaleAnalyticsSellers(payload);
                commit(MUTATIONS.SET_SALE_ANALYTICS_SELLERS, data);
            } catch (e) {
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.GET_ARRIVAL_ANALYTICS]({commit}, payload) {
            try {
                this.$loading.enable();
                const { data } = await getArrivalAnalytics(payload);
                commit(MUTATIONS.SET_ARRIVAL_ANALYTICS, data);
            } catch (e) {
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        }
    }
};

export default analyticsModule;
