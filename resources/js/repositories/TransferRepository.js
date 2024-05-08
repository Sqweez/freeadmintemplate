import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/transfers';

const TransferRepository = () => ({
    async get(params = {}) {
        return axiosClient.get(baseURL, {
            params,
        });
    },
});

export default TransferRepository();
