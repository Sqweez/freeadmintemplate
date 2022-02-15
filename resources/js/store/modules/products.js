import ACTIONS from '../actions';
import MUTATIONS from '../mutations';
import {
    addProductBatch,
    addProductRange,
    createProduct,
    deleteProduct,
    editProduct,
    getProducts,
    getMainProducts, getProductsBySearch, changeProductCount, getMarginTypes, setMarginTypes
} from "@/api/products";
import {getSaleTypes, makeSale} from "@/api/sale";
import axiosClient from "@/utils/axiosClient";

const productsModule = {
    state: {
        products: [],
        productsSearch: [],
        total: 0,
        prev: null,
        next: null,
        main_products: [],
        payment_types: [
            /*{id: 0, name: 'Наличные'},
            {id: 1, name: 'Безналичная оплата'},
            {id: 2, name: 'Kaspi RED/PayDa!'},
            {id: 3, name: 'Перевод на карту'},
            {id: 4, name: 'Kaspi Магазин'},
            {id: 5, name: 'Раздельная оплата'},
            {id: 6, name: 'Онлайн оплата'},
            {id: 7, name: 'Почта'}*/
        ],
    },
    getters: {
        products: state => state.products,
        product: state => id => state.products.find(p => p.id === id),
        totalProducts: state => state.total,
        prevLink: state => state.prev,
        nextLink: state => state.next,
        main_products: state => state.main_products,
        payment_types: state => state.payment_types,
        products_without_photo: state => state.products.filter(p => {
            if (p.product_images.length === 1 && p.product_images[0] === 'products/product_image_default.jpg') {
                return p;
            }
        }),
        products_with_photo: state => state.products.filter(p => {
            if (p.product_images.length >= 1 && p.product_images[0] !== 'products/product_image_default.jpg') {
                return p;
            }
        }),
        PRODUCTS_SEARCH: state => state.productsSearch,
    },
    mutations: {
        [MUTATIONS.CREATE_PRODUCT](state, payload) {
            state.products.push(payload);
        },
        [MUTATIONS.SET_MAIN_PRODUCTS](state, payload) {
            state.main_products = payload;
        },
        [MUTATIONS.SET_PRODUCTS](state, payload) {
            state.products = payload;
        },
        [MUTATIONS.EDIT_PRODUCT](state, payload) {
            state.products = state.products.map(p => {
                const item = payload.find(_p => _p.id === p.id);
                if (item) {
                    p = item;
                }
                return p;
            });
        },
        [MUTATIONS.DELETE_PRODUCT](state, payload) {
            state.products = state.products.filter(p => {
                return p.id !== payload;
            })
        },
        [MUTATIONS.ADD_PRODUCT_QUANTITY](state, payload) {
            state.products = state.products.map(p => {
                if (payload.id === p.id) {
                    if (typeof p.quantity !== 'number') {
                        p.quantity.push(payload);
                    } else {
                        p.quantity += payload.quantity;
                    }
                }
                return p;
            })
        },
        [MUTATIONS.ADD_PRODUCT_RANGE](state, payload) {
            state.products.push(payload);
        },
        [MUTATIONS.ON_SALE](state, payload) {
            state.products = state.products.map(p => {
                const index = payload.findIndex(i => i.id === p.id);
                if (index !== -1) {
                    p = payload[index];
                }
                return p;
            });
        },
        setTotal(state, payload) {
            state.total = payload;
        },
        setLinks(state, payload) {
            state.next = payload.next;
            state.prev = payload.prev;
        },
        clearProducts(state) {
            state.products = [];
        },
        setSearchProducts(state, payload) {
            state.productsSearch = payload;
        },
        changeCount(state, payload) {
            state.products = state.products.map(product => {
                if (product.id == payload.product_id) {
                    const findIndex = product.quantity.findIndex(q => q.id == payload.id);
                    if (findIndex !== -1) {
                        product.quantity[findIndex] = payload
                    } else {
                        product.quantity.push(payload);
                    }
                }
                return product;
            })
        },
        [MUTATIONS.SET_PAYMENT_TYPES] (state, payload) {
            state.payment_types = payload;
        },
    },
    actions: {
        async [ACTIONS.GET_PRODUCT]({commit}, store_id) {
            const response = await getProducts(store_id);
            const products = response.data.data;
            commit(MUTATIONS.SET_PRODUCTS, products);
        },
        async [ACTIONS.CREATE_PRODUCT]({commit}, payload) {
            const product = await createProduct(payload);
            commit(MUTATIONS.CREATE_PRODUCT, product);
        },
        async [ACTIONS.EDIT_PRODUCT]({commit}, payload) {
            const product = await editProduct(payload);
            commit(MUTATIONS.EDIT_PRODUCT, product);
        },
        async [ACTIONS.DELETE_PRODUCT]({commit}, payload) {
            await deleteProduct(payload);
            commit(MUTATIONS.DELETE_PRODUCT, payload);
        },
        async [ACTIONS.ADD_PRODUCT_QUANTITY]({commit}, payload) {
            await addProductBatch(payload);
            commit(MUTATIONS.ADD_PRODUCT_QUANTITY, {
                id: payload.product_id,
                quantity: payload.quantity,
                store_id: payload.store_id,
            })
        },
        async [ACTIONS.ADD_PRODUCT_RANGE]({commit}, payload) {
            const product = await addProductRange(payload);
            commit(MUTATIONS.ADD_PRODUCT_RANGE, product);
        },
        async [ACTIONS.MAKE_SALE]({commit}, payload) {
            const {products, client, sale_id} = await makeSale(payload);
            commit(MUTATIONS.ON_SALE, products);
            commit(MUTATIONS.EDIT_CLIENT, client);
            return sale_id;
        },
        async [ACTIONS.GET_MAIN_PRODUCTS]({commit}) {
            const {data} = await getMainProducts();
            commit(MUTATIONS.SET_MAIN_PRODUCTS, data);
        },
        async [ACTIONS.GET_SALE_TYPES]({commit}) {
            const data = await getSaleTypes();
            commit(MUTATIONS.SET_PAYMENT_TYPES, data);
        },
        async searchProducts({commit}, search) {
            const {data} = await getProductsBySearch(search);
            commit('setSearchProducts', data);
        },
        async changeCount({commit}, payload) {
            commit('enableLoading');
            try {
                const {data} = await changeProductCount(payload);
                commit('changeCount', data);
            } catch (e) {
                this.$toast.error(e.response.data.message);
            } finally {
                commit('disableLoading');
            }

        }
    }
};

export default productsModule;
