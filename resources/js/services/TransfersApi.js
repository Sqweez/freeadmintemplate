import axiosClient from '@/utils/axiosClient';

class TransfersApi {
    retrieveMatrixTransfer(childStoreId, parentStoreId) {
        return axiosClient.get(
            `v2/matrix/${childStoreId}/transfer/${parentStoreId}`,
        );
    }
}

export default new TransfersApi();
