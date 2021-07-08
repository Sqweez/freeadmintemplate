import axios from 'axios';

export async function createPreOrder(payload) {
    return await axios.post(`/api/v2/preorder`, payload);
}

export async function getPreOrders(user_id = null) {
    let query = '';
    if (user_id) {
        query += `?user_id=${user_id}`;
    }
    return await axios.get(`/api/v2/preorder${query}`);
}

export async function cancelPreOrder(id) {
    return await axios.patch(`/api/v2/preorder/cancel/${id}`)
}

export async function getPreOrdersReports({start, finish}, user_id = null) {

    return await axios.get(`/api/v2/preorder/report?start=${start}&finish=${finish}&user_id=${user_id}`);
}
