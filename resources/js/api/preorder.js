import axios from 'axios';

export async function createPreOrder(payload) {
    return await axios.post(`/api/v2/preorder`, payload);
}

export async function getPreOrders() {
    return await axios.get(`/api/v2/preorder`);
}

export async function cancelPreOrder(id) {
    return await axios.patch(`/api/v2/preorder/cancel/${id}`)
}

export async function getPreOrdersReports({start, finish}) {
    return await axios.get(`/api/v2/preorder/report?start=${start}&finish=${finish}`);
}
