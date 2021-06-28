import axios from 'axios';

export async function getTasks() {
    return await axios.get('/api/v2/tasks');
}

export async function getCurrentTasks(store_id) {
    return await axios.get(`/api/v2/tasks/current?store_id=${store_id}`);
}

export async function createTask(task) {
    return await axios.post('/api/v2/tasks', task);
}

export async function deleteTask(id) {
    return await axios.delete(`/api/v2/tasks/${id}`);
}

export async function editTask(task, id) {
    return await axios.patch(`/api/v2/tasks/${id}`, task);
}

export async function changeTaskStatus(id) {
    return await axios.patch(`/api/v2/tasks/status/${id}`);
}
