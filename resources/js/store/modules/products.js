import ACTIONS from '../actions';
import MUTATIONS from '../mutations';
import {
    addProductBatch,
    addProductRange,
    createProduct,
    deleteProduct,
    editProduct,
    getProducts,
    getMainProducts
} from "../../api/products";
import {makeSale} from "../../api/sale";

const productsModule = {
    state: {
        products: [],
        total: 0,
        prev: null,
        next: null,
        main_products: [],
        paymentTypes: [
            {id: 0, name: 'Наличные'},
            {id: 1, name: 'Безналичная оплата'},
            {id: 2, name: 'Kaspi RED/PayDa!'},
            {id: 3, name: 'Перевод на карту'},
        ]
    },
    getters: {
        products: state => state.products,
        product: state => id =>  state.products.find(p => p.id === id),
        totalProducts: state => state.total,
        prevLink: state => state.prev,
        nextLink: state => state.next,
        main_products: state => state.main_products,
        payment_types: state => state.paymentTypes,
    },
    mutations: {
        [MUTATIONS.CREATE_PRODUCT](state, payload) {
            state.products.push(payload);
        },
        [MUTATIONS.SET_MAIN_PRODUCTS] (state, payload) {
          state.main_products = payload;
        },
        [MUTATIONS.SET_PRODUCTS] (state, payload) {
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
        [MUTATIONS.ON_SALE] (state, payload) {
            state.products = state.products.map(p => {
                const index = payload.findIndex(i => i.id === p.id);
                if (index !== -1) {
                    p = payload[index];
                }
                return p;
            });
        },
        setTotal (state, payload) {
            state.total = payload;
        },
        setLinks(state, payload) {
            state.next = payload.next;
            state.prev = payload.prev;
        },
        clearProducts(state) {
            state.products = [];
        }
    },
    actions: {
        async [ACTIONS.GET_PRODUCT] ({commit}, store_id) {
            const response = await getProducts(store_id);
            const products = response.data.data;
            commit(MUTATIONS.SET_PRODUCTS, products);
        },
        async [ACTIONS.CREATE_PRODUCT] ({commit}, payload) {
            const product = await createProduct(payload);
            commit(MUTATIONS.CREATE_PRODUCT, product);
        },
        async [ACTIONS.EDIT_PRODUCT] ({commit}, payload) {
            const product = await editProduct(payload);
            console.log(product);
            commit(MUTATIONS.EDIT_PRODUCT, product);
        },
        async [ACTIONS.DELETE_PRODUCT] ({commit}, payload) {
            await deleteProduct(payload);
            commit(MUTATIONS.DELETE_PRODUCT, payload);
        },
        async [ACTIONS.ADD_PRODUCT_QUANTITY] ({commit}, payload) {
            await addProductBatch(payload);
            commit(MUTATIONS.ADD_PRODUCT_QUANTITY, {
                id: payload.product_id,
                quantity: payload.quantity,
                store_id: payload.store_id,
            })
        },
        async [ACTIONS.ADD_PRODUCT_RANGE] ({commit}, payload) {
            const product = await addProductRange(payload);
            commit(MUTATIONS.ADD_PRODUCT_RANGE, product);
        },
        async [ACTIONS.MAKE_SALE] ({commit}, payload) {
            const {products, client, sale_id} = await makeSale(payload);
            commit(MUTATIONS.ON_SALE, products);
            commit(MUTATIONS.EDIT_CLIENT, client);
            return sale_id;
        },
        async [ACTIONS.GET_MAIN_PRODUCTS] ({commit}) {
            const {data} = await getMainProducts();
            commit(MUTATIONS.SET_MAIN_PRODUCTS, data);
        }
    }
};

export default productsModule;
