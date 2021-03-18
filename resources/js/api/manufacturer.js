import axios from "axios";

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getManufacturers() {
    const response = await axios.get('/api/manufacturers');
    return response.data.data;
}

export async function createManufacturer(payload) {
    const response = await axios.post(`/api/manufacturers`, payload);
    return response.data;
}

export async function deleteManufacturers(id) {
    await axios.delete(`/api/manufacturers/${id}`);
}

export async function editManufacturer(payload) {
    const response = await axios.patch(`/api/manufacturers/${payload.id}`, payload);
    return response.data;
}
