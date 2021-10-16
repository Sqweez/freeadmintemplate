import {acceptOrder, declineOrder, deleteOrder, getOrders, restoreOrder, setImage} from "@/api/orders";

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
        },
        SET_ORDER(state, order) {
            state.orders = state.orders.map(_order => {
                if (_order.id === order.id) {
                    _order = {...order};
                }
                return _order;
            })
        },
        RESTORE_ORDER(state, order) {
            state.orders = state.orders.map(o => {
                if (o.id === order) {
                    o.status = 0;
                }
                return o;
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
        async RESTORE_ORDER({commit}, payload) {
            try {
                commit('enableLoading');
                await restoreOrder(payload);
                commit('RESTORE_ORDER', payload);
                this.$toast.success('Заказ успешно восстановлен!')
            } catch (e) {
                this.$toast.error(e.response.data.message);
            } finally {
                commit('disableLoading');
            }
        },
        async DELETE_ORDER({commit}, payload) {
            try {
                commit('enableLoading');
                await deleteOrder(payload);
                commit('DELETE_ORDER', payload);
                this.$toast.success('Заказ успешно удален!')
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
                this.$toast.success('Заказ успешно подтвержден!')
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
                this.$toast.success('Заказ успешно отменен!')
            } catch (e) {

            } finally {
                commit('disableLoading');
            }
        },
        async SET_ORDER_IMAGE({commit}, payload) {
            try {
                commit('enableLoading');
                const response = await setImage(payload.order_id, payload.image);
                commit('SET_ORDER', response.data.data);
            } catch (e) {

            } finally {
                commit('disableLoading');
            }
        }
    }
}

export default orderModule;
