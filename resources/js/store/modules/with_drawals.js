export default {
    state: {
        withdrawals: [],
    },
    getters: {
        withdrawals: s => s.withdrawals,
    },
    mutations: {
        SET_WITHDRAWALS (state, payload) {
            state.withdrawals = payload;
        },
        CREATE_WITHDRAWAL (state, payload) {
            state.withdrawals.push(payload);
        },
        DELETE_WITHDRAWAL (state, id) {
            state.withdrawals = state.withdrawals.filter(s => s.id !== id);
        }
    },
}
