import {
    createProduct,
    getProducts,
    getProductsQuantity,
    getProduct,
    deleteProduct,
    editProduct, addProductQuantity, createProductSku, updateProductSku
} from "@/api/v2/products";
import showToast from "@/utils/toast";
import {TOAST_TYPE} from "@/config/consts";
import {makeSale} from "@/api/sale";
import MUTATATIONS from "@/store/mutations";

const state = {
    products_v2: [],
    quantities: [],
    product_v2: null
};

const getters = {
    PRODUCTS_v2: state => state.products_v2,
    QUANTITIES_v2: state => state.quantities,
    PRODUCT_v2: state => state.product_v2,
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
        const findIndex = state.products_v2.findIndex(p => p.id === id);
        return state.products_v2.splice(findIndex + 1, 0, product)
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
        const quantities = getters.QUANTITIES_v2;
        if (!quantities[store_id]) {
            commit('enableLoading');
        } else {
            commit('SET_PRODUCT_QUANTITIES_v2', {
                quantities: quantities[store_id],
                store_id
            })
        }
        try {
            const { data } = await getProductsQuantity(store_id);
            commit('SET_PRODUCT_QUANTITIES_v2', {
                quantities: data,
                store_id
            })
        } catch (e) {
            console.log(e.response);
        } finally {
            commit('disableLoading');
        }
    },
    async GET_PRODUCT_v2({commit, dispatch, getters}, product_id) {
        try {
            commit('enableLoading');
            const { data } = await getProduct(product_id);
            commit('SET_PRODUCT_v2', data.data);
        } catch (e) {

        } finally {
            commit('disableLoading');
        }
    },
    async CREATE_PRODUCT_v2({commit, dispatch, getters}, product) {
        try {
            commit('enableLoading');
            const { data } = await createProduct(product);
            commit('CREATE_PRODUCT_v2', data.data);
        } catch (e) {
            console.log(e.response);
        } finally {
            commit('disableLoading');
        }
    },
    async DELETE_PRODUCT_v2({commit}, id) {
        try {
            commit('enableLoading');
            await deleteProduct(id);
            commit('DELETE_PRODUCT_v2', id);
        } catch (e) {
            console.log(e.response);
        } finally {
            commit('disableLoading');
        }
    },
    async EDIT_PRODUCT_v2({commit}, payload) {
        try {
            commit('enableLoading');
            const { data } = await editProduct(payload);
            commit('EDIT_PRODUCT_v2', data.data);
        } catch (e) {
            console.log(e);
        } finally {
            commit('disableLoading');
        }
    },
    async ADD_PRODUCT_QUANTITY_v2({commit}, {id, batch, store_id}) {
        try {
            commit('enableLoading');
            const { data } = await addProductQuantity(id, batch);
            if (batch.store_id === store_id) {
                commit('ADD_PRODUCT_QUANTITY_v2', {id, quantity: data});
            }
        } catch (e) {
            console.log(e);
        } finally {
            commit('disableLoading');
        }
    },
    async CREATE_PRODUCT_SKU({commit}, {id, product, sku_id}) {
        try {
            commit('enableLoading');
            const { data } = await createProductSku(id, product);
            commit('CREATE_PRODUCT_SKU', {
                id: sku_id,
                product: data.data
            });
            showToast('Ассортимент добавлен', TOAST_TYPE.SUCCESS);
        } catch (e) {
            showToast('Не удалось создать ассортимент', TOAST_TYPE.ERROR);
        } finally {
            commit('disableLoading');
        }
    },
    async UPDATE_PRODUCT_SKU({commit}, {id, product}) {
        try {
            commit('enableLoading');
            const { data } = await updateProductSku(id, product);
            commit('UPDATE_PRODUCT_SKU', {
                id,
                product: data.data
            });
            showToast('Ассортимент отредактирован', TOAST_TYPE.SUCCESS);
        } catch (e) {
            showToast('Не удалось отредактировать ассортимент', TOAST_TYPE.ERROR);
        } finally {
            commit('disableLoading');
        }
    },
    async MAKE_SALE_v2 ({commit}, payload) {
        try {
            commit('enableLoading');
            const {product_quantities, client, sale_id} = await makeSale(payload);
            commit(MUTATATIONS.EDIT_CLIENT, client);
            commit('ON_PRODUCTS_SALE_v2', product_quantities);
            return sale_id;
        } catch (e) {
            throw Error();
        } finally {
            commit('disableLoading');
        }
    }
};


export default {
    state, getters, mutations, actions
}