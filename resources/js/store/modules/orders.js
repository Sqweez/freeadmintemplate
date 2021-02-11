import {acceptOrder, declineOrder, deleteOrder, getOrders} from "@/api/orders";
import showToast from "@/utils/toast";

const orderModule = {
    state: {
        orders: [],
    },
    getters: {
        ORDERS: s => s.orders,
    },
    mutations: {
        SET_ORDERS(state, payload) {
            state.orders = payload;
        },
        DELETE_ORDER(state, payload) {
            state.orders = state.orders.filter(s => s.id !== payload);
        },
        ACCEPT_ORDER(state, payload) {
            state.orders = state.orders.map(order => {
                if (order.id === payload) {
                    order.status = 1;
                    order.status_text = 'Выполнен'
                }
                return order;
            })
        },
        DECLINE_ORDER(state, payload) {
            state.orders = state.orders.map(order => {
                if (order.id === payload) {
                    order.status = -1;
                    order.status_text = 'Отменен'
                }
                return order;
            })
        }
    },
    actions: {
        async GET_ORDERS({commit}) {
            try {
                commit('enableLoading');
                const response = await getOrders();
                commit('SET_ORDERS', response.data.data);
            } catch (e) {

            } finally {
                commit('disableLoading');
            }

        },
        async DELETE_ORDER({commit}, payload) {
            try {
                commit('enableLoading');
                await deleteOrder(payload);
                commit('DELETE_ORDER', payload);
                showToast('Заказ успешно удален!')
            } catch (e) {

            } finally {
                commit('disableLoading');
            }
        },
        async ACCEPT_ORDER({commit}, payload) {
            try {
                commit('enableLoading');
                await acceptOrder(payload);
                commit('ACCEPT_ORDER', payload);
                showToast('Заказ успешно подтвержден!')
            } catch (e) {

            } finally {
                commit('disableLoading');
            }
        },
        async DECLINE_ORDER({commit}, payload) {
            try {
                commit('enableLoading');
                await declineOrder(payload);
                commit('DECLINE_ORDER', payload);
                showToast('Заказ успешно отменен!')
            } catch (e) {

            } finally {
                commit('disableLoading');
            }
        }
    }
}

export default orderModule;
