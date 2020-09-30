export default {
    methods: {
        getPrice(product) {
            const item = product.prices.find(p => p.store_id == this.storeFilter);
            return item ? item.price : product.product_price;
        },
    }
}
