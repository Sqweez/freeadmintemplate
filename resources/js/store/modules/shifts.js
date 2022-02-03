import {
    createShift,
    createShiftPenalty,
    deleteShiftPenalty, editShift, getPayrolls,
    getShiftPenalties, getShifts,
    getShiftTaxes,
    openShift,
    saveShiftTaxes
} from "@/api/v2/shifts";
import ACTIONS from "@/store/actions";

const shiftModule = {
    state: {
        shiftTaxes: [],
        penalties: [],
        payroll: [],
        shifts: [],
    },
    getters: {
        PENALTIES: s => s.penalties,
        PAYROLL: s => s.payroll,
        SHIFTS: s => s.shifts,
    },
    mutations: {
        SET_SHIFT_TAXES(state, payload) {
            state.shiftTaxes = payload;
        },
        CREATE_SHIFT_PENALTY(state, penalty) {
            state.penalties.push(penalty);
        },
        SET_SHIFT_PENALTIES(state, payload) {
            state.penalties = payload;
        },
        [ACTIONS.DELETE_SHIFT_PENALTY] (state, id) {
            state.penalties = state.penalties.filter(s => s.id !== id);
        },
        SET_PAYROLL(state, payload) {
            state.payroll = payload;
        },
        SET_SHIFTS(state, payload) {
            state.shifts = payload;
        }
    },
    actions: {
        async [ACTIONS.OPEN_SHIFT]({commit, getters}, user) {
            if (!getters.IS_SELLER && !getters.IS_SENIOR_SELLER) {
                return;
            }
            await openShift({
                store_id: user.store_id,
                user_id: user.id
            })
        },
        async [ACTIONS.GET_SHIFT_TAXES]({commit}) {
            this.$loading.enable()
            const {data} = await getShiftTaxes();
            commit('SET_SHIFT_TAXES', data);
            this.$loading.disable()
        },
        async [ACTIONS.SAVE_SHIFT_TAXES]({commit}, taxes) {
            try {
                this.$loading.enable();
                const { data } = await saveShiftTaxes(taxes);
                commit('SET_SHIFT_TAXES', data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.GET_SHIFT_PENALTIES] ({commit}) {
            this.$loading.enable()
            const { data } = await getShiftPenalties();
            commit('SET_SHIFT_PENALTIES', data.data);
            this.$loading.disable()
        },
        async [ACTIONS.CREATE_SHIFT_PENALTY]({commit}, penalty) {
            try {
                this.$loading.enable();
                const { data } = await createShiftPenalty(penalty);
                commit('CREATE_SHIFT_PENALTY', data.data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.DELETE_SHIFT_PENALTY] ({commit}, id) {
            try {
                this.$loading.enable();
                await deleteShiftPenalty(id);
                commit(ACTIONS.DELETE_SHIFT_PENALTY, id);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.GET_PAYROLL] ({commit}, period) {
            try {
                this.$loading.enable();
                const { data } = await getPayrolls(period);
                commit('SET_PAYROLL', data);
            } catch {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.GET_SHIFTS] ({commit}, date) {
            try {
                this.$loading.enable();
                const { data } = await getShifts(date);
                commit('SET_SHIFTS', data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.CREATE_SHIFT] ({commit}, shift) {
            try {
                this.$loading.enable();
                await createShift(shift);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.EDIT_SHIFT] ({commit}, payload) {
            try {
                this.$loading.enable();
                await editShift(payload);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        }
    }
};

export default shiftModule;
