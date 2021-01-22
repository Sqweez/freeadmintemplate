import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getAttributes() {
    const response = await axios.get(`/api/attributes`);
    return response.data;
}

export async function createAttribute(payload) {
    const response = await axios.post('/api/attributes', payload);
    return response.data;
}

export async function editAttribute(payload) {
    const response = await axios.patch(`/api/attributes/${payload.id}`, payload);
    return response.data;
}

export async function deleteAttribute(id) {
    await axios.delete(`/api/attributes/${id}`);
}
