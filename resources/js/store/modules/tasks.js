import {changeTaskStatus, createTask, deleteTask, editTask, getCurrentTasks, getTasks} from "@/api/tasks";

export default {
    state: {
        tasks: [],
        currentTasks: [],
    },
    getters: {
        TASKS: s => s.tasks,
        CURRENT_TASKS: s => s.currentTasks,
    },
    mutations: {
        SET_TASKS(state, payload) {
            state.tasks = payload;
        },
        ADD_TASK(state, payload) {
            state.tasks = [
                ...state.tasks,
                ...payload
            ];
        },
        DELETE_TASK(state, id) {
            state.tasks = state.tasks.filter(t => t.id !== id);
        },
        EDIT_TASK(state, task) {
            state.tasks = state.tasks.map(t => {
                if (t.id === task.id) {
                    t = task;
                }
                return t;
            })
        },
        SET_CURRENT_TASKS(state, tasks) {
            state.currentTasks = tasks;
        },
        CHANGE_TASK_STATUS(state, id) {
            state.currentTasks = state.currentTasks.map(t => {
                /*if (t.id === id) {
                    t.is_completed = !t.is_completed;
                }*/
                return t;
            })
        }
    },
    actions: {
        async GET_TASKS({commit, dispatch, getters}) {
            try {
                commit('enableLoading');
                const { data } = await getTasks();
                commit('SET_TASKS', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async GET_CURRENT_TASKS({commit}, payload) {
            try {
                commit('enableLoading');
                const { data } = await getCurrentTasks(payload);
                commit('SET_CURRENT_TASKS', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async EDIT_TASK({commit, dispatch, getters}, {task, id}) {
            try {
                commit('enableLoading');
                const { data } = await editTask(task, id);
                commit('EDIT_TASK', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async CREATE_TASK({commit, dispatch, getters}, task) {
            try {
                commit('enableLoading');
                const { data } = await createTask(task);
                commit('ADD_TASK', data.data);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async DELETE_TASK({commit}, taskId) {
            try {
                commit('enableLoading');
                await deleteTask(taskId);
                commit('DELETE_TASK', taskId);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        },
        async CHANGE_TASK_STATUS({commit}, taskId) {
            try {
                commit('enableLoading');
                await changeTaskStatus(taskId);
                commit('CHANGE_TASK_STATUS', taskId);
            }
            catch (e) {
                throw e;
            } finally {
                commit('disableLoading');
            }
        }
    }
}
