import axiosClient from '@/fitness/utils/fitAxiosClient';

export default {
    state: {
        users: [],
        roles: [
            {
                id: 1,
                name: 'Руководитель',
            },
            {
                id: 2,
                name: 'Отдел продаж',
            },
            {
                id: 3,
                name: 'Администратор',
            },
            {
                id: 4,
                name: 'Бармен',
            },
            {
                id: 5,
                name: 'Инструктор',
            },
        ],
    },
    getters: {
        users: (s) => s.users,
        user: (s) => (id) => s.users.find((user) => user.id === id),
        roles: (s) => s.roles,
    },
    mutations: {
        setUsers(state, users) {
            state.users = users;
        },
        createUser(state, payload) {
            state.users.push(payload);
        },
        editUser(state, payload) {
            state.users = state.users.map((u) => {
                if (u.id === payload.id) {
                    u = { ...payload };
                }
                return u;
            });
        },
    },
    actions: {
        async getUsers({ commit }) {
            const { data } = await axiosClient.get('fit/v1/users');
            commit('setUsers', data.data);
        },
        async createUser({ commit }, payload) {
            const { data } = await axiosClient.post('fit/v1//users', payload);
            commit('createUser', data.data);
        },
        async editUser({ commit }, payload) {
            const { data } = await axiosClient.patch(
                `fit/v1/users/${payload.id}`,
                payload,
            );
            commit('editUser', data.data);
        },
    },
};
