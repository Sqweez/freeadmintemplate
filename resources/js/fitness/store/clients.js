import axiosClient from '@/fitness/utils/fitAxiosClient';

export default {
    state: {
        clients: [],
        client: null,
        searchedClient: null,
    },
    getters: {
        searchedClient: (s) => s.searchedClient,
        clients: (s) => s.clients,
        client: (s) => (id) => s.clients.find((c) => c.id === id),
        nearlyClients: (s) => s.nearlyClients,
        outdatedClients: (s) => s.outdatedClients,
        todayClients: (s) => s.todayClients,
        singleClient: (s) => s.client,
    },
    mutations: {
        setClients(state, p) {
            state.clients = p;
        },
        createClient(state, p) {
            state.clients.push(p);
        },
        editClient(state, p) {
            state.clients = state.clients.map((c) => {
                if (c.id == p.id) {
                    c = { ...p };
                }
                return c;
            });
        },
        deleteClient(state, id) {
            state.clients = state.clients.filter((c) => c.id !== id);
        },
        setNearlyClients(state, p) {
            state.nearlyClients = p;
        },
        setOutdatedClients(state, p) {
            state.outdatedClients = p;
        },
        setTodayClients(s, p) {
            s.todayClients = p;
        },
        setClient(state, client) {
            state.client = client;
        },
        updateSale(state, payload) {
            console.log(state.client);
            state.client = {
                ...state.client,
                sales: state.client.sales.map((s) => {
                    if (payload.id === s.id) {
                        s = payload;
                    }
                    return s;
                }),
            };
        },
        cancelSubscription(state, id) {
            state.client = {
                ...state.client,
                sales: state.client.sales.filter((s) => s.id !== id),
            };
        },
    },
    actions: {
        async getClients({ commit }) {
            const r = await axiosClient.get('fit/v1/clients');
            commit('setClients', r.data.data);
        },
        async createClient({ commit }, payload) {
            try {
                this.$loading.enable();
                const {
                    data: { data },
                } = await axiosClient.post('fit/v1/clients', payload);
                commit('createClient', data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async editClient({ commit }, { payload, id }) {
            try {
                this.$loading.enable();
                const {
                    data: { data },
                } = await axiosClient.post(
                    `fit/v1/clients/${id}?_method=PATCH`,
                    payload,
                );
                commit('editClient', data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async deleteClient({ commit }, id) {
            await axiosClient.delete(`fit/v1/clients/${id}`);
            commit('deleteClient', id);
        },
        async getClient({ commit }, id) {
            const {
                data: { data },
            } = await axiosClient.get(`/clients/${id}`);
            commit('setClient', data);
        },
        async activateSale({ commit }, payload) {
            const {
                data: { data },
            } = await axiosClient.post(`/sales/${payload.id}/activate`);
            commit('updateSale', data);
        },
        async cancelSubscription({ commit }, id) {
            await axiosClient.get(`/sales/${id}/cancel`);
            commit('cancelSubscription', id);
        },
        async createVisit({ commit }, payload) {
            const {
                data: { data },
            } = await axiosClient.post(
                `/sales/${payload.sale_id}/visit`,
                payload,
            );
            commit('updateSale', data);
        },
        async updateSale({ commit }, payload) {
            const {
                data: { data },
            } = await axiosClient.patch(`/sales/${payload.id}/update`, payload);
            commit('updateSale', data);
        },
    },
};
