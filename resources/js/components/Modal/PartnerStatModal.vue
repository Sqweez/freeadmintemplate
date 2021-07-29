<template>
    <v-dialog persistent max-width="1200" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Информация о партнере</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('close')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div class="d-flex justify-end">
                    <v-select
                        style="max-width: 300px; text-align: right"
                        :items="filters"
                        v-model="currentFilter"
                        item-text="name"
                        item-value="value"
                    ></v-select>
                </div>
                <h5>Партнер: {{ partner.client_name }}</h5>
                <h5>Телефон: {{ partner.client_phone }}</h5>
                <v-simple-table v-if="isValid">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>
                                Общая сумма продаж
                            </th>
                            <th>
                                Количество уникальных клиентов
                            </th>
                            <th>
                                Количество продаж
                            </th>
                            <th>
                                Средний чек
                            </th>
                            <th>
                                Наиболее популярные позиции
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ current_stats.total_sales_sum | priceFilters}}</td>
                            <td>{{ current_stats.unique_clients_count }}</td>
                            <td>{{ current_stats.count }}</td>
                            <td>{{ current_stats.avg_sum | priceFilters}}</td>
                            <td>
                                <ul>
                                    <li
                                        :key="product.product.id"
                                        v-for="product of current_stats.most_popular_products"
                                    >
                                        <a :href="`https://iron-addicts.kz/product/${product.product.id}`" target="_blank">
                                            <b>{{ product.product.product_name }}</b> | {{ product.count }} шт.
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div v-if="isValid">
                    <h5>Клиенты:</h5>
                    <ol>
                        <li v-for="client of current_stats.unique_clients" :key="client.id">
                            {{ client.client_name }}
                        </li>
                    </ol>
                </div>
                <h5 v-if="!isValid" class="text-center">Недостаточно информации для формирования отчета!</h5>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    import axios from 'axios';
    export default {
        data: () => ({
            currentFilter: 'daily',
            filters: [
                {
                    name: 'За день',
                    value: 'daily'
                },
                {
                    name: 'За неделю',
                    value: 'weekly'
                },
                {
                    name: 'За месяц',
                    value: 'monthly'
                },
                {
                    name: 'За все время',
                    value: 'all_time'
                },
            ],
            stats: [],
        }),
        methods: {},
        computed: {
            current_stats() {
                return this.stats[this.currentFilter] || {};
            },
            isValid() {
                return Object.keys(this.current_stats).length > 0;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: true
            },
            partner: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            async state(value) {
                if (value) {
                    this.currentFilter = 'daily';
                    this.$loading.enable();
                    const response = await axios(`/api/analytics/partners/${this.partner.id}`);
                    this.stats = response.data;
                    this.$loading.disable();
                } else {
                    this.stats = [];
                }
            }
        }
    }
</script>

<style scoped>

</style>
