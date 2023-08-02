<template>
    <v-card>
        <v-responsive
            :min-height="200"
            v-if="loading"
            class="text-center d-flex justify-center align-center">
            <v-progress-circular
                :size="50"
                color="primary"
                indeterminate
            ></v-progress-circular>
        </v-responsive>
        <v-responsive v-else>
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">План продаж</span>
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Магазин"
                    :items="$shops"
                    item-text="name"
                    item-value="id"
                    v-model="storeFilter"
                    :disabled="!IS_SUPERUSER"
                />
                <v-list v-if="plan">
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-bold">
                                {{ plan.store_name }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ plan.month.plan | priceFilters}}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                План на месяц
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ plan.month.amount | priceFilters}} ({{ plan.month.percent }}%)
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                Выполнение, месяц
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ plan.week.plan | priceFilters }}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                План, неделя
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ plan.week.amount | priceFilters}} ({{ plan.week.percent }}%)
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                Выполнение, неделя
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ plan.today.plan | priceFilters}}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                План, день
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ plan.today.amount | priceFilters}} ({{ plan.today.percent }}%)
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                Выполнение, день
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                <v-progress-linear
                                    color="light-green darken-2"
                                    height="20"
                                    :value="plan.month.percent"
                                    striped
                                >
                                    <template v-slot:default="{ value }">
                                        {{ plan.plan.prize | priceFilters}}
                                    </template>
                                </v-progress-linear>
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                Премия
                                <v-icon :color="plan.month.percent >= 100 ? 'success' : 'error'">
                                    {{ plan.month.percent >= 100 ? 'mdi-check' : 'mdi-close' }}
                                </v-icon>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    export default {
        data: () => ({
            plans: [],
            loading: true,
            storeFilter: null,
        }),
        computed: {
            plan () {
                return this.$store.getters.PLAN_REPORTS;
            }
        },
        methods: {
            getDayPlan(store) {
                const dt = new Date();
                const month = dt.getMonth() + 1;
                const year = dt.getFullYear();
                const daysInMonth = new Date(year, month, 0).getDate();
                return Math.ceil(store.month_plan / daysInMonth);
            },
            _init () {
                this.loading = true;
                this.storeFilter = this.IS_SUPERUSER ? this.$shops[0]?.id : this.$user.store_id;
                this.loading = false;
            },
        },
        watch: {
            async storeFilter (value) {
                if (value) {
                    this.loading = true;
                    await this.$store.dispatch('getPlanReports', value);
                    this.loading = false;
                }
            },
            $shops: {
                immediate: true,
                handler: function (value) {
                    if (value && value.length) {
                        this._init();
                    }
                }
            }
        }
    }
</script>

<style lang="scss">
    .total td{
        border-top: 1px solid #ffffff;
    }

    .plan__data {
        color: #fff;
        td {
            font-size: 14px!important;
        }
    }
</style>
