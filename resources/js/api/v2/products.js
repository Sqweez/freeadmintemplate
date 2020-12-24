import axios from 'axios'

export async function getProducts() {
    return await axios.get('/api/v2/products');
}

export async function getProductsQuantity(store_id =  1) {
    return await axios.get(`/api/v2/products/quantity/${store_id}`);
}

export async function getProduct(product_id) {
    return await axios.get(`/api/v2/products/${product_id}`);
}

export async function createProduct(product) {
    return await axios.post(`/api/v2/products`, product);
}

export async function deleteProduct(id) {
    return await axios.delete(`/api/v2/products/${id}`);
}

export async function editProduct({product, id}) {
    return await axios.patch(`/api/v2/products/${id}`, product);
}

export async function addProductQuantity(id, batch) {
    return await axios.post(`/api/v2/products/${id}/quantity`, batch);
}

export async function createProductSku(id, product) {
    return await axios.post(`/api/v2/products/${id}/sku`, product);
}

export async function updateProductSku(id, product) {
    return await axios.patch(`/api/v2/products/${id}/sku`, product);
}
/*export async function createProduct(payload) {
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

export async function changeProductCount({product_id, store_id, increment}) {
    return await axios.get(`/api/v2/products/${product_id}/count?store_id=${store_id}&increment=${increment}`)
}*/
