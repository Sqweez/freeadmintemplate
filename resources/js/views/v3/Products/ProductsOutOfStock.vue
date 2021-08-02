<template>
    <div>
        <v-card>
            <v-card-title>
                Заканчивающиеся товары
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Склад"
                    :items="stores"
                    item-value="id"
                    item-text="name"
                    v-model="storeId"
                />
                <v-btn block color="success" @click="getOutOfStock">
                    Получить отчет
                </v-btn>
                <div v-if="quantitiesLoaded">
                    <v-select
                        label="Производитель"
                        :items="manufacturers"
                        v-model="manufacturerId"
                        item-value="id"
                        item-text="manufacturer_name"
                    />
                    <v-data-table
                        :search="searchQuery"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :headers="headers"
                        :page.sync="pagination.page"
                        :items="products"
                        @page-count="pageCount = $event"
                        :items-per-page="10"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                        <template v-slot:item.attributes="{ item }">
                            <ul>
                                <li v-for="(attr, index) of item.attributes" :key="index">
                                    {{ attr.attribute_name }}: {{ attr.attribute_value }}
                                </li>
                            </ul>
                        </template>
                        <template v-slot:item.product_price="{ item }">
                            <span>{{ item.product_price | priceFilters }}</span>
                        </template>
                        <template v-slot:item.category="{ item }">
                            <span>{{ item.category.category_name }}</span>
                        </template>
                        <template v-slot:item.manufacturer="{ item }">
                            <span>{{ item.manufacturer.manufacturer_name }}</span>
                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </div>

            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import ACTIONS from "@/store/actions";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import axios from 'axios';

    export default {
        data: () => ({
            storeId: 1,
            pagination: {
                ascending: true,
                rowsPerPage: 10,
                page: 1
            },
            headers: [
                {
                    value: 'product_name',
                    text: 'Наименование',
                    sortable: false,
                },
                {
                    value: 'quantity',
                    text: 'Остаток'
                },
                {
                    value: 'product_price',
                    text: 'Стоимость'
                },
                {
                    value: 'product_barcode',
                    text: 'Штрих-код',
                    align: ' d-none'
                },
                {
                    value: 'attributes',
                    text: 'Атрибуты'
                },
                {
                    value: 'manufacturer',
                    text: 'Производитель'
                },
                {
                    value: 'manufacturer.manufacturer_name',
                    text: 'Название производителя',
                },
                {
                    value: 'category',
                    text: 'Категория'
                }
            ],
            quantitiesLoaded: false,
            quantities: [],
            manufacturerId: -1,
        }),
        mixins: [product, product_search],
        methods: {
            async getOutOfStock() {
                this.$loading.enable();
                this.quantitiesLoaded = false;
                const { data } = await axios.get(`/api/v2/products/stock/out?store_id=${this.storeId}`);
                this.quantities = data;
                this.quantitiesLoaded = true;
                this.$loading.disable();
            }
        },
        computed: {
            stores() {
                return this.$store.getters.stores;
            },
            _products() {
                let products = this.$store.getters.PRODUCTS_v2;
                return products.filter(p => {
                    return this.quantities.find(q => q.product_id === p.id);
                });
            },
            products() {
                let products = this.$store.getters.PRODUCTS_v2;
                return products.filter(p => {
                    return this.quantities.find(q => q.product_id === p.id);
                }).map(p => {
                    p.quantity = this.quantities.find(q => q.product_id === p.id).quantity;
                    return p;
                }).filter(p => {
                    if (this.manufacturerId === -1) {
                        return p;
                    } else {
                        return p.manufacturer.id == this.manufacturerId;
                    }
                })
            },
            manufacturers() {
                const manufacturersId = this._products.map(p => p.manufacturer.id);
                return [
                    {
                        id: -1,
                        manufacturer_name: 'Все'
                    },
                    ...this.$store.getters.manufacturers.filter(m => {
                        return manufacturersId.find(a => a == m.id);
                    })
                ];
            },
        },
        async mounted() {
            this.$loading.enable();
            await this.$store.dispatch('GET_PRODUCTS_v2');
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            this.$loading.disable();
        }
    }
</script>

<style scoped>

</style>
