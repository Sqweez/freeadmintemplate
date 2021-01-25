import axios from 'axios';
import store from "@/store";
const axiosClient = axios.create();

axiosClient.interceptors.request.use((config) => {
    config.baseURL = '/api/';
    config.headers = {
        Authorization: store.getters.TOKEN
    };
    return config;
});

export default axiosClient;
