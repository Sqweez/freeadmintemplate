import fitAxiosClient from '@/fitness/utils/fitAxiosClient';

export default {
    state: {
        products: [],
    },
    getters: {
        products: (state) => state.products,
    },
    mutations: {
        setProducts(state, payload) {
            state.products = payload;
        },
    },
    actions: {
        async getProducts({ commit }) {
            const { data } = await fitAxiosClient.get('fit/v1/products');
            commit('setProducts', data.data);
        },
        async saleProduct({ commit, dispatch }, payload) {
            await fitAxiosClient.post('fit/v1/products/sale', payload);
            await dispatch('getProducts');
        },
    },
};
