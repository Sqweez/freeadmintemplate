import axios from 'axios';
import store from '@/fitness/store';

const axiosClient = axios.create();

axiosClient.interceptors.request.use((config) => {
    config.baseURL = '/api/';
    config.headers = {
        Authorization: store.getters.TOKEN,
        accept: 'application/json',
    };
    return config;
});

export default axiosClient;
