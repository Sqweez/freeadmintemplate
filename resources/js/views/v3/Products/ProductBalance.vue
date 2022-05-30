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
                                <td>{{ (purchasePrices[store.id] || 0) | priceFilters }}</td>
                                <td>{{ (productPrices[store.id] || 0) | priceFilters }}</td>
                            </tr>
                            <tr v-if="isAdmin || IS_BOSS || IS_MARKETOLOG">
                                <td>
                                    Поступления:
                                </td>
                                <td>
                                    {{ arrivalPurchasePrice | priceFilters }}
                                </td>
                                <td>
                                    {{ arrivalProductPrice | priceFilters }}
                                </td>
                            </tr>
                            <tr v-if="isAdmin || IS_BOSS || IS_MARKETOLOG">
                                <td>
                                    Перемещения:
                                </td>
                                <td>
                                    {{ transferPurchasePrice | priceFilters }}
                                </td>
                                <td>
                                    {{ transferProductPrice | priceFilters }}
                                </td>
                            </tr>
                            <tr v-if="isAdmin || IS_BOSS || IS_MARKETOLOG">
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
                return this.$store.getters.stores.filter(s => {
                    if (this.IS_FRANCHISE) {
                        return s.id == this.$user.store_id;
                    }
                    return true;
                })
            },
            purchasePrices() {
                return this.$store.getters.PRODUCT_BALANCE.purchase_prices;
            },
            productPrices() {
                return this.$store.getters.PRODUCT_BALANCE.product_prices;
            },
            arrivalPurchasePrice() {
                return this.$store.getters.PRODUCT_BALANCE.totalArrivalsPurchasePrice;
            },
            arrivalProductPrice() {
                return this.$store.getters.PRODUCT_BALANCE.totalArrivalsProductPrice;
            },
            transferPurchasePrice() {
                return this.$store.getters.PRODUCT_BALANCE.totalTransfersPurchasePrice;
            },
            transferProductPrice() {
                return this.$store.getters.PRODUCT_BALANCE.totalTransfersProductPrice;
            },
            totalPurchasePrices() {
                return Object.values(this.purchasePrices).reduce((a, c) => {
                    return a + c;
                }, 0) + this.arrivalPurchasePrice + this.transferPurchasePrice;
            },
            totalProductPrices() {
                return Object.values(this.productPrices).reduce((a, c) => {
                    return a + c;
                }, 0) + this.arrivalProductPrice + this.transferProductPrice;
            }
        },
        async mounted() {
            await this.$store.dispatch('GET_PRODUCT_BALANCE');
        }
    }
</script>

<style scoped>

</style>
