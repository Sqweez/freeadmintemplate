import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function makeSale(payload) {
    const response = await axios.post('/api/sales', payload);
    return response.data;
}

export async function getReports({start, finish, user_id = null, is_supplier = null}) {
    let query = `?start=${start}&finish=${finish}`;
    if (user_id) {
        query += `&user_id=${user_id}`
    }
    if (is_supplier) {
        query += '&is_supplier=1'
    }
    const response = await axios.get(`/api/reports${query}`);
    return response.data.data;
}

export async function cancelSale(payload, id) {
    return await axios.post(`/api/sales/${id}/cancel`, payload);
}

export async function getStoreReports(date_filter) {
    return await axios.get(`/api/reports/total?date_filter=${date_filter}`)
}

export async function getPlanReports() {
    return await axios.get(`/api/reports/plan`)
}

export async function updateSale(payload) {
    const { data } = await axios.patch(`/api/reports/${payload.id}`, payload);
    return data.data;
}

