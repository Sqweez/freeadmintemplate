import axios from 'axios'

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getUsers() {
    return await axios.get('/api/users');
}

export async function getUserRoles() {
    return await axios.get('/api/users/roles');
}

export async function createUser(user) {
    return await axios.post('/api/users', user);
}

export async function editUser(user) {
    return await axios.patch(`/api/users/${user.id}`, user);
}

export async function deleteUser(id) {
    return await axios.delete(`/api/users/${id}`);
}
