import {getArrivals} from "@/api/arrivals";

const arrivalModule = {
    state: {
        arrivals: [],
        currentArrival: {},
        currentMoneyRate: 0,
        currentChildStore: -1,
    },
    getters: {
        ARRIVALS: s => s.arrivals.map(arrival => {
            arrival.search = arrival.products.map(product => {
                return `${product.product_name} ${product.manufacturer.manufacturer_name}`
            })
            return arrival;
        }),
        CURRENT_ARRIVAL: s => ({
            cart: s.currentArrival,
            moneyRate: s.currentMoneyRate,
            child_store: s.currentChildStore,
        }),
    },
    mutations: {
        SET_ARRIVALS(state, arrivals) {
            state.arrivals = arrivals;
        },
        UPDATE_CURRENT_ARRIVAL(state, payload) {
            state.currentArrival = payload;
        },
        UPDATE_MONEY_RATE(state, payload) {
            state.currentMoneyRate = payload;
        },
        UPDATE_CHILD_STORE(state, payload) {
            state.currentChildStore = payload;
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
