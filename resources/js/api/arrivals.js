import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

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

export async function editArrival({id, store_id, payment_cost, arrived_at, comment}) {
    return await axios.patch(`/api/arrivals/${id}`, {
        store_id,
        payment_cost,
        arrived_at,
        comment
    })
}
