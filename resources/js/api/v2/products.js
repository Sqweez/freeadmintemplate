import axios from 'axios'

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

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

