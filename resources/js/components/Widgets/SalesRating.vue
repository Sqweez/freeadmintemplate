<template>
    <v-card>
        <v-responsive>
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">Продажи</span>
                <v-select
                    @change="changeFilter"
                    :items="items"
                    item-text="name"
                    item-value="value"
                    :disabled="IS_OBSERVER"
                    style="max-width: 300px; margin-bottom: 0!important;"
                    class="pa-0" v-model="current"/>
            </v-card-title>
            <v-card-text class="pl-0 pr-0">
                <v-list flat>
                    <v-list-item v-for="(store, index) of stores" :key="index" class="darken-3" :class="index % 2 ? 'grey' : 'black'" v-if="(IS_ADMIN || IS_OBSERVER || IS_BOSS || IS_MARKETOLOG) || store.id == USER.store_id">
                        <v-list-item-content>
                            <v-list-item-title class="d-flex justify-space-between">
                                <span>{{ store.name }}</span>
                                <span class="font-weight-black">{{ getAmount(store.id) | priceFilters}}</span>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item class="darken-3 black" v-if="IS_ADMIN || IS_BOSS || IS_OBSERVER || IS_MARKETOLOG || IS_FRANCHISE">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>Каспи магазин</span>
                                    <span>
                                        {{ getAmount(5555) | priceFilters}}
                                    </span>
                                </div>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item class="darken-3 black" v-if="IS_ADMIN || IS_BOSS || IS_OBSERVER || IS_MARKETOLOG">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>Интернет-магазин</span>
                                    <span>
                                        {{ getAmount(-1) | priceFilters}}
                                    </span>
                                </div>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item class="darken-3 black" v-if="IS_ADMIN || IS_BOSS || IS_OBSERVER || IS_MARKETOLOG">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>Оптовые продажи</span>
                                    <span>
                                        {{ getAmount(7845) | priceFilters}}
                                    </span>
                                </div>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item class="darken-3 black" v-if="IS_ADMIN || IS_BOSS || IS_OBSERVER || IS_MARKETOLOG">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>Бронирования</span>
                                    <span>
                                        {{ getAmount(9999) | priceFilters}}
                                    </span>
                                </div>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item class="darken-3 black" v-if="IS_ADMIN || IS_BOSS || IS_OBSERVER || IS_MARKETOLOG">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>Итого</span>
                                    <span>
                                        {{ totalSum | priceFilters }}
                                    </span>
                                </div>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import moment from 'moment';


    const DATE_FORMAT = 'YYYY-MM-DD';

    export default {
        data: () => ({
            items: [
                {
                    name: 'Сегодня',
                    value: moment().format(DATE_FORMAT)
                },
                {
                    name: 'За неделю',
                    value: moment().subtract(7, 'days').format(DATE_FORMAT)
                },
                {
                    name: 'За месяц',
                    value: moment().subtract(30, 'days').format(DATE_FORMAT)
                },
                {
                    name: 'За 3 месяца',
                    value: moment().subtract(90, 'days').format(DATE_FORMAT)
                },
                {
                    name: 'За все время',
                    value:  moment.unix(1).format(DATE_FORMAT),
                }
            ],
            current: null,
        }),
        mounted() {
            this.$store.dispatch('getStoresReport')
            this.current = this.items[0];
        },
        computed: {
            ...mapGetters([
                'STORES_REPORTS',
                'IS_ADMIN',
                'USER',
                'IS_OBSERVER'
            ]),
            totalSum () {
                const values = Object.values(this.STORES_REPORTS);
                return values.reduce((a, c) => {
                    return c.amount + a;
                }, 0);
            },
            stores () {
                return [
                    ...this.$store.getters.shops,
                    ...this.$store.getters.warehouses
                ];
            }
        },
        methods: {
            ...mapActions([
                'getStoresReport'
            ]),
            getTotal(store_id) {
                const sales = this.STORES_REPORTS.filter(s => s.store_id === store_id) || [];
                return sales.reduce((a, c) => {
                    return c.total_cost + a;
                }, 0)
            },
            getAmount(store_id) {
                return this.STORES_REPORTS[store_id] ? this.STORES_REPORTS[store_id].amount : 0;
            },
            async changeFilter() {
                await this.$store.dispatch('getStoresReport', this.current)
            }
        }

    }
</script>

<style>
</style>
