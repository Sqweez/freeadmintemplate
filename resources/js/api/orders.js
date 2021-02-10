import axios from 'axios';

export async function getOrders() {
    return await axios.get('/api/v2/orders/')
}
