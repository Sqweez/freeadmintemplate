import axios from 'axios';

export async function createSupplier(supplier) {
    return await axios.post(`/api/v2/suppliers`, supplier);
}

export async function getSuppliers() {
    return await axios.get(`/api/v2/suppliers`);
}

export async function editSupplier(supplier, id) {
    return await axios.patch(`/api/v2/suppliers/${id}`, supplier);
}

export async function deleteSupplier(id) {
    return await axios.delete(`/api/v2/suppliers/${id}`);
}
