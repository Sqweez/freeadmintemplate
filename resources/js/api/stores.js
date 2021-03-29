import axios from 'axios';
import store from "@/store";

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getStores(store_id) {
    const getParams = store_id ? `?store_id=${store_id}` : ``;
    const response = await axios.get(`/api/stores${getParams}`);
    return response.data.data;
}

export async function getStoreTypes() {
    const response = await axios.get('/api/stores/types');
    return response.data;
}

export async function createStore(payload) {
    const response = await axios.post('/api/stores', payload);
    return response.data.data;
}

export async function deleteStore(id) {
    await axios.delete(`/api/stores/${id}`);
}

export async function editStore(payload) {
    const response = await axios.patch(`/api/stores/${payload.id}`, payload);
    return response.data.data;
}

export async function getCities() {
    const { data } = await axios.get('/api/v2/cities');
    return data;
}

export async function addCompanionBalance({store_id, sum}) {
    return await axios.post(`/api/stores/balance/${store_id}`, {sum});
}
