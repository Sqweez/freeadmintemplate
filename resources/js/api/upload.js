import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export default async function uploadFile(file, fileName = 'file', path = 'uploads') {
    let formData = new FormData();
    formData.append(fileName, file);
    formData.append('path', path);
    const config = {
        headers: {
            'content-type': 'multipart/form-data'
        }
    };
    return await axios.post('/api/v1/file/upload', formData, config);
}

export async function deleteFile(file) {
    const formData = new FormData;
    formData.append('file', file);
    return await axios.post('/api/v1/file/delete', formData);
}
