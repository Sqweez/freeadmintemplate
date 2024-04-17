import axiosClient from '@/utils/axiosClient';

const baseURL = '/v3/revision';
const revisionRepository = () => ({
    async all() {
        return axiosClient.get(baseURL);
    },
    async create(payload) {
        return axiosClient.post(baseURL, payload);
    },
    async get(id) {
        return axiosClient.get(baseURL + '/' + id);
    },
    async upload(revisionId, fileId, data) {
        return axiosClient.post(
            baseURL + `/${revisionId}/process/${fileId}`,
            data,
        );
    },
    async table(revisionId, fileId = null) {
        const query = {};
        if (fileId) {
            query.file_id = fileId;
        }

        return axiosClient.get(
            baseURL +
                `/${revisionId}/table?${new URLSearchParams(query).toString()}`,
        );
    },
});

export default revisionRepository();
