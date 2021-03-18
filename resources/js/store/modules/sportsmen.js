import MUTATIONS from './../mutations';
import ACTIONS from './../actions';
import axios from 'axios';

const sportsmenModule = {
    state: {
        sportsmen: [],
    },
    getters: {
        SPORTSMEN: state => state.sportsmen,
        SPORTSMAN: state => id => state.sportsmen.find(s => s.id == id),
    },
    mutations: {
        [MUTATIONS.SET_SPORTSMEN](state, payload) {
            state.sportsmen = payload;
        },
        [MUTATIONS.DELETE_SPORTSMEN](state, payload) {
            state.sportsmen = state.sportsmen.filter(s => s.id != payload);
        },
        [MUTATIONS.CREATE_SPORTSMAN](state, payload) {
            state.sportsmen.push(payload);
        },
        [MUTATIONS.EDIT_SPORTSMEN](state, payload) {
            state.sportsmen = state.sportsmen.map(s => {
                if (s.id == payload.id) {
                    s = payload;
                }
                return s;
            })
        }
    },
    actions: {
        async [ACTIONS.GET_SPORTSMEN]({commit}) {
            const {data} = await axios.get('/api/sportsmen');
            commit(MUTATIONS.SET_SPORTSMEN, data);
        },
        async [ACTIONS.CREATE_SPORTSMEN]({commit}, payload) {
            const {data} = await axios.post('/api/sportsmen', payload)
            commit(MUTATIONS.CREATE_SPORTSMAN, data);
        },
        async [ACTIONS.EDIT_SPORTSMEN]({commit}, payload) {
            const {data} = await axios.patch(`/api/sportsmen/${payload.id}`, payload);
            commit(MUTATIONS.EDIT_SPORTSMEN, data);
        },
        async [ACTIONS.DELETE_SPORTSMEN]({commit}, payload) {
            await axios.delete(`/api/sportsmen/${payload}`);
            commit(MUTATIONS.DELETE_SPORTSMEN, payload);
        }
    }
}

export default sportsmenModule;
