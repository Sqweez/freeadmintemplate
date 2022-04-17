import axios from 'axios';
import store from "@/store";

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function makeSale(payload) {
    const response = await axios.post('/api/sales', payload);
    return response.data;
}

export async function getReports({start, finish, user_id = null, is_supplier = null, store_id = null, manufacturer_id = null}) {
    let query = `?start=${start}&finish=${finish}`;
    if (user_id) {
        query += `&user_id=${user_id}`
    }
    if (is_supplier) {
        query += '&is_supplier=1'
    }
    if (store_id) {
        query += `&store_id=${store_id}`
    }
    if (manufacturer_id) {
        query += `&manufacturer_id=${manufacturer_id}`;
    }
    const response = await axios.get(`/api/reports${query}`);
    return response.data.data;
}

export async function cancelSale(payload, id) {
    return await axios.post(`/api/sales/${id}/cancel`, payload);
}

export async function getStoreReports({date_filter, store_id, role}) {
    return await axios.get(`/api/reports/total?date_filter=${date_filter}&store_id=${store_id}&role=${role}`)
}

export async function getPlanReports() {
    return await axios.get(`/api/reports/plan`)
}

export async function updateSale(payload) {
    const { data } = await axios.patch(`/api/reports/${payload.id}`, payload);
    return data.data;
}

export async function getBrandsMotivation() {
    const { data } = await axios.get('/api/sales/brands/motivation');
    return data;
}

export async function createBrandsMotivation(motivations) {
    const { data } = await axios.post(`/api/v2/brands/motivation`, {
        motivations
    });
    return data;
}

export async function getBrandsMotivationPlans() {
    const { data } = await axios.get('/api/v2/brands/motivation');
    return data;
}

export async function getSaleTypes() {
    const { data } = await axios.get(`/api/sales/types`);
    return data;
}

export async function editSaleList(res) {
    const { data } = await axios.post(`/api/sales/${res.id}/list/edit`, res);
    return data;
}
