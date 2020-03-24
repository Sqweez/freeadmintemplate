import axios from 'axios';

export async function getClients() {
    const response = await axios.get('/api/clients');
    return response.data.data;
}

export async function createClient(payload) {
    const response = await axios.post('/api/clients', payload);
    return response.data.data;
}

export async function editClient(payload) {
    const response = await axios.patch(`/api/clients/${payload.id}`, payload);
    return response.data.data;
}

export async function deleteClient(id) {
    await axios.delete(`/api/clients/${id}`)
}
