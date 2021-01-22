import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getCategories() {
    const response = await axios.get(`/api/category`);
    return response.data.data;
}

export async function deleteCategory(id) {
    await axios.delete(`/api/category/${id}`)
}

export async function createCategory(payload) {
    const response  = await axios.post(`/api/category`, payload);
    return response.data.data;
}

export async function editCategory(payload) {
    const response = await axios.patch(`/api/category/${payload.id}`, payload);
    return response.data.data;
}
