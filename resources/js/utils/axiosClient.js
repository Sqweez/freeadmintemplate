import axios from 'axios';
import store from "@/store";
const axiosClient = axios.create();

axiosClient.interceptors.request.use((config) => {
    config.baseURL = '/api/';
    config.headers = {
        Authorization: store.getters.TOKEN,
        user_id: store.getters.USER.id,
        store_id: store.getters.USER.store_id,
    };
    return config;
});

export default axiosClient;
