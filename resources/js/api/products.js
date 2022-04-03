import axios from 'axios'

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

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

export async function getProductsBySearch(search = "") {
    const { data } = await axios.get(`/api/v2/products/search?search=${search}`);
    return data;
}

export async function getMarginTypes() {
    const { data } = await axios.get('/api/v2/products/margin/types');
    return data;
}

export async function setMarginTypes(payload) {
    const { data } = await axios.post('/api/v2/products/margin/types', payload);
    return data;
}

export async function updateMarginTypes (types) {
    await axios.post('/api/v2/products/margin/types/set', { types })
}

export async function changeProductCount({product_id, store_id, increment}) {
    return await axios.get(`/api/v2/products/${product_id}/count?store_id=${store_id}&increment=${increment}`)
}

export async function getKaspiProductAnalytics(start, finish) {
    return await axios.get(`/api/v2/kaspi/analytics?start=${start}&finish=${finish}`)
}

export async function setProductTags (payload) {
    return await axios.post(`/api/v2/products/tags`, payload);
}
