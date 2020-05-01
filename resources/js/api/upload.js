import axios from 'axios';

export default async function uploadFile(file, fileName = 'file', path = 'uploads') {
    let formData = new FormData;
    formData.append(fileName, file);
    formData.append('path', path);
    const config = {
        headers: {
            'content-type': 'multipart/form-data'
        }
    };
    return await axios.post('/api/upload', formData, config);
}

export async function deleteFile(file) {
    const formData = new FormData;
    formData.append('file', file);
    return await axios.post('/api/delete', formData);
}
