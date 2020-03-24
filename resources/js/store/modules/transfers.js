import {createTransfer, getTransfers} from "../../api/transfers";
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
        }
    },
    actions: {
        async getTransfers({commit}, payload) {
            const transfers = await getTransfers(payload);
            commit('setTransfers', transfers);
        },
        async [ACTIONS.MAKE_TRANSFER] ({commit}, payload) {
            const {products} = await createTransfer(payload);
            commit(MUTATIONS.ON_SALE, products);
        }
    }
};

export default transferModule;
