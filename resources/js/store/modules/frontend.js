export default {
    state: {
        loadingSpinner: false,
    },
    getters: {
        isLoading: s => s.loadingSpinner,
    },
    mutations: {
        enableLoading(state) {
            state.loadingSpinner = true;
        },
        disableLoading(state) {
            state.loadingSpinner = false;
        },
        toggleLoading(state) {
            state.loadingSpinner = !state.loadingSpinner;
        },
        showProductModal(state) {
            state.productModal = true;
        },
        closeProductModal(state) {
            state.productModal = false;
        }
    },
    actions: {}
}
