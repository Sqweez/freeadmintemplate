import axios from 'axios';

export default class {
    async generate () {
        const { data } = await axios.get('/api/v2/products/generate-barcode');
        return data;
    }
}
