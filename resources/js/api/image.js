import axios from 'axios';

export async function generateThumb(image) {
    return await axios.post(`/api/v1/image/thumb`, {
        image
    })
}
