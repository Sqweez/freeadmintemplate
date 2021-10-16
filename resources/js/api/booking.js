import axios from 'axios';

export async function createBooking(payload) {
    return await axios.post(`/api/v2/booking`, payload);
}

export async function getBookings(payload) {
    return await axios.get(`/api/v2/booking`);
}

export async function deleteBooking(id) {
    return await axios.delete(`/api/v2/booking/${id}`);
}

export async function createBookingSale(sale) {
    return await axios.post(`/api/v2/booking/sale`, sale)
}

