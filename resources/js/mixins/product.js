import _ from 'lodash';

export default {
    data: () => ({
        waitingQuantities: false,
    }),
    methods: {
        getPrice(product) {
            const item = product.prices.find(p => p.store_id == this.storeFilter);
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
