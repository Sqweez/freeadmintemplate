import fitAxiosClient from '@/fitness/utils/fitAxiosClient';

export default {
    state: {
        dashboardStats: null,
    },
    getters: {
        dashboardStats: (s) => s.dashboardStats,
    },
    mutations: {
        setDashboardStats(state, payload) {
            state.dashboardStats = payload;
        },
    },
    actions: {
        async getDashboardStats({ commit }) {
            const { data } = await fitAxiosClient.get('fit/v1/stats/dashboard');
            commit('setDashboardStats', data);
        },
    },
};
