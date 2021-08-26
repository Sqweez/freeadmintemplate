<template>
    <v-card>
        <v-responsive>
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">Рейтинг продаж</span>
                <v-select
                    @change="changeFilter"
                    :items="items"
                    item-text="name"
                    item-value="value"
                    style="max-width: 300px; margin-bottom: 0!important;"
                    class="pa-0" v-model="current"/>
            </v-card-title>
            <v-card-text class="pl-0 pr-0">
                <v-list-item-group>
                    <v-list-item v-for="(store, index) of stores" :key="index" class="darken-3" :class="index % 2 ? 'grey' : 'black'" v-if="(IS_ADMIN || IS_OBSERVER || IS_BOSS ) || store.id == USER.store_id">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    {{ store.name }}
                                </div>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                {{ getTotal(store.id) | priceFilters}}
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item class="darken-3 black" v-if="(IS_ADMIN || IS_OBSERVER || IS_BOSS)">
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
                </v-list-item-group>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import moment from 'moment';

    export default {
        data: () => ({
            items: [
                {
                    name: 'Сегодня',
                    value: {
                        dateStart: moment().format(this.dateFormat)
                    }
                },
                {
                    name: 'За неделю',
                    value: 'week'
                },
                {
                    name: 'За месяц',
                    value: 'month'
                },
                {
                    name: 'За 3 месяца',
                    value: '3months'
                },
                {
                    name: 'За все время',
                    value: 'alltime'
                }
            ],
            dateFormat: 'yyyy-M-DD',
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
            totalSum() {
                return this.STORES_REPORTS.reduce((a, c) => {
                    return c.total_cost + a;
                }, 0);
            },
            stores() {
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
            async changeFilter() {
                await this.$store.dispatch('getStoresReport', this.current)
            }
        }

    }
</script>

<style>
</style>
