import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/work-shift';

const WorkShiftRepository = () => ({
    async open() {
        return axiosClient.get(`${baseURL}/open`);
    },
    async close(shiftId) {
        return axiosClient.get(`${baseURL}/${shiftId}/close`);
    },
    async list(params) {
        return axiosClient.get(baseURL, {
            params,
        });
    },
});

export default WorkShiftRepository();
