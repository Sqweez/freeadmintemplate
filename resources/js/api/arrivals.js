import axios from 'axios';

export async function getArrivals(payload) {
    const { data } = await axios.get(`/api/arrivals?is_completed=${+payload}`);
    return data;
}

export async function getArrival(id) {
    const { data } = await axios.get(`/api/arrivals/${id}`);
    return data;
}

export async function cancelArrival(id) {
    return await axios.get(`/api/arrivals/cancel/${id}`);
}

export async function createArrival(payload) {
    const { data } = await axios.post('/api/arrivals', payload);
    return data;
}

export async function createBatch(payload) {
    const { data } = await axios.post('/api/arrivals/complete', payload);
    return data;
}

export async function deleteArrival(id) {
    await axios.delete(`/api/arrivals/${id}`);
}

export async function changeArrival(id, payload) {
    await axios.post(`/api/arrivals/change/${id}`, payload);
}
