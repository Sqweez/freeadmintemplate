import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {addBalance, createClient, deleteClient, editClient, getClients, getLoyalty} from "../../api/clients";
import axiosClient from '@/utils/axiosClient';

const clientModule = {
    state: {
        clients: [],
        loyalty: [],
        clientsWithoutSales: [],
        wholesale_clients: [],
        wholesale_types: [],
        wholesale_statuses: [
            {
                id: 1,
                name: 'Сотрудничает'
            },
            {
                id: 2,
                name: 'Думает'
            },
            {
                id: 3,
                name: 'Не сотрудничает'
            },
        ]
    },
    getters: {
        clients: state => state.clients,
        client: state => id => state.clients.find(c => c.id === id),
        PARTNERS: state => state.clients.filter(c => c.is_partner),
        LOYALTY: s => s.loyalty,
        CLIENTS_WITHOUT_SALES: s => s.clientsWithoutSales,
        WHOLESALE_CLIENTS: s => s.wholesale_clients,
        WHOLESALE_CLIENT: state => id => state.wholesale_clients.find(c => c.id === id),
        WHOLESALE_TYPES: state => state.wholesale_types,
        WHOLESALE_STATUSES: state => state.wholesale_statuses,
    },
    mutations: {
        [MUTATIONS.SET_CLIENTS_WITHOUT_SALES](state, payload) {
            state.clientsWithoutSales = payload;
        },
        [MUTATIONS.CREATE_CLIENT](state, payload) {
            state.clients.push(payload);
        },
        [MUTATIONS.EDIT_CLIENT](state, payload) {
            state.clients = state.clients.map(c => {
                if (c.id === payload.id) {
                    c = payload;
                }
                return c;
            })
        },
        [MUTATIONS.DELETE_CLIENT](state, payload) {
            state.clients = state.clients.filter(c => c.id !== payload);
        },
        [MUTATIONS.SET_CLIENTS](state, payload) {
            state.clients = payload;
        },
        [ACTIONS.GET_LOYALTY](state, payload) {
            state.loyalty = payload;
        },
        [MUTATIONS.SET_WHOLESALE_CLIENTS] (state, payload) {
            state.wholesale_clients = payload;
        },
        [MUTATIONS.SET_WHOLESALE_TYPES] (state, payload) {
            state.wholesale_types = payload;
        },
        createWholesaleClient (state, payload) {
            state.wholesale_clients.push(payload);
        },
        editWholesaleClient (state, payload) {
            state.wholesale_clients = state.wholesale_clients
                .map(client => {
                    if (client.id === payload.id) {
                        client = {...payload};
                    }
                    return client;
                })
        },
    },
    actions: {
        async [ACTIONS.CREATE_CLIENT]({commit}, payload) {
            const client = await createClient(payload);
            await commit(MUTATIONS.CREATE_CLIENT, client);
        },
        async [ACTIONS.EDIT_CLIENT]({commit}, payload) {
            const client = await editClient(payload);
            await commit(MUTATIONS.EDIT_CLIENT, client);
        },
        async [ACTIONS.DELETE_CLIENT]({commit}, payload) {
            await deleteClient(payload);
            await commit(MUTATIONS.DELETE_CLIENT, payload);
        },
        async [ACTIONS.GET_CLIENTS]({commit}, filters) {
            const payload = await getClients();
            await commit(MUTATIONS.SET_CLIENTS, payload);
        },
        async [ACTIONS.GET_PARTNERS] ({ commit }) {
            const payload = await axiosClient.get(`/clients?is_partner=1`);
            await commit(MUTATIONS.SET_CLIENTS, payload);
        },
        async [ACTIONS.GET_WHOLESALE_CLIENTS] ({commit}) {
            const { data: { data } } = await axiosClient.get('/clients?wholesale=1');
            commit(MUTATIONS.SET_WHOLESALE_CLIENTS, data);
        },
        async [ACTIONS.ADD_BALANCE]({commit}, payload) {
            const client = await addBalance(payload);
            await commit(MUTATIONS.EDIT_CLIENT, client.data);
        },
        async [ACTIONS.GET_LOYALTY] ({commit}) {
            const { data } = await getLoyalty();
            commit(ACTIONS.GET_LOYALTY, data);
        },
        async [ACTIONS.GET_WHOLESALE_TYPES] ({ commit }) {
            const { data } = await axiosClient.get('/clients/wholesale-types');
            commit(MUTATIONS.SET_WHOLESALE_TYPES, data);
        },
        async [ACTIONS.CREATE_WHOLESALE_CLIENT] ({ commit }, payload) {
            const { data: { data } } = await axiosClient.post('/clients', payload);
            commit('createWholesaleClient', data);
        },
        async [ACTIONS.EDIT_WHOLESALE_CLIENT] ({ commit }, payload) {
            const { data: { data } } = await axiosClient.patch(`/clients/${payload.id}`, payload);
            commit('editWholesaleClient', data);
        },
    }
};

export default clientModule;
