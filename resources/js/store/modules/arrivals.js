import {getArrivals} from "@/api/arrivals";

const arrivalModule = {
    state: {
        arrivals: [],
    },
    getters: {
        ARRIVALS: s => s.arrivals,
    },
    mutations: {
        SET_ARRIVALS(state, arrivals) {
            state.arrivals = arrivals;
        }
    },
    actions: {
        async GET_ARRIVALS({commit}, payload) {
            const { data } = await getArrivals(payload);
            commit('SET_ARRIVALS', data);
        }
    }
};

export default arrivalModule;
