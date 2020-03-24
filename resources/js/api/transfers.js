import axios from 'axios';

export async function getTransfers({mode}) {
    const response = await axios.get(`/api/transfers?mode=${mode}`);
    return response.data.data;
}

export async function createTransfer(payload) {
    const response = await axios.post('/api/transfers', payload);
    return response.data;
}

export async function getTransferInfo(id) {
    const response = await axios.get(`/api/transfers/${id}`);
    return response.data.data;
}

export async function acceptTransfer(payload, id) {
    return await axios.post(`/api/transfers/${id}/accept`, payload);
}

export async function declineTransfer(id) {
    return await axios.post(`/api/transfers/${id}/cancel`)
}
