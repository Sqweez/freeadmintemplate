import axiosClient from '@/utils/axiosClient';

const BASE_URL = '/reports';

const ReportRepository = () => ({
    async getExcelReport(params = {}) {
        return axiosClient.get(BASE_URL + '/excel', {
            params,
        });
    },
});

export default ReportRepository();
