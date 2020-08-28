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
                    style="max-width: 300px; margin-bottom: 0!important;"
                    class="pa-0" v-model="current"/>
            </v-card-title>
            <v-card-text class="pl-0 pr-0">
                <v-list-item-group>
                    <v-list-item v-for="(store, index) of stores" :key="index" class="darken-3" :class="index % 2 ? 'grey' : 'black'" v-if="IS_ADMIN || store.id == USER.store_id">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>{{ store.name }}</span>
                                    <span>
                                        {{ getTotal(store.id) }} тнг
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

    export default {
        data: () => ({
            items: [
                {
                    name: 'Сегодня',
                    value: 'today'
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
            current: null,
        }),
        mounted() {
            this.$store.dispatch('getStores');
            this.$store.dispatch('getStoresReport')
            this.current = this.items[0];
        },
        computed: {
            ...mapGetters([
                'STORES_REPORTS',
                'IS_ADMIN',
                'USER'
            ]),
            stores() {
                return this.$store.getters.shops.filter(s => {
                    return s;
                }) || [];
            }
        },
        methods: {
            ...mapActions([
                'getStoresReport'
            ]),
            getTotal(store_id) {
                const sales = this.STORES_REPORTS.filter(s => s.store_id === store_id) || [];
                return new Intl.NumberFormat('ru-RU').format(sales.reduce((a, c) => {
                    return c.total_cost + a;
                }, 0));
            },
            async changeFilter() {
                await this.$store.dispatch('getStoresReport', this.current)
            }
        }

    }
</script>

<style>
</style>
