import ACTIONS from '../actions';
import {createGoal, deleteGoal, editGoal, getGoals} from "@/api/goals";

const goalModule = {
    state: {
        goals: [],
    },
    getters: {
        GOALS: state => state.goals,
    },
    mutations: {
        SET_GOALS(state, payload) {
            state.goals = payload;
        },
        DELETE_GOAL(state, payload) {
            state.goals = state.goals.filter(g => g.id != payload)
        },
        EDIT_GOAL(state, payload) {
           state.goals = state.goals.map(goal => {
               if (goal.id === payload.id) {
                   goal = payload;
               }
               return goal;
           })
        },
        CREATE_GOAL(state, payload) {
            state.goals.push(payload);
        }
    },
    actions: {
        async [ACTIONS.GET_GOALS]({commit}, payload) {
            const data = await getGoals();
            commit('SET_GOALS', data);
        },
        async [ACTIONS.DELETE_GOAL]({commit}, payload) {
            await deleteGoal(payload);
            commit('DELETE_GOAL', payload);
        },
        async [ACTIONS.EDIT_GOAL]({commit}, payload) {
            const data = await editGoal(payload);
            commit('EDIT_GOAL', data);
        },
        async [ACTIONS.CREATE_GOAL]({commit}, payload) {
            const data = await createGoal(payload);
            commit('SET_GOALS', data);
        }
    }
}

export default goalModule;
