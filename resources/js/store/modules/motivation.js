import ACTIONS from "@/store/actions";
import {createBrandsMotivation, getBrandsMotivationPlans} from "@/api/sale";

const motivationModule = {
    state: {
        brands_motivation_plan: [],
    },
    getters: {
        BRANDS_MOTIVATION_PLAN: s => s.brands_motivation_plan,
    },
    mutations: {
        [ACTIONS.GET_BRANDS_MOTIVATIONS_PLAN](state, payload) {
            state.brands_motivation_plan = payload;
        }
    },
    actions: {
        async [ACTIONS.CREATE_BRANDS_MOTIVATION]({commit}, payload) {
            try {
                this.$loading.enable();
                const plans = await createBrandsMotivation(payload);
                commit(ACTIONS.GET_BRANDS_MOTIVATIONS_PLAN, plans);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.GET_BRANDS_MOTIVATIONS_PLAN] ({commit}) {
            try {
                this.$loading.enable();
                const plans = await getBrandsMotivationPlans();
                commit(ACTIONS.GET_BRANDS_MOTIVATIONS_PLAN, plans);
            } catch {

            } finally {
                this.$loading.disable();
            }
        }
    }
};

export default motivationModule;
