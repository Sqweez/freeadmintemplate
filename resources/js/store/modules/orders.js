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
        }
    },
    actions: {
        GET_ORDERS(state) {
            s
        }
    }
}

export default orderModule;
