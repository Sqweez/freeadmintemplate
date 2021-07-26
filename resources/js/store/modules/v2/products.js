import {
    createProduct,
    getProducts,
    getProductsQuantity,
    getProduct,
    deleteProduct,
    editProduct, addProductQuantity, createProductSku, updateProductSku, getModeratorProducts, getProductBalance
} from "@/api/v2/products";
import showToast from "@/utils/toastService";
import {TOAST_TYPE} from "@/config/consts";
import {makeSale} from "@/api/sale";
import MUTATATIONS from "@/store/mutations";
import axios from 'axios';
import {changeProductCount} from "@/api/products";
import ACTIONS from "@/store/actions";
import {getArrivals} from "@/api/arrivals";

const state = {
    products_v2: [],
    quantities: [],
    product_v2: null,
    certificates: [],
    moderator_products: [],
    product_balance: [],
};

const getters = {
    PRODUCTS_v2: state => state.products_v2,
    MAIN_PRODUCTS_v2: state => {
        const array = [];
        return state.products_v2.filter(product => {
            if (array.findIndex(a => a.product_id === product.product_id) === -1) {
                array.push(product);
                return true;
            }
            return false;
        });
    },
    QUANTITIES_v2: state => state.quantities,
    PRODUCT_v2: state => state.product_v2,
    CERTIFICATES: s => s.certificates,
    MODERATOR_PRODUCTS: s => s.moderator_products,
    PRODUCT_BALANCE: s => s.product_balance,
};

const mutations = {
    SET_PRODUCTS_v2(state, payload) {
        state.products_v2 = payload;
    },
    SET_PRODUCT_QUANTITIES_v2(state, {quantities, store_id}) {
        let products = state.products_v2;
        products = products.map((product) => {
            const quantity = quantities.find(q => product.id == q.product_id);
            if (quantity) {
                product.quantity = quantity.quantity;
            } else {
                product.quantity = 0;
            }
            return product;
        });
        const _quantities = products.map(product => ({
            product_id: product.id,
            quantity: product.quantity
        }))
        state.quantities = {...state.quantities, [store_id]: _quantities};
        state.products_v2 = products;
    },
    CREATE_PRODUCT_v2(state, product) {
        state.products_v2.push(product);
    },
    SET_PRODUCT_v2(state, payload) {
        state.product_v2 = payload;
    },
    DELETE_PRODUCT_v2(state, id) {
        state.products_v2 = state.products_v2.filter(product => product.id !== id);
    },
    EDIT_PRODUCT_v2(state, products) {
        products.forEach((product) => {
            const findIndex = state.products_v2.findIndex(p => p.id === product.id);
            if (findIndex !== -1) {
                state.products_v2.splice(findIndex, 1, {
                    ...product,
                    quantity: state.products_v2[findIndex].quantity
                });
            }
        });
    },
    ADD_PRODUCT_QUANTITY_v2(state, batch) {
        state.products_v2 = state.products_v2.map(product => {
            if (product.id === batch.id) {
                product.quantity = batch.quantity;
            }
            return product;
        })
    },
    CREATE_PRODUCT_SKU(state, {id, product}) {
        state.products_v2.push(product);
        /*const findIndex = state.products_v2.findIndex(p => p.id === id);
        return state.products_v2.splice(findIndex + 1, 0, product)*/
    },
    UPDATE_PRODUCT_SKU(state, {id, product}) {
        state.products_v2 = state.products_v2.map(p => {
            if (p.id === id) {
                p = product;
            }
            return p;
        });
    },
    ON_PRODUCTS_SALE_v2(state, payload) {
        payload.forEach(item => {
            const findIndex = state.products_v2.findIndex(p => p.id === item.product_id);
            if (findIndex !== -1) {
                state.products_v2.splice(findIndex, 1, {
                    ...state.products_v2[findIndex],
                    quantity: item.quantity
                })
            }
        });
    },
    SET_CERTIFICATES(state, payload) {
        state.certificates = payload.filter(cert => cert.used_sale_id === 0).map(cert => {
            cert.name = `${cert.barcode} (${cert.amount}) тенге`
            return cert;
        });
    },
    CHANGE_COUNT_v2(state, payload) {
        state.products_v2 = state.products_v2.map(product => {
            if (product.id === payload.product_id) {
                product.quantity = payload.quantity;
            }
            return product;
        })
    },
    SET_MODERATOR_PRODUCTS(state, payload) {
        state.moderator_products = payload;
    },
    SET_PRODUCT_BALANCE(state, payload) {
        state.product_balance = payload;
    }
};

const actions = {
    async GET_PRODUCTS_v2({commit, dispatch, getters}, payload) {
        try {
            const { data } = await getProducts();
            commit('SET_PRODUCTS_v2', data.data);
        } catch (e) {
            console.log(e.response);
        } finally {

        }
    },
    async GET_PRODUCTS_QUANTITIES({commit, dispatch, getters}, store_id) {
        try {
            this.$loading.enable();
            const { data } = await getProductsQuantity(store_id);
            commit('SET_PRODUCT_QUANTITIES_v2', {
                quantities: data,
                store_id
            })
        } catch (e) {
            console.log(e.response);
        } finally {
            this.$loading.disable();
        }
    },
    async GET_MAIN_STORE_QUANTITIES({commit, dispatch}) {
        try {
            this.$loading.enable();
            const { data } = await getProductsQuantity(1);
            const response = await getProductsQuantity(6);
            const quantities = data.map(q => {
               const _q = response.data.find(d => q.product_id === q.product_id);
               q.quantity += +_q.quantity;
               return q;
            });
            commit('SET_PRODUCT_QUANTITIES_v2', {
                quantities: quantities,
                store_id: 1
            })
        } catch (e) {
            console.log(e.response);
        } finally {
            this.$loading.disable();
        }
    },
    async GET_PRODUCT_v2({commit, dispatch, getters}, product_id) {
        try {
            this.$loading.enable();
            const { data } = await getProduct(product_id);
            commit('SET_PRODUCT_v2', data.data);
        } catch (e) {

        } finally {
            this.$loading.disable();
        }
    },
    async CREATE_PRODUCT_v2({commit, dispatch, getters}, product) {
        try {
            this.$loading.enable();
            const { data } = await createProduct(product);
            commit('CREATE_PRODUCT_v2', data.data);
        } catch (e) {
            console.log(e.response);
        } finally {
            this.$loading.disable();
        }
    },
    async DELETE_PRODUCT_v2({commit}, id) {
        try {
            this.$loading.enable();
            await deleteProduct(id);
            commit('DELETE_PRODUCT_v2', id);
        } catch (e) {
            console.log(e.response);
        } finally {
            this.$loading.disable();
        }
    },
    async EDIT_PRODUCT_v2({commit}, payload) {
        try {
            this.$loading.enable();
            const { data } = await editProduct(payload);
            commit('EDIT_PRODUCT_v2', data.data);
        } catch (e) {
            console.log(e);
        } finally {
            this.$loading.disable();
        }
    },
    async ADD_PRODUCT_QUANTITY_v2({commit}, {id, batch, store_id}) {
        try {
            this.$loading.enable();
            const { data } = await addProductQuantity(id, batch);
            if (batch.store_id === store_id) {
                commit('ADD_PRODUCT_QUANTITY_v2', {id, quantity: data});
            }
        } catch (e) {
            console.log(e);
        } finally {
            this.$loading.disable();
        }
    },
    async CREATE_PRODUCT_SKU({commit}, {id, product, sku_id}) {
        try {
            this.$loading.enable();
            const { data } = await createProductSku(id, product);
            commit('CREATE_PRODUCT_SKU', {
                id: sku_id,
                product: data.data
            });
            showToast('Ассортимент добавлен', TOAST_TYPE.SUCCESS);
        } catch (e) {
            showToast('Не удалось создать ассортимент', TOAST_TYPE.ERROR);
        } finally {
            this.$loading.disable();
        }
    },
    async UPDATE_PRODUCT_SKU({commit}, {id, product}) {
        try {
            this.$loading.enable();
            const { data } = await updateProductSku(id, product);
            commit('UPDATE_PRODUCT_SKU', {
                id,
                product: data.data
            });
            showToast('Ассортимент отредактирован', TOAST_TYPE.SUCCESS);
        } catch (e) {
            showToast('Не удалось отредактировать ассортимент', TOAST_TYPE.ERROR);
        } finally {
            this.$loading.disable();
        }
    },
    async MAKE_SALE_v2 ({commit}, payload) {
        try {
            this.$loading.enable();
            const {product_quantities, client, sale_id} = await makeSale(payload);
            commit(MUTATATIONS.EDIT_CLIENT, client);
            commit('ON_PRODUCTS_SALE_v2', product_quantities);
            return sale_id;
        } catch (e) {
            throw Error();
        } finally {
            this.$loading.disable();
        }
    },
    async GET_CERTIFICATES({commit}) {
        const { data } = await axios.get('/api/v2/certificates');
        commit('SET_CERTIFICATES', data);
    },
    async CHANGE_COUNT_v2({commit}, payload) {
        this.$loading.enable();
        try {
            const response = await changeProductCount(payload);
            commit('CHANGE_COUNT_v2', response.data);
        } catch (e) {
            showToast(e.response.data.message, TOAST_TYPE.ERROR);
            throw e;
        } finally {
            this.$loading.disable();
        }

    },
    async GET_MODERATOR_PRODUCTS({commit}, payload) {
        try {
            this.$loading.enable();
            const { data } = await getModeratorProducts();
            commit('SET_MODERATOR_PRODUCTS', data.data);
        } catch (e) {
            console.log(e.response);
        } finally {
            this.$loading.disable();
        }
    },
    async GET_PRODUCT_BALANCE({commit, dispatch}) {
        await dispatch(ACTIONS.GET_STORES)
        const { data } = await getProductBalance();
        const response = await getArrivals(false);
        const totalArrivalsPurchasePrice = response.data.reduce((a, c) => {
            return a + +c.total_cost;
        }, 0);
        const totalArrivalsProductPrice = response.data.reduce((a, c) => {
            return a + +c.total_sale_cost;
        }, 0);
        commit('SET_PRODUCT_BALANCE', {...data, totalArrivalsPurchasePrice, totalArrivalsProductPrice});
    }
};


export default {
    state, getters, mutations, actions
}
