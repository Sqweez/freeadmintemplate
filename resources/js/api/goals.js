import axios from 'axios'

export async function getGoals() {
    const response = await axios.get('/api/goals');
    return response.data;
}

export async function deleteGoal(id) {
    await axios.delete(`/api/goals/${id}`);
}

export async function editGoal(payload) {
    const response = await axios.patch(`/api/goals/${payload.id}`, payload);
    return response.data;
}

export async function createGoal(payload) {
    const response = await axios.post(`/api/goals`, payload);
    return response.data;
}
