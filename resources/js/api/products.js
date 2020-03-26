import axios from 'axios'

export async function getProducts() {
    const response = await axios.get(`/api/products`);
    return response;
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
    await axios.delete(`/api/products/${payload.id}`, payload)
}

export async function addProductRange(payload) {
    const response  = await axios.post(`/api/products/range`, payload);
    return response.data.data;
}

export async function addProductBatch(payload) {
    const response  = await axios.post(`/api/products/batch`, payload);
    return response.data.data;
}

