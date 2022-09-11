import _ from 'lodash';

export default {
    data: () => ({
        waitingQuantities: false,
    }),
    methods: {
        getPrice(product, store_id = null) {
            const storeId = store_id === null ? this.storeFilter : store_id;
            const item = product.prices.find(p => p.store_id === storeId);
            return item ? item.price : product.product_price;
        },
        async getProductQuantities(value) {
            if (!this.waitingQuantities) {
                const debouncedFn = _.debounce(() => {
                    try {
                        this.$store.dispatch('GET_PRODUCTS_QUANTITIES', value);
                    } catch (e) {
                        console.log(e.response);
                    }
                    this.waitingQuantities = false;
                }, 150)

                debouncedFn();
            }

            this.waitingQuantities = true;
        }
    },
    watch: {
        storeFilter: {
            handler: async function (value) {
                if (value !== null) {
                    await this.getProductQuantities(value);
                }
            },
            immediate: true
        }
    },
}
