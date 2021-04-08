<template>
    <div>
        <v-card>
            <v-card-title>
                Баланс товаров
            </v-card-title>
            <v-card-text>
                <v-simple-table>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>Склад</th>
                                <th>Закупочная стоимость</th>
                                <th>Продажная стоимость</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="store of stores" :key="`store-id-${store.id}`">
                                <td>{{ store.name }}</td>
                                <td>{{ purchasePrices[store.id] | priceFilters }}</td>
                                <td>{{ productPrices[store.id] | priceFilters }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Итого:</b>
                                </td>
                                <td>{{ totalPurchasePrices | priceFilters }}</td>
                                <td>{{ totalProductPrices | priceFilters }}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    export default {
        data: () => ({}),
        methods: {},
        computed: {
            stores() {
                return this.$store.getters.stores;
            },
            purchasePrices() {
                return this.$store.getters.PRODUCT_BALANCE.purchase_prices;
            },
            productPrices() {
                return this.$store.getters.PRODUCT_BALANCE.product_prices;
            },
            totalPurchasePrices() {
                return Object.values(this.purchasePrices).reduce((a, c) => {
                    return a + c;
                }, 0);
            },
            totalProductPrices() {
                return Object.values(this.productPrices).reduce((a, c) => {
                    return a + c;
                }, 0);
            }
        },
        async mounted() {
            await this.$store.dispatch('GET_PRODUCT_BALANCE');
        }
    }
</script>

<style scoped>

</style>
