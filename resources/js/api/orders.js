import axios from 'axios';

export async function getOrders() {
    return await axios.get('/api/v2/orders')
}

export async function deleteOrder(order) {
    return await axios.delete(`/api/v2/orders/${order}`);
}

export async function acceptOrder(order) {
    return await axios.get(`/api/order/${order}/accept`)
}

export async function declineOrder(order) {
    return await axios.get(`/api/order/${order}/decline`)
}
