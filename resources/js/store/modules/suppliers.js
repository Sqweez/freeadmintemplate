import ACTIONS from "@/store/actions";
import {createSupplier, deleteSupplier, editSupplier, getSuppliers} from "@/api/suppliers";

const suppliersModule = {
    state: {
        suppliers: [],
    },
    getters: {
        SUPPLIERS: s => s.suppliers,
    },
    mutations: {
        addSupplier(state, supplier) {
            state.suppliers.push(supplier);
        },
        setSuppliers(state, suppliers) {
            state.suppliers = suppliers;
        },
        editSupplier(state, supplier) {
            state.suppliers = state.suppliers.map(s => {
                if (s.id === supplier.id) {
                    s = supplier;
                }
                return s;
            })
        },
        deleteSupplier(state, id) {
            state.suppliers = state.suppliers.filter(s => s.id !== id);
        }
    },
    actions: {
        async [ACTIONS.CREATE_SUPPLIER]({commit}, supplier) {
            try {
                commit('enableLoading');
                const response = await createSupplier(supplier);
                commit('addSupplier', response.data);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async [ACTIONS.GET_SUPPLIERS]({commit}) {
            const response = await getSuppliers();
            commit('setSuppliers', response.data);
        },
        async [ACTIONS.EDIT_SUPPLIER]({commit}, payload) {
            try {
                commit('enableLoading');
                const response = await editSupplier(payload.supplier, payload.id);
                commit('editSupplier', response.data);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async [ACTIONS.DELETE_SUPPLIER] ({commit}, id) {
            try {
                commit('enableLoading');
                await deleteSupplier(id);
                commit('deleteSupplier', id);
            } catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        }
    }
}

export default suppliersModule;
