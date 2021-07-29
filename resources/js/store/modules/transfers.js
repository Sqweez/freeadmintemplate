import {createTransfer, editTransferStore, getTransfers} from "@/api/transfers";
import ACTIONS from "../actions";
import MUTATIONS from "../mutations";

const transferModule = {
    state: {
        transfers: [],
    },
    getters: {
        transfers: state => state.transfers,
    },
    mutations: {
        setTransfers(state, payload) {
            state.transfers = payload;
        },
        addTransfer(state, payload) {
            state.transfers.push(payload)
        },
        editTransfer(state, transfer) {
            state.transfers = state.transfers.map(t => {
                if (t.id === transfer.id) {
                    t = transfer;
                }
                return t;
            })
        }
    },
    actions: {
        async getTransfers({commit}, payload) {
            const transfers = await getTransfers(payload);
            commit('setTransfers', transfers);
        },
        async [ACTIONS.MAKE_TRANSFER] ({commit}, payload) {
            await createTransfer(payload);
        },
        async editTransfer({commit}, payload) {
            try {
                this.$loading.enable();
                const { data } = await editTransferStore(payload);
                commit('editTransfer', data.data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        }
    }
};

export default transferModule;
