import axiosClient from '@/fitness/utils/fitAxiosClient';

export default {
    state: {
        services: [],
    },
    getters: {
        services: (s) => s.services,
        service: (s) => (id) => s.services.find((s) => s.id === id),
    },
    mutations: {
        setServices(s, p) {
            s.services = p;
        },
        updateService(s, p) {
            s.services = s.services.map((s) => {
                if (s.id === p.id) {
                    s = { ...p };
                }
                return s;
            });
        },
        createService(s, p) {
            s.services.push(p);
        },
    },
    actions: {
        async getServices({ commit }) {
            const { data } = await axiosClient.get('fit/v1/services');
            commit('setServices', data.services);
        },
        async createService({ commit }, payload) {
            const { data } = await axiosClient.post('fit/v1/services', payload);
            commit('createService', data.service);
        },
        async updateService({ commit }, payload) {
            const { data } = await axiosClient.patch(
                'fit/v1/services/' + payload.id,
                payload,
            );
            commit('updateService', data.service);
        },
    },
};
