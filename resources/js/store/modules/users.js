import ACTIONS from "../actions";
import MUTATIONS from '../mutations';

const userModule = {
    state: {
        users: [
            {
                id: 0,
                name: 'Андрей 1Соловьев',
                login: 'admin',
                role: 'Суперадмин',
                city: 'Все города',
            },
            {
                id: 1,
                name: 'Андрей 2Соловьев',
                login: 'admin',
                role: 'Суперадмин',
                city: 'Все города',
            },
            {
                id: 2,
                name: 'Андрей 3Соловьев',
                login: 'admin',
                role: 'Суперадмин',
                city: 'Все города',
            },
            {
                id: 3,
                name: 'Андрей 4Соловьев',
                login: 'admin',
                role: 'Суперадмин',
                city: 'Все города',
            },
            {
                id: 4,
                name: 'Андрей 5Соловьев',
                login: 'admin',
                role: 'Суперадмин',
                city: 'Все города',
            },
            {
                id: 5,
                name: 'Андрей 6Соловьев',
                login: 'admin',
                role: 'Суперадмин',
                city: 'Все города',
            }
            ]
    },
    getters: {
        users: state => state.users,
        user: state => id => state.users.find(u => u.id === id)
    },
    mutations: {
        [MUTATIONS.CREATE_USER](state, payload) {
            state.users.push(payload);
        },
        [MUTATIONS.EDIT_USER](state, payload) {
            state.users = state.users.map(u => {
                if (u.id === payload.id) {
                    u = payload;
                }
                return u;
            })
        },
        [MUTATIONS.DELETE_USER](state, payload) {
            state.users = state.users.filter(u => u.id !== payload);
        }
    },
    actions: {
        async [ACTIONS.CREATE_USER]({commit}, payload) {
            await commit(MUTATIONS.CREATE_USER, payload);
        },
        async [ACTIONS.DELETE_USER]({commit}, payload) {
            await commit(MUTATIONS.DELETE_USER, payload);
        },
        async [ACTIONS.EDIT_USER]({commit}, payload) {
            await commit(MUTATIONS.EDIT_USER, payload)
        }
    },
};

export default userModule;
