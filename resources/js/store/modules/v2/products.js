import {
    createProduct,
    getProducts,
    getProductsQuantity,
    getProduct,
    deleteProduct,
    editProduct,
    addProductQuantity,
    createProductSku,
    updateProductSku,
    getModeratorProducts,
    getProductBalance,
    createProductSaleEarnings, getProductSaleEarnings, removeTagFromProduct, getIherbProducts
} from "@/api/v2/products";
import {makeSale} from "@/api/sale";
import MUTATATIONS from "@/store/mutations";
import axios from 'axios';
import {changeProductCount, getMarginTypes, setMarginTypes, setProductTags, updateMarginTypes} from "@/api/products";
import ACTIONS from "@/store/actions";
import {getArrivals} from "@/api/arrivals";
import store from "@/store";
import {getTransfers} from "@/api/transfers";
import MUTATIONS from "@/store/mutations";

const state = {
    products_v2: [],
    quantities: [],
    product_v2: null,
    certificates: [],
    moderator_products: [],
    product_balance: [],
    product_earnings: [],
    margin_types: [],
    iherb_products: [],
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
    MAIN_MODERATOR_PRODUCTS: state => {
        const array = [];
        return state.moderator_products.filter(product => {
            if (array.findIndex(a => a.product_id === product.product_id) === -1) {
                array.push(product);
                return true;
            }
            return false;
        });
    },
    PRODUCT_BALANCE: s => s.product_balance,
    PRODUCT_EARNINGS: s => s.product_earnings,
    MARGIN_TYPES: s => s.margin_types,
    IHERB_PRODUCTS: s => s.iherb_products,
};

const mutations = {
    SET_PRODUCTS_v2(state, payload) {
        state.products_v2 = payload;
    },
    SET_MODERATOR_PRODUCT_QUANTITIES_v2(state, {quantities, store_id}) {
        let products = state.moderator_products;
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
        state.moderator_products = products;
    },
    SET_PRODUCT_QUANTITIES_v2(state, {quantities, store_id}) {
        if (store_id !== -1) {
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
        } else {
            state.quantities = quantities;
        }
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
    },
    [MUTATATIONS.SET_PRODUCT_SALE_EARNINGS](state, payload) {
        state.product_earnings = payload;
    },
    [MUTATIONS.SET_MARGIN_TYPES] (state, payload) {
        state.margin_types = payload;
    },
    [MUTATIONS.UPDATE_PRODUCT_MARGIN_TYPES] (state, payload) {
        state.products_v2 = state.products_v2.map(s => {
            const product = payload.find(p => p.id === s.id);
            if (product) {
                s.margin_type = product.margin_type;
            }
            return s;
        })
    },
    SET_PRODUCT_TAGS (state, {product_id, tag_id}) {
        state.moderator_products = state.moderator_products.map(product => {
            if (product.product_id === product_id) {
                product.tags = product.tags.filter(t => t.id !== tag_id);
            }
            return product;
        })
    },
    SET_IHERB_PRODUCTS (state, products) {
        state.iherb_products = products;
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
    async GET_IHERB_PRODUCTS ({commit}) {
        try {
            this.$loading.enable();
            const { data } = await getIherbProducts();
            commit('SET_IHERB_PRODUCTS', data.data);
        } catch (e) {
            console.log(e.response);
        } finally {
            this.$loading.disable();
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
            console.log(e);
        } finally {
            this.$loading.disable();
        }
    },
    async GET_MODERATOR_PRODUCT_QUANTITIES({commit}, store_id) {
        try {
            this.$loading.enable();
            const { data } = await getProductsQuantity(store_id);
            commit('SET_MODERATOR_PRODUCT_QUANTITIES_v2', {
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
               const _q = response.data.find(d => d.product_id === q.product_id);
               if (_q) {
                   q.quantity += +_q.quantity;
               }
               return q;
            });
            const storeQuantities = response.data.filter((item) => {
                return quantities.findIndex(q => q.product_id === item.product_id) === -1;
            })
            console.log(storeQuantities);
            commit('SET_PRODUCT_QUANTITIES_v2', {
                quantities: [...quantities, ...storeQuantities],
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
            this.$toast.success('Ассортимент добавлен');
        } catch (e) {
            this.$toast.error('Не удалось создать ассортимент');
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
            this.$toast.success('Ассортимент отредактирован');
        } catch (e) {
            this.$toast.error('Не удалось отредактировать ассортимент');
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
            this.$toast.error(e.response.data.message);
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
        const transferResponse = await getTransfers({mode: 'current'});
        const totalTransfersPurchasePrice = transferResponse.reduce((a, c) => {
            return a + +c.total_purchase_cost;
        }, 0);
        const totalTransfersProductPrice = transferResponse.reduce((a, c) => {
            return a + +c.total_cost;
        }, 0);
        commit('SET_PRODUCT_BALANCE', {...data, totalArrivalsPurchasePrice, totalArrivalsProductPrice, totalTransfersPurchasePrice, totalTransfersProductPrice});
    },
    async [ACTIONS.GET_PRODUCT_SALE_EARNINGS]({commit}) {
        try {
            this.$loading.enable();
            const { data } = await getProductSaleEarnings();
            commit(MUTATATIONS.SET_PRODUCT_SALE_EARNINGS, data);
        } catch (e) {

        } finally {
            this.$loading.disable();
        }
    },
    async [ACTIONS.CREATE_PRODUCT_SELLERS_EARNINGS]({commit, dispatch}, payload) {
        try {
            this.$loading.enable();
            await createProductSaleEarnings(payload);
            dispatch(ACTIONS.GET_STORES);
            dispatch(ACTIONS.GET_PRODUCT_SALE_EARNINGS);
        } catch (e) {

        } finally {
            this.$loading.disable();
        }
    },
    async [ACTIONS.GET_MARGIN_TYPES] ({commit}) {
        const data = await getMarginTypes();
        commit(MUTATIONS.SET_MARGIN_TYPES, data);
    },
    async [ACTIONS.SET_MARGIN_TYPES] ({ commit }, payload) {
        this.$loading.enable();
        const data = await setMarginTypes(payload);
        commit(MUTATIONS.UPDATE_PRODUCT_MARGIN_TYPES, data);
        this.$loading.disable();
    },
    async [ACTIONS.UPDATE_MARGIN_TYPES] ({commit, dispatch}, payload) {
        this.$loading.enable();
        await updateMarginTypes(payload);
        this.$loading.disable();
        dispatch(ACTIONS.GET_MARGIN_TYPES);
    },
    async [ACTIONS.SET_TAGS] ({commit, dispatch}, payload) {
        this.$loading.enable();
        await setProductTags(payload);
        await dispatch('GET_MODERATOR_PRODUCTS');
        this.$loading.disable();
    },
    async [ACTIONS.REMOVE_TAG] ({ commit, dispatch }, payload) {
        try {
            this.$loading.enable();
            await removeTagFromProduct(payload);
            commit('SET_PRODUCT_TAGS', payload);
        } catch (e) {
            console.log(e);
        } finally {
            this.$loading.disable();
        }
    }
};


export default {
    state, getters, mutations, actions
}
