import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};
export async function generateThumb(image) {
    return await axios.post(`/api/v1/image/thumb`, {
        image
    })
}
