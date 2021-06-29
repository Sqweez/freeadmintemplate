import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getBrandsAnalytics(store_id, date_start, date_finish) {
    return await axios.get(`/api/v2/brands/analytics?store_id=${store_id}&date_start=${date_start}&date_finish=${date_finish}`)
}
