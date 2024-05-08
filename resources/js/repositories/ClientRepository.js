import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/clients';

const ClientRepository = () => ({
    async get(params = {}) {
        return axiosClient.get(baseURL, {
            params,
        });
    },
});

export default ClientRepository();
