import {cancelPreOrder, createPreOrder, getPreOrders, getPreOrdersReports} from "@/api/preorder";

const preordersModule = {
    state: {
        preorders: [],
    },
    getters: {
        PREORDERS: s => s.preorders,
        ACTIVE_PREORDERS: s => s.preorders.filter(p => p.status === 0)
    },
    mutations: {
        SET_PREORDERS(state, payload) {
            state.preorders = payload;
        },
        CREATE_PREORDER(state, payload) {
            state.preorders = [...state.preorders, payload];
        },
        CANCEL_PREORDER(state, payload) {
            state.preorders = state.preorders.map(p => {
                if (p.id === payload) {
                    p.status = -1;
                    p.status_text = 'Отменен';
                }
                return p;
            })
        }
    },
    actions: {
        async GET_PREORDERS({commit, dispatch, getters}) {
            try {
                let user_id = null;
                if (getters.USER.role_id === 2) {
                    user_id = getters.USER.id;
                }
                commit('enableLoading');
                const { data } = await getPreOrders(user_id);
                commit('SET_PREORDERS', data.data);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async GET_PREORDERS_REPORT({commit, dispatch, getters}, payload) {
            try {
                commit('enableLoading');
                let user_id = null;
                if (getters.USER.role_id === 2) {
                    user_id = getters.USER.id;
                }
                const { data } = await getPreOrdersReports(payload, user_id);
                commit('SET_PREORDERS', data.data);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async CREATE_PREORDER({commit, dispatch}, payload) {
            try {
                commit('enableLoading');
                const { data } = await createPreOrder(payload);
                commit('CREATE_PREORDER', data);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async CANCEL_PREORDER({commit}, payload) {
            try {
                commit('enableLoading');
                await cancelPreOrder(payload);
                commit('CANCEL_PREORDER', payload);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        }
    }
}

export default preordersModule;
