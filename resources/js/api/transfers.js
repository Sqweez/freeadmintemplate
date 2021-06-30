import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getTransfers({mode, partners = false}) {
    let url = `/api/transfers?mode=${mode}`;
    if (partners) {
        url += `&partners=true`
    }
    const response = await axios.get(url);
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

export async function acceptTransferFromSeller(payload, id) {
    return await axios.post(`/api/transfers/${id}/confirm`, payload);
}

export async function declineTransfer(id) {
    return await axios.post(`/api/transfers/${id}/cancel`)
}

export async function editTransferStore({id, child_store_id}) {
    return await axios.patch(`/api/transfers/${id}`, {
        child_store_id
    })
}
