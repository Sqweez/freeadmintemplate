<template>
    <v-card>
        <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
            Рейтинг товаров
        </v-card-title>
        <v-card-text>
            <v-flex class="d-flex">
                <v-select
                    class="mr-4"
                    :items="stores"
                    item-value="id"
                    item-text="name"
                    v-model="store_id"
                    label="Магазин"
                    @change="getMVPProducts"
                ></v-select>
                <v-select
                    class="ml-4"
                    label="Временной промежуток"
                    :items="times"
                    item-text="name"
                    v-model="timeFilter"
                    item-value="key"
                    @change="getMVPProducts"
                ></v-select>
            </v-flex>
            <h4 class="color-text--green">Самые продаваемые товары:</h4>
            <v-simple-table v-slot:default>
                <thead>
                <tr class="stat-table">
                    <th>Категория</th>
                    <th>Товары лучшие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(category, key) of MVP_CATEGORY_PRODUCTS" :key="Math.random()" class="stat-table">
                    <td>
                        {{ getCategoryById(key) }}
                    </td>
                    <td>
                        <ol>
                            <li v-for="item of category">
                                {{ item.product.product_name }}
                                {{ item.product.manufacturer }}
                                {{ item.product.attributes.map(a => a.attribute_value).join(' ') }} |
                                <b>Количество:  {{ item.count }} шт.</b>
                            </li>
                        </ol>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
            <h4 class="color-text--red">Самые непродаваемые товары:</h4>
            <v-simple-table v-slot:default>
                <thead>
                <tr class="stat-table">
                    <th>Категория</th>
                    <th>Товары лучшие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(category, key) of WORST_CATEGORY_PRODUCTS" :key="Math.random()" class="stat-table">
                    <td>
                        {{ getCategoryById(key) }}
                    </td>
                    <td>
                        <ol>
                            <li v-for="item of category">
                                {{ item.product.product_name }}
                                {{ item.product.manufacturer }}
                                {{ item.product.attributes.map(a => a.attribute_value).join(' ') }} |
                                <b>Количество:  {{ item.count }} шт.</b>
                            </li>
                        </ol>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </v-card-text>
    </v-card>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import { mapGetters } from 'vuex';

    export default {
        data: () => ({
            store_id: null,
            loading: true,
            timeFilter: null,
            times: [
                {
                    key: 'last_30_days',
                    name: 'Последние 30 дней'
                },
                {
                    key: 'last_7_days',
                    name: 'Последние 7 дней'
                },
                {
                    key: 'all_time',
                    name: 'За все время'
                }
            ],
        }),
        methods: {
            getCategoryById(id) {
                return this.categories.find(c => c.id == id).name;
            },
            async getMVPProducts() {
                this.loading = true;
                await this.$store.dispatch(ACTIONS.GET_MVP_PRODUCTS, {
                    store: this.store_id,
                    time: this.timeFilter
                });
                this.loading = false;
            }
        },
        computed: {
            ...mapGetters(['MVP_CATEGORY_PRODUCTS', 'categories', 'WORST_CATEGORY_PRODUCTS']),
            stores() {
                return [{id: -1, name: 'Все магазины'}, ...this.$store.getters.stores];
            }
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            this.store_id = this.stores[0].id;
            this.timeFilter = this.times[0].key;
            await this.getMVPProducts();
        }
    }
</script>

<style lang="scss">
    .stat-table th, .stat-table td {
        font-size: 16px!important;

        padding: 10px 5px!important;

        li {
            font-size: 16px!important;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        li:first-child {
            border-top: 1px solid #eee;
        }
    }

    th:first-child, td:first-child {
        max-width: 300px!important;
    }

</style>
