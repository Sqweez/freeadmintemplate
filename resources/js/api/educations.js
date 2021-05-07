import axios from 'axios';

export async function getEducations() {
    return await axios.get('/api/v2/educations');
}

export async function createEducation(education) {
    return await axios.post('/api/v2/educations', education);
}

export async function deleteEducation(id) {
    return await axios.delete(`/api/v2/educations/${id}`);
}

export async function editEducation(education, id) {
    return await axios.patch(`/api/v2/educations/${id}`, education);
}
