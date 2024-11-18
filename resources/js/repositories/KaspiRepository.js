import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/kaspi/orders';

const KaspiRepository = () => ({
    async getOrders(params = {}) {
        return axiosClient.get(baseURL, {
            params,
        });
    },
    async getOrder(id) {
        return axiosClient.get(`${baseURL}/${id}`);
    },
});

export default KaspiRepository();
