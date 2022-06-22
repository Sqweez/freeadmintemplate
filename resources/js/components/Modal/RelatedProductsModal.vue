<template>
    <v-dialog
        persistent
        v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Связанные товары</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('close', null)">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th>#</th>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
                        <tr v-for="(item, index) of cart">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>
                                {{ item.product_price | priceFilters }}
                            </td>
                            <td>
                                <v-btn icon color="error" @click="deleteList(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-text-field
                    class="mt-2"
                    v-on:input="searchInput"
                    v-model="searchValue"
                    solo
                    clearable
                    label="Поиск товара"
                    single-line
                    hide-details
                ></v-text-field>
                <v-data-table
                    class="background-tooman-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :search="searchQuery"
                    loading-text="Идет загрузка товаров..."
                    :items="products"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.product_name="{item}">
                        <v-list flat>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.product_name }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn depressed icon color="success" @click="addToList(item)">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('close', null)">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" v-if="cart.length" @click="saveCategory">
                    Сохранить
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import product_search from "@/mixins/product_search";
    import axiosClient from "@/utils/axiosClient";

    export default {
        data: () => ({
            headers: [
                {
                    text: 'Наименование',
                    value: 'product_name',
                    sortable: false,
                    align: ' fz-18'
                },
                {
                    value: 'manufacturer.manufacturer_name',
                    text: 'Производитель',
                    align: ' d-none'
                },
                {
                    text: 'Стоимость',
                    value: 'product_price'
                },
                {
                    text: 'Добавить',
                    value: 'actions'
                },
                {
                    text: 'Штрих-код',
                    value: 'product_barcode',
                    align: ' d-none'
                }
            ],
            cart: [],
        }),
        methods: {
            addToList(item) {
                const findIndex = this.cart.findIndex(p => p.product_id === item.product_id);
                if (findIndex === -1) {
                    this.cart.push(item);
                }
            },
            deleteList(key) {
                this.cart.splice(key, 1);
            },
            async saveCategory() {
                const products = this.cart.map(c => c.product_id);
                const category = {
                    category_id: this.category.id,
                    products: products
                };

                this.$loading.enable();

                const { data } = await axiosClient.post('v2/products/related', category);

                this.$loading.disable();
                this.$emit('close', data.data);
            }
        },
        mixins: [product_search],
        computed: {
            products() {
                return this.$store.getters.MAIN_PRODUCTS_v2;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            category: {
                type: Object,
                default: () => ({}),
            }
        },
        watch: {
            state(value) {
                if (value) {
                    const product_ids = this.category.related_products.map(c => c.product_id);
                    this.cart = product_ids.map(p => {
                        return this.products.find(_p => _p.product_id === p);
                    })
                } else {
                    this.cart = [];
                }
            }
        }
    }
</script>

<style scoped>

</style>
