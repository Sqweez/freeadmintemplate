import ACTIONS from "../actions";
import MUTATIONS from "../mutations";
import {
    addCompanionBalance,
    createStore,
    deleteStore,
    editStore,
    getCities,
    getStores,
    getStoreTypes
} from "@/api/stores";

const storeModule = {
    state: {
        stores: [],
        cities: [],
        store_types: [],
    },
    getters: {
        stores: state => state.stores,
        store_filters: state => ([
            {
                id: -1,
                name: 'Все'
            },
            ...state.stores,
        ]),
        store: state => id => state.stores.find(s => s.id === id),
        store_types: state => state.store_types,
        shops: state => state.stores.filter(s => s.type_id == 1),
        cities: state => state.cities,
        partner_stores: state => state.stores.filter(s => s.type_id === 3),
        warehouses: state => state.stores.filter(s => s.type_id === 2)
    },
    mutations: {
        async [MUTATIONS.DELETE_STORE] (state, payload) {
            state.stores = state.stores.filter(s => s.id !== payload);
        },
        async [MUTATIONS.CREATE_STORE] (state, payload) {
            state.stores.push(payload);
        },
        async [MUTATIONS.EDIT_STORE] (state, payload) {
            state.stores = state.stores.map(s => {
                if (s.id === payload.id) {
                    s = payload;
                }
                return s;
            })
        },
        [MUTATIONS.SET_STORE_TYPES] (state, payload) {
            state.store_types = payload;
        },
        [MUTATIONS.SET_STORES] (state, payload) {
            state.stores = payload;
        },
        [MUTATIONS.SET_CITIES] (state, payload) {
            state.cities = payload;
        }
    },
    actions: {
        async [ACTIONS.DELETE_STORE] ({commit}, payload) {
            await deleteStore(payload);
            await commit(MUTATIONS.DELETE_STORE, payload);
        },
        async [ACTIONS.CREATE_STORE] ({commit}, payload) {
            const store = await createStore(payload);
            await commit(MUTATIONS.CREATE_STORE, store);
        },
        async [ACTIONS.EDIT_STORE] ({commit}, payload) {
            const store = await editStore(payload);
            await commit(MUTATIONS.EDIT_STORE, store);
        },
        async [ACTIONS.GET_STORES] ({commit, dispatch}, store_id) {
            await dispatch(ACTIONS.GET_STORE_TYPES);
            const stores = await getStores(store_id);
            commit(MUTATIONS.SET_STORES, stores);
        },
        async [ACTIONS.GET_STORE_TYPES] ({commit}) {
            const store_types = await getStoreTypes();
            commit(MUTATIONS.SET_STORE_TYPES, store_types);
        },
        async [ACTIONS.GET_CITIES] ({commit}) {
            const cities = await getCities();
            commit(MUTATIONS.SET_CITIES, cities);
        },
        async [ACTIONS.ADD_COMPANION_BALANCE] ({commit}, payload) {
            const response = await addCompanionBalance(payload);
            commit(MUTATIONS.EDIT_STORE, response.data.data);
        }
    }
};

export default storeModule;
