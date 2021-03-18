import axios from 'axios';
import showToast from "../../utils/toast";

export default {
    state: {
        promocodes: [],
    },
    getters: {
        PROMOCODES: s => s.promocodes,
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
        }
    },
    actions: {
        async getPromocodes({commit}, payload) {
            try {
                const response = await axios.get('/api/promocode')
                await commit('setPromocodes', response.data.data);
            } catch (e) {
                console.error(e);
            }
        },
        async addPromocode({commit}, payload) {
            try {
                const response = await axios.post(`/api/promocode`, payload);
                await commit('addPromocode', response.data.data);
                showToast('Промокод создан!')
            } catch (e) {
                console.error(e);
            }
        },
        async editPromocode({commit}, payload) {
            try {
                const response = await axios.patch(`/api/promocode/${payload.id}`, payload);
                await commit('editPromocode', response.data.data);
                showToast('Промокод отредактирован!')
            } catch (e) {
                console.error(e);
            }
        },
        async deletePromocode({commit}, payload) {
            try {
                await axios.delete(`/api/promocode/${payload}`);
                await commit('deletePromocode', payload);
                showToast('Промокод удален!')
            } catch (e) {
                console.error(e);
            }
        },
    }
}
