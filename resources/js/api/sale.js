import axios from 'axios';

export async function makeSale(payload) {
    const response = await axios.post('/api/sales', payload);
    return response.data;
}

export async function getReports() {
    const response = await axios.get('/api/reports');
    return response.data.data;
}

export async function cancelSale(payload, id) {
    await axios.post(`/api/sales/${id}/cancel`, payload);
}

export async function getStoreReports(date_filter) {
    return await axios.get(`/api/reports/total?date_filter=${date_filter}`)
}
