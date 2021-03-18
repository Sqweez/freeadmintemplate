import ACTIONS from "../actions";
import MUTATIONS from "../mutations";
import axios from 'axios';

const plansModule = {
    state: {
        plans: [],
    },
    getters: {
        PLANS: state => state.plans,
        PLAN_BY_STORE: state => id => state.plans.find(p => p.store_id == id)
    },
    mutations: {
        [MUTATIONS.SET_PLANS] (state, payload) {
            state.plans = payload;
        }
    },
    actions: {
        async [ACTIONS.GET_PLANS] ({commit}, payload) {
            const { data } = await axios.get('/api/plans');
            commit(MUTATIONS.SET_PLANS, data);
        },
        async [ACTIONS.SAVE_PLANS] ({commit}, payload) {
            const plans = payload.filter(p => p.week_plan > 0 || p.month_plan > 0);
            if (plans.length) {
                const { data } = await axios.post('/api/plans', plans);
                commit(MUTATIONS.SET_PLANS, data);
            }
        }
    }
};

export default plansModule;
