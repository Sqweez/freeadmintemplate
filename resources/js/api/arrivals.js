import axios from 'axios';

export async function getArrivals(payload) {
    const { data } = await axios.get(`/api/arrivals?is_completed=${+payload}`);
    return data;
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
