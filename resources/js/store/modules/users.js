import ACTIONS from "../actions";
import MUTATIONS from '../mutations';
import {createUser, deleteUser, editUser, getUserRoles, getUsers} from "@/api/users";

const userModule = {
    state: {
        users: [],
        user_roles: []
    },
    getters: {
        users: state => state.users,
        user: state => id => state.users.find(u => u.id === id),
        user_roles: state => state.user_roles,
        USERS_SUPPLIERS: state => state.users.filter(user => user.role_id === 5),
        USERS_SELLERS: s => s.users.filter(user => user.role_id === 2 || user.role_id === 9)
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
        },
        [MUTATIONS.SET_USERS] (state, payload) {
            state.users = payload;
        },
        SET_USER_ROLES (state, payload) {
            state.user_roles = payload;
        }
    },
    actions: {
        async [ACTIONS.CREATE_USER]({commit}, payload) {
            const response = await createUser(payload);
            await commit(MUTATIONS.CREATE_USER, response.data.data);
        },
        async [ACTIONS.DELETE_USER]({commit}, payload) {
            await deleteUser(payload);
            await commit(MUTATIONS.DELETE_USER, payload);
        },
        async [ACTIONS.EDIT_USER]({commit}, payload) {
            const response = await editUser(payload);
            await commit(MUTATIONS.EDIT_USER, response.data.data);
        },
        async [ACTIONS.GET_USERS] ({commit}) {
            const response = await getUsers();
            commit(MUTATIONS.SET_USERS, response.data.data)
        },
        async [ACTIONS.GET_USER_ROLES] ({commit}) {
            const response = await getUserRoles();
            commit(MUTATIONS.SET_USER_ROLES, response.data);
        },
    },
};

export default userModule;
