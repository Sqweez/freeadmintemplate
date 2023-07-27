import ACTIONS from '../actions';
import MUTATIONS from '../mutations';
import {
    addCompanionBalance,
    createStore,
    deleteStore,
    editStore,
    getCities,
    getStores,
    getStoreTypes,
} from '@/api/stores';
import axiosClient from '@/utils/axiosClient';

const storeModule = {
    state: {
        stores: [],
        cities: [],
        store_types: [],
        legal_entities: [],
    },
    getters: {
        stores: (state) => state.stores,
        store_filters: (state) => [
            {
                id: -1,
                name: 'Все',
            },
            ...state.stores,
        ],
        store: (state) => (id) => state.stores.find((s) => s.id === id),
        store_types: (state) => state.store_types,
        shops: (state) => state.stores.filter((s) => s.type_id == 1),
        cities: (state) => state.cities,
        partner_stores: (state) => state.stores.filter((s) => s.type_id === 3),
        warehouses: (state) => state.stores.filter((s) => s.type_id === 2),
        legal_entities: (state) => state.legal_entities,
        legal_entity_by_id: (state) => (id) =>
            state.legal_entities.find((l) => l.id === +id),
    },
    mutations: {
        async [MUTATIONS.DELETE_STORE](state, payload) {
            state.stores = state.stores.filter((s) => s.id !== payload);
        },
        async [MUTATIONS.CREATE_STORE](state, payload) {
            state.stores.push(payload);
        },
        async [MUTATIONS.EDIT_STORE](state, payload) {
            state.stores = state.stores.map((s) => {
                if (s.id === payload.id) {
                    s = payload;
                }
                return s;
            });
        },
        [MUTATIONS.SET_STORE_TYPES](state, payload) {
            state.store_types = payload;
        },
        [MUTATIONS.SET_STORES](state, payload) {
            state.stores = payload;
        },
        [MUTATIONS.SET_CITIES](state, payload) {
            state.cities = payload;
        },
        setLegalEntities(state, payload) {
            state.legal_entities = payload;
        },
        createLegalEntity(state, payload) {
            state.legal_entities.push(payload);
        },
        updateLegalEntity(state, payload) {
            state.legal_entities = state.legal_entities.map((l) => {
                if (l.id === payload.id) {
                    l = { ...payload };
                }
                return l;
            });
        },
        createBankAccount(state, payload) {
            state.legal_entities = state.legal_entities.map((l) => {
                if (l.id === payload.legal_entity_id) {
                    l.bank_accounts.push(payload);
                }
                return l;
            });
        },
        updateBankAccount(state, payload) {
            state.legal_entities = state.legal_entities.map((l) => {
                if (l.id === payload.legal_entity_id) {
                    l.bank_accounts = l.bank_accounts.map((b) => {
                        if (b.id === payload) {
                            b = { ...payload };
                        }
                        return b;
                    });
                }
                return l;
            });
        },
    },
    actions: {
        async [ACTIONS.DELETE_STORE]({ commit }, payload) {
            await deleteStore(payload);
            await commit(MUTATIONS.DELETE_STORE, payload);
        },
        async [ACTIONS.CREATE_STORE]({ commit }, payload) {
            const store = await createStore(payload);
            await commit(MUTATIONS.CREATE_STORE, store);
        },
        async [ACTIONS.EDIT_STORE]({ commit }, payload) {
            const store = await editStore(payload);
            await commit(MUTATIONS.EDIT_STORE, store);
        },
        async [ACTIONS.GET_STORES]({ commit, dispatch }, store_id) {
            await dispatch(ACTIONS.GET_STORE_TYPES);
            const stores = await getStores(store_id);
            commit(MUTATIONS.SET_STORES, stores);
        },
        async [ACTIONS.GET_STORE_TYPES]({ commit }) {
            const store_types = await getStoreTypes();
            commit(MUTATIONS.SET_STORE_TYPES, store_types);
        },
        async [ACTIONS.GET_CITIES]({ commit }) {
            const cities = await getCities();
            commit(MUTATIONS.SET_CITIES, cities);
        },
        async [ACTIONS.ADD_COMPANION_BALANCE]({ commit }, payload) {
            const response = await addCompanionBalance(payload);
            commit(MUTATIONS.EDIT_STORE, response.data.data);
        },
        async getLegalEntities({ commit }) {
            const { data } = await axiosClient.get('v2/legal-entity');
            commit('setLegalEntities', data.entities);
        },
        async createLegalEntity({ commit }, payload) {
            const { data } = await axiosClient.post('v2/legal-entity', payload);
            commit('createLegalEntity', data.entity);
        },
        async updateLegalEntity({ commit }, payload) {
            const { data } = await axiosClient.patch(
                `v2/legal-entity/${payload.id}`,
                payload,
            );
            commit('updateLegalEntity', data.entity);
        },
        async createBankAccount({ commit }, payload) {
            const { data } = await axiosClient.post(
                'v2/legal-entity/bank-account',
                payload,
            );
            commit('createBankAccount', data.account);
        },
        async updateBankAccount({ commit }, payload) {
            const { data } = await axiosClient.patch(
                `v2/legal-entity/bank-account/${payload.id}`,
                payload,
            );
            commit('updateBankAccount', data.account);
        },
    },
};

export default storeModule;
