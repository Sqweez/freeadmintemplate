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

export async function setImage(order, image) {
    const formData = new FormData();
    formData.append('file', image);
    return await axios.post(`/api/v2/orders/${order}/image`, formData);
}

export async function restoreOrder(order) {
    return await axios.get(`/api/v2/orders/restore/${order}`)
}
