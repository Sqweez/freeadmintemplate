import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {addBalance, createClient, deleteClient, editClient, getClients, getLoyalty} from "../../api/clients";

const clientModule = {
    state: {
        clients: [],
        loyalty: [],
        clientsWithoutSales: [],
    },
    getters: {
        clients: state => state.clients,
        client: state => id => state.clients.find(c => c.id === id),
        PARTNERS: state => state.clients.filter(c => c.is_partner),
        LOYALTY: s => s.loyalty,
        CLIENTS_WITHOUT_SALES: s => s.clientsWithoutSales
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
        }
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
        async [ACTIONS.GET_CLIENTS]({commit}) {
            const payload = await getClients();
            await commit(MUTATIONS.SET_CLIENTS, payload);
        },
        async [ACTIONS.ADD_BALANCE]({commit}, payload) {
            const client = await addBalance(payload);
            await commit(MUTATIONS.EDIT_CLIENT, client.data);
        },
        async [ACTIONS.GET_LOYALTY] ({commit}) {
            const { data } = await getLoyalty();
            commit(ACTIONS.GET_LOYALTY, data);
        }
    }
};

export default clientModule;
