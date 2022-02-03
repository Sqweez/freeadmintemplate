import {PRODUCT_MODAL_EVENTS} from "@/config/consts";

const modalModule = {
    namespaced: true,
    state: {
        productModal: false,
        productModalId: null,
        productModalAction: null,
        productSkuModal: false,
        productSkuModalId: null,
        productSkuModalEdit: false,
    },
    getters: {
        PRODUCT_MODAL: s => s.productModal,
        PRODUCT_MODAL_ID: s => s.productModalId,
        PRODUCT_MODAL_ACTION: s => s.productModalAction,
        PRODUCT_SKU_MODAL: s => s.productSkuModal,
        PRODUCT_SKU_MODAL_ID: s => s.productSkuModalId,
        PRODUCT_SKU_MODAL_EDIT: s => s.productSkuModalEdit,
    },
    mutations: {
        showProductModal(state, {id = null, action = PRODUCT_MODAL_EVENTS.ADD_PRODUCT}) {
            state.productModal = true;
            state.productModalId = id;
            state.productModalAction = action;
        },
        closeProductModal(state) {
            state.productModal = false;
            state.productModalId = null;
            state.productModalAction = null;
        },
        showProductSkuModal(state, {id = null, edit = false}) {
            state.productSkuModal = true;
            state.productSkuModalId = id;
            state.productSkuModalEdit = edit;
        },
        closeProductSkuModal(state) {
            state.productSkuModal = false;
            state.productSkuModalId = null;
            state.productSkuModalEdit = false;
        }
    },
    actions: {
    }
}

export default modalModule;
