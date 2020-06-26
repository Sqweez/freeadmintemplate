import axios from 'axios'

export async function getProducts(store_id) {
    const getParams = store_id ? `?store_id=${store_id}` : ``;
    return await axios.get(`/api/products${getParams}`);
}

export async function createProduct(payload) {
    const response = await axios.post(`/api/products`, payload);
    return response.data.data;
}

export async function editProduct(payload) {
    const response = await axios.patch(`/api/products/${payload.id}`, payload);
    return response.data.data;
}

export async function deleteProduct(payload) {
    await axios.delete(`/api/products/${payload}`)
}

export async function addProductRange(payload) {
    const response  = await axios.post(`/api/products/range`, payload);
    return response.data.data;
}

export async function addProductBatch(payload) {
    const response  = await axios.post(`/api/products/batch`, payload);
    return response.data.data;
}

export async function getMainProducts() {
    const response = await axios.get(`/api/products/main`);
    return response.data;
}
