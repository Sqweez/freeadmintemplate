import axios from 'axios';
import axiosClient from '@/utils/axiosClient';

export default {
    state: {
        promocodes: [],
        promocode_types: [],
    },
    getters: {
        PROMOCODES: s => s.promocodes,
        PROMOCODE_TYPES: s => s.promocode_types,
    },
    mutations: {
        setPromocodes(state, payload) {
            state.promocodes = payload;
        },
        addPromocode(state, payload) {
            state.promocodes.push(payload);
        },
        editPromocode(state, payload) {
            state.promocodes = state.promocodes.map(p => {
                if (p.id == payload.id) {
                    p = payload;
                }
                return p;
            })
        },
        deletePromocode(state, payload) {
            state.promocodes = state.promocodes.filter(p => p.id !== payload)
        },
        setPromocodeTypes (state, types) {
            state.promocode_types = types;
        }
    },
    actions: {
        async getPromocodeTypes ({ commit }) {
            const { data } = await axiosClient.get('promocode/types');
            commit('setPromocodeTypes', data);
        },
        async getPromocodes({commit}, payload) {
            try {
                const response = await axios.get('/api/promocode')
                await commit('setPromocodes', response.data.data);
            } catch (e) {
                throw e;
            }
        },
        async addPromocode({commit}, payload) {
            try {
                const response = await axiosClient.post(`promocode`, payload);
                await commit('addPromocode', response.data.data);
                this.$toast.success('Промокод создан!')
            } catch (e) {
                throw e;
            }
        },
        async editPromocode({commit}, payload) {
            try {
                const response = await axiosClient.patch(`promocode/${payload.id}`, payload);
                await commit('editPromocode', response.data.data);
                this.$toast.success('Промокод отредактирован!')
            } catch (e) {
                throw e;
            }
        },
        async deletePromocode({commit}, payload) {
            try {
                await axios.delete(`/api/promocode/${payload}`);
                await commit('deletePromocode', payload);
                this.$toast.success('Промокод удален!')
            } catch (e) {
                throw e;
            }
        },
    }
}
