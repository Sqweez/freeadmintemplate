import axios from 'axios';

export async function makeSale(payload) {
    const response = await axios.post('/api/sales', payload);
    return response.data;
}

export async function getReports({start, finish}) {
    const response = await axios.get(`/api/reports?start=${start}&finish=${finish}`);
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

