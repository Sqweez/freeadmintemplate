import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/clients';

const ClientRepository = () => ({
    async get(params = {}) {
        return axiosClient.get(baseURL, {
            params,
        });
    },
    async export(params = {}) {
        return axiosClient.get(`${baseURL}/export`, {
            params,
            responseType: 'blob',
        });
    },
});

export default ClientRepository();
