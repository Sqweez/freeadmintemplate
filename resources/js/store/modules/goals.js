import ACTIONS from '../actions';
import {createGoal, deleteGoal, editGoal, getGoals} from "../../api/goals";

const goalModule = {
    state: {
        goals: [],
    },
    getters: {
        GOALS: state => state.goals,
    },
    mutations: {
        SET_GOALS(state, payload) {
            state.goals = payload.map(p => {
                p.image = p.image_origin;
                p.parts = p.parts.map(_p => {
                    _p.products = _p.product_ids;
                    return _p;
                })
                return p;
            });
        },
        DELETE_GOAL(state, payload) {
            state.goals = state.goals.filter(g => g.id != payload)
        },
        EDIT_GOAL(state, payload) {
            state.goals = state.goals.map(g => {
                if (g.id == payload[0].id) {
                    g = payload.map(p => {
                        p.parts = p.parts.map(_p => {
                            _p.products = _p.product_ids;
                            return _p;
                        })
                        p.image = p.image_origin;
                        return p;
                    })[0]
                }
                return g;
            })
        },
        CREATE_GOAL(state, payload) {
            state.goals.push(payload.data.map(p => {
                p.parts = p.parts.map(_p => {
                    _p.products = _p.product_ids;
                    return _p;
                })
                p.image = p.image_origin;
                return p;
            })[0])
        }
    },
    actions: {
        async [ACTIONS.GET_GOALS]({commit}, payload) {
            const {data} = await getGoals();
            commit('SET_GOALS', data);
        },
        async [ACTIONS.DELETE_GOAL]({commit}, payload) {
            await deleteGoal(payload);
            commit('DELETE_GOAL', payload);
        },
        async [ACTIONS.EDIT_GOAL]({commit}, payload) {
            const {data} = await editGoal(payload);
            commit('SET_GOALS', data);
        },
        async [ACTIONS.CREATE_GOAL]({commit}, payload) {
            const {data} = await createGoal(payload);
            commit('SET_GOALS', data);
        }
    }
}

export default goalModule;
