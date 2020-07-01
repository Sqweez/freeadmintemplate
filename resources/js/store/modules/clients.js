import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {addBalance, createClient, deleteClient, editClient, getClients} from "../../api/clients";

const clientModule = {
    state: {
        clients: [],
    },
    getters: {
        clients: state => state.clients,
        client: state => id => state.clients.find(c => c.id === id)
    },
    mutations: {
        [MUTATIONS.CREATE_CLIENT] (state, payload) {
            state.clients.push(payload);
        },
        [MUTATIONS.EDIT_CLIENT] (state, payload) {
            state.clients = state.clients.map(c => {
                if (c.id === payload.id) {
                    c = payload;
                }
                return c;
            })
        },
        [MUTATIONS.DELETE_CLIENT] (state, payload) {
            state.clients = state.clients.filter(c => c.id !== payload);
        },
        [MUTATIONS.SET_CLIENTS] (state, payload) {
            state.clients = payload;
        }
    },
    actions: {
        async [ACTIONS.CREATE_CLIENT] ({commit}, payload) {
            const client = await createClient(payload);
            await commit(MUTATIONS.CREATE_CLIENT, client);
        },
        async [ACTIONS.EDIT_CLIENT] ({commit}, payload) {
            const client = await editClient(payload);
            await commit(MUTATIONS.EDIT_CLIENT, client);
        },
        async [ACTIONS.DELETE_CLIENT] ({commit}, payload) {
            await deleteClient(payload);
            await commit(MUTATIONS.DELETE_CLIENT, payload);
        },
        async [ACTIONS.GET_CLIENTS] ({commit}) {
            const payload = await getClients();
            await commit(MUTATIONS.SET_CLIENTS, payload);
        },
        async [ACTIONS.ADD_BALANCE] ({commit}, payload) {
            const client = await addBalance(payload);
            await commit(MUTATIONS.EDIT_CLIENT, client.data);
        }
    }
};

export default clientModule;
