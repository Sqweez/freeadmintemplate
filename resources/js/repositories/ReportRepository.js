import axiosClient from '@/utils/axiosClient';

const BASE_URL = '/reports';

const ReportRepository = () => ({
    async getExcelReport(params = {}) {
        return axiosClient.get(BASE_URL + '/excel', {
            params,
        });
    },
    async getExcelEliteClientReport(params = {}) {
        return axiosClient.get(BASE_URL + '/elite/excel', { params });
    },
});

export default ReportRepository();
