import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

axios.defaults.withCredentials = true;

export async function setCSRFToken() {
    return await axios.get('/sanctum/csrf-cookie');
}

export async function login(user = {}) {
    return await axios.post('/api/login', user);
}

export async function auth(token) {
    return await axios.post('/api/auth', token);
}
