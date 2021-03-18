<template>
    <div>
        <v-card>
            <v-card-title>
                С этим товаром часто покупают
            </v-card-title>
            <v-card-text>
                <v-simple-table>
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>Наименование категории</th>
                            <th>Список товаров</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(category, idx) of categories" :key="idx">
                            <td>
                                {{ category.category_name }}
                            </td>
                            <td>
                                <ul>
                                    <li v-for="product of category.related_products">
                                        {{ product.product.product_name }} | {{ product.product.manufacturer.manufacturer_name }} | {{ product.product.category.category_name }} | {{ product.product.product_price | priceFilters}}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <div>
                                    <v-btn icon @click="currentCategory = category; modal = true;">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <RelatedProductsModal
            :state="modal"
            :category="currentCategory"
            @close="onClose"
        />
    </div>
</template>

<script>
    import axiosClient from "@/utils/axiosClient";
    import RelatedProductsModal from "@/components/Modal/RelatedProductsModal";

    export default {
        components: {RelatedProductsModal},
        data: () => ({
            categories: [],
            modal: false,
            currentCategory: null,
        }),
        async created() {
            await this.$store.dispatch('GET_PRODUCTS_v2');
            const response = await axiosClient.get('v2/products/related');
            this.categories = response.data.data;
        },
        methods: {
            onClose(item) {
                if (item !== null) {
                    this.categories = this.categories.map(category => {
                        if (category.id === item.id) {
                            category = item;
                        }
                        return category;
                    })
                }
                this.modal = false;
                this.currentCategory = null;
            }
        },
        computed: {

        }
    }
</script>

<style scoped>

</style>
