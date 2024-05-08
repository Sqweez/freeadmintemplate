import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/arrivals';

const ArrivalRepository = () => ({
    async get(params = {}) {
        return axiosClient.get(baseURL, {
            params,
        });
    },
});

export default ArrivalRepository();
