import axios from 'axios';

export default class {
    async generate (id) {
        const { data } = await axios.get(`/api/v2/products/generate-barcode/${id}`);
        return data;
    }
}
