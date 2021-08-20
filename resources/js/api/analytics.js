import axios from 'axios';

axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
};

export async function getBrandsAnalytics(store_id, date_start, date_finish) {
    return await axios.get(`/api/v2/brands/analytics?store_id=${store_id}&date_start=${date_start}&date_finish=${date_finish}`)
}

export async function getSaleAnalytics({start, finish}) {
    return await axios.get(`/api/analytics/sales?start=${start}&finish=${finish}`)
}

export async function getArrivalAnalytics({start, finish}) {
    return await axios.get(`/api/analytics/arrivals?start=${start}&finish=${finish}`)
}
