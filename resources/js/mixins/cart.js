export default {
    data: () => ({
        storeFilter: null,
        manufacturerId: -1,
        categoryId: -1,
        hideNotInStock: true,
    }),
    watch: {
        stores() {
            this.storeFilter = this.stores[0].id;
        },
    },
    methods: {
        increaseCartCount(index) {
            const item = this.cart[index];
            const currentCount = Math.min(this.cart[index].quantity, this.cart.filter(c => c.id === item.id).reduce((a, c) => {
                return a + +c.count
            }, 0));


            if (item.quantity - currentCount === 0) {
                this.$toast.error('Недостаточно товара');
                return;
            }

            this.$set(this.cart[index], 'count', this.cart[index].count + 1);
        },
        decreaseCartCount(index) {
            this.$set(this.cart[index], 'count', Math.max(1, this.cart[index].count - 1))
        },
        addToCart(item, merge = false) {
            if (item.quantity - this.getCartCount(item.id) === 0) {
                this.$toast.error('Недостаточно товара');
                return;
            }

            const index = this.cart.findIndex(c => c.id === item.id);
            if (index === -1 || merge) {
                this.cart.push({...item, count: 1, product_price: item.product_price, discount: 0, uuid: Math.random()});
            } else {
                this.increaseCartCount(index);
            }
        },
        getCartCount(id) {
            return this.cart
                .filter(c => c.id === id)
                .reduce((a, c) => {
                    return a + c.count;
                }, 0);
        },
        deleteFromCart(index) {
            this.cart.splice(index, 1);
        },
        updateCount(e, item) {
            this.$nextTick(() => {
                const index = this.cart.findIndex(c => c.uuid === item.uuid);
                const currentCount = this.cart.filter(c => c.id === item.id && c.uuid !== item.uuid)
                    .reduce((a, c) => {
                        return a + +c.count
                    }, 0);

                const quantity = this.cart[index].quantity - currentCount;
                this.$set(this.cart[index], 'count', Math.min(quantity, Math.max(+e, 0)));
            })
        },
        updateDiscount(item) {
            this.$nextTick(() => {
                const index = this.cart.findIndex(c => c.uuid === item.uuid);
                this.$set(this.cart[index], 'discount', Math.max(0, Math.min(100, item.discount)));
            });
        },
    },
    computed: {
        productsWithoutFilters() {
            return this.$store.getters.PRODUCTS_v2;
        },
        products() {
            let products = this.$store.getters.PRODUCTS_v2;
            if (this.manufacturerId !== -1) {
                products = products.filter(product => product.manufacturer.id === this.manufacturerId);
            }
            if (this.hideNotInStock) {
                products = products.filter(product => product.quantity > 0);
            }
            if (this.categoryId !== -1) {
                products = products.filter(product => product.category.id === this.categoryId);
            }
            return products;
        },
        stores() {
            return this.$store.getters.stores;
        },
        manufacturers() {
            return [
                {
                    id: -1,
                    manufacturer_name: 'Все'
                }, ...this.$store.getters.manufacturers];
        },
        categories() {
            return [
                {
                    id: -1,
                    name: 'Все'
                }, ...this.$store.getters.categories
            ];
        },
        emptyCart() {
            return !!!this.cart.length;
        },
        cartCount() {
            return this.cart
                .map(c => c.count)
                .reduce((a, c) => {
                    return a + c;
                }, 0) + (this.certificate ? 1 : 0);
        },
        subtotal() {
            return this.cart.reduce((a, c) => {
                return (c.product_price * c.count) + a;
            }, 0) + (this.certificate ? this.certificate.amount : 0);
        },
        user() {
            return this.$store.getters.USER;
        },
    },
}
