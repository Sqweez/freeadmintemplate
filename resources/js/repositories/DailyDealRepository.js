import axiosClient from '@/utils/axiosClient';

const baseURL = 'opt/admin/v1/daily-deal';

const DailyDealRepository = () => ({
    async get() {
        return axiosClient.get(baseURL);
    },
    async create(payload) {
        return axiosClient.post(baseURL, payload);
    },
    async update(dealId, payload) {
        return axiosClient.patch(baseURL + `/${dealId}`, payload);
    },
    async delete(dealId) {
        return axiosClient.delete(baseURL + `/${dealId}`);
    },
});

export default DailyDealRepository();
