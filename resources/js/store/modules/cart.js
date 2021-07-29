const cartModule = {
    state: {
        cart: [],
        cart_params: {
            isRed: false,
            isFree: false,
            payment_type: 0,
            partner_id: null,
            discountPercent: 0,
            promocode: "",
            promocodeSet: false,
            store_id: null,
        },
        cart_client: null,
    },
    getters: {
        CART: s => s.cart,
        CART_PARAMS: s => s.cart_params,
        CART_CLIENT: s => s.cart_client,
    },
    mutations: {
        ADD_TO_CART(state, payload) {
            state.cart.push({...payload, count: 1, product_price: payload.product_price});
        },
        DELETE_FROM_CART(state, index) {
            state.cart.splice(index, 1);
            if (state.cart.length === 0) {
                state.cart_client = null;
                state.cart_params = {
                    isRed: false,
                    isFree: false,
                    payment_type: 0,
                    partner_id: null,
                    discountPercent: 0,
                    promocode: "",
                    promocodeSet: false
                };
            }
        },
        INCREASE_CART(state, index) {
            state.cart[index].count = state.cart[index].count + 1;
        },
        DECREASE_CART(state, index) {
            state.cart[index].count = Math.max(1, state.cart[index].count - 1);
        },
        SET_CART_PARAMS(state, payload) {
            state.cart_params = {...state.cart_params, ...payload};
        },
        CLEAR_CART(state) {
            state.cart = [];
        },
        SET_CART_CLIENT(state, payload) {
            state.cart_client = payload;
        },
        UPDATE_CART_COUNT(state, payload) {
            const index = state.cart.findIndex(c => c.id === payload.id);
            state.cart[index].count = Math.min(state.cart[index].quantity, Math.max(payload.count, 0))
        },
        SYNC_CART_COUNT(state, products) {
            state.cart = state.cart.map(item => {
                const product = products.find(p => p.id === item.id);
                if (product) {
                    if (product.quantity === 0) {
                        return false;
                    }

                    if (product.quantity > 0 && item.count <= product.quantity) {
                        item.quantity = product.quantity;
                        return item;
                    }

                    if (product.quantity > 0 && item.count > product.quantity) {
                        item.quantity = product.quantity;
                        item.count = product.quantity;
                        return item;
                    }
                } else {
                    return false;
                }
                return item;
            }).filter(item => item);
        },
    },
    actions: {
        AFTER_SALE({commit}, payload) {
            commit('CLEAR_CART');
            commit('SET_CART_PARAMS', {
                isRed: false,
                isFree: false,
                payment_type: 0,
                partner_id: null,
                discountPercent: 0,
                promocode: "",
                promocodeSet: false,
            })
            commit('SET_CART_CLIENT', null);
        },
        SYNC_CART_COUNT({getters, commit}) {
            commit('SYNC_CART_COUNT', getters.PRODUCTS_v2);
        },
        ADD_TO_CART({commit, getters}, item) {
            if (item.count - item.quantity === 0) {
                this.$toast.error('Недостаточно товара');
                return;
            }
            const index = getters.CART.findIndex(c => c.id === item.id);
            if (index === -1) {
                commit('ADD_TO_CART', {...item, count: 1, product_price: item.product_price});
            } else {
                commit('INCREASE_CART', index);
            }
        },
    }
};

export default cartModule;
