import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {createManufacturer, deleteManufacturers, editManufacturer, getManufacturers} from "../../api/manufacturer";

const manufacturerModule = {
    state: {
        manufacturers: []
    },
    getters: {
        manufacturers: state => state.manufacturers,
        manufacturer: state => id => state.manufacturers.find(m => m.id === id),
    },
    mutations: {
        [MUTATIONS.CREATE_MANUFACTURER] (state, payload) {
            state.manufacturers.push(payload);
        },
        [MUTATIONS.EDIT_MANUFACTURER] (state, payload) {
            state.manufacturers = state.manufacturers.map(m => {
                if (payload.id === m.id) {
                    m = payload;
                }
                return m;
            })
        },
        [MUTATIONS.DELETE_MANUFACTURER] (state, payload) {
            state.manufacturers = state.manufacturers.filter(m => m.id !== payload);
        },
        [MUTATIONS.SET_MANUFACTURERS] (state, payload) {
            state.manufacturers = payload;
        }
     },
    actions: {
        async [ACTIONS.GET_MANUFACTURERS] ({commit}) {
            const manufacturers = await getManufacturers();
            commit(MUTATIONS.SET_MANUFACTURERS, manufacturers);
        },
        async [ACTIONS.CREATE_MANUFACTURER] ({commit}, payload) {
            const manufacturer = await createManufacturer(payload);
            commit(MUTATIONS.CREATE_MANUFACTURER, manufacturer);
        },
        async [ACTIONS.EDIT_MANUFACTURER] ({commit}, payload) {
            const manufacturer = await editManufacturer(payload);
            commit(MUTATIONS.EDIT_MANUFACTURER, manufacturer);
        },
        async [ACTIONS.DELETE_MANUFACTURER] ({commit}, payload) {
            await deleteManufacturers(payload);
            commit(MUTATIONS.DELETE_MANUFACTURER, payload);
        },
    }
};

export default manufacturerModule;
