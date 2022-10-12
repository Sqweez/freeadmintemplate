import ACTIONS from '../actions'
import MUTATIONS from '../mutations';
import {createManufacturer, deleteManufacturers, editManufacturer, getManufacturers} from "../../api/manufacturer";
import axios from 'axios';
import axiosClient from '@/utils/axiosClient';

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
            const { data: { data } } = await axiosClient.post(`/manufacturers`, payload);
            commit(MUTATIONS.CREATE_MANUFACTURER, data);
        },
        async [ACTIONS.EDIT_MANUFACTURER] ({commit}, { payload, id }) {
            const { data: { data } } = await axios.post(`/api/manufacturers/${id}?_method=PATCH`, payload);
            commit(MUTATIONS.EDIT_MANUFACTURER, data);
        },
        async [ACTIONS.DELETE_MANUFACTURER] ({commit}, payload) {
            await deleteManufacturers(payload);
            commit(MUTATIONS.DELETE_MANUFACTURER, payload);
        },
    }
};

export default manufacturerModule;
