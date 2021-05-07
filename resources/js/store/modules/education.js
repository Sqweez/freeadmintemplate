import {createEducation, deleteEducation, editEducation, getEducations} from "@/api/educations";

export default {
    state: {
        educations: [],
    },
    getters: {
        EDUCATIONS: s => s.educations,
    },
    mutations: {
        SET_EDUCATIONS(state, payload) {
            state.educations = payload;
        },
        ADD_EDUCATION(state, payload) {
            state.educations.push(payload);
        },
        DELETE_EDUCATION(state, id) {
            state.educations = state.educations.filter(t => t.id !== id);
        },
        EDIT_EDUCATION(state, payload) {
            state.educations = state.educations.map(t => {
                if (t.id === payload.id) {
                    t = payload;
                }
                return t;
            })
        },
    },
    actions: {
        async GET_EDUCATIONS({commit, dispatch, getters}) {
            try {
                commit('enableLoading');
                const { data } = await getEducations();
                commit('SET_EDUCATIONS', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async EDIT_EDUCATION({commit, dispatch, getters}, {education, id}) {
            try {
                commit('enableLoading');
                const { data } = await editEducation(education, id);
                commit('EDIT_EDUCATION', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async CREATE_EDUCATION({commit, dispatch, getters}, task) {
            try {
                commit('enableLoading');
                const { data } = await createEducation(task);
                commit('ADD_EDUCATION', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async DELETE_EDUCATION({commit}, taskId) {
            try {
                commit('enableLoading');
                await deleteEducation(taskId);
                commit('DELETE_EDUCATION', taskId);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
    }
}
