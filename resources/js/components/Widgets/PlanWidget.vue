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
            <v-card-text class="pl-0 pr-0">
                <v-row>
                    <v-col v-for="(store) of plans" v-if="(IS_ADMIN || IS_OBSERVER || IS_BOSS || IS_MARKETOLOG) || USER.store_id == store.store_id" cols="12" md="6" lg="auto">
                        <v-list >
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title class="font-weight-bold">
                                        {{ store.name }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ store.month_plan | priceFilters}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План на месяц
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ store.month_fact | priceFilters}} ({{ store.month_percent }}%)
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Выполнение, месяц
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ store.week_plan | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План, неделя
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ store.week_fact | priceFilters}} ({{ store.week_percent }}%)
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Выполнение, неделя
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ getDayPlan(store) | priceFilters}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План, день
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ store.day_fact | priceFilters}} ({{ store.day_percent }}%)
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
                                            :value="store.month_percent"
                                            striped
                                        >
                                            <template v-slot:default="{ value }">
                                                {{ store.prize | priceFilters}}
                                            </template>
                                        </v-progress-linear>
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Премия
                                        <v-icon :color="store.month_percent >= 100 ? 'success' : 'error'">
                                            {{ store.month_percent >= 100 ? 'mdi-check' : 'mdi-close' }}
                                        </v-icon>
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                    <v-col v-if="IS_ADMIN" md="6" lg="auto">
                        <v-list >
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title class="font-weight-bold">
                                       Итого
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ totalMonthPlan | priceFilters}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План на месяц
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ totalMonthPlanSum | priceFilters}} ({{ totalMonthPlanPercent }}%)
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Выполнение, месяц
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ totalWeekPlan | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План, неделя
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ totalWeekPlanSum | priceFilters}} ({{ totalWeekPlanPercent }}%)
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Выполнение, неделя
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ totalDayPlan | priceFilters}}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План, день
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ totalDaySum | priceFilters}} ({{ totalDayPercent }}%)
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Выполнение, день
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import {mapGetters} from 'vuex';

    export default {
        data: () => ({
            plans: [],
            loading: true,
        }),
        computed: {
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
            shops() {
                return this.$store.getters.shops;
            },
            _plans() {
                return this.$store.getters.PLANS;
            },
            planReports() {
                return this.$store.getters.PLAN_REPORTS;
            },
            totalWeekPlan() {
                return this.plans.reduce(function (a, c) {
                    return c.week_plan + a;
                }, 0);
            },
            totalWeekPlanSum() {
                return this.plans.reduce(function (a, c) {
                    return c.week_fact + a;
                }, 0);
            },
            totalWeekPlanPercent() {
                return Math.floor(100 * this.totalWeekPlanSum / this.totalWeekPlan);
            },
            totalMonthPlan() {
                return this.plans.reduce(function (a, c) {
                    return c.month_plan + a;
                }, 0);
            },
            totalMonthPlanSum() {
                return this.plans.reduce(function (a, c) {
                    return c.month_fact + a;
                }, 0);
            },
            totalMonthPlanPercent() {
                return Math.floor(100 * this.totalMonthPlanSum / this.totalMonthPlan);
            },
            totalDayPlan() {
                const dt = new Date();
                const month = dt.getMonth() + 1;
                const year = dt.getFullYear();
                const daysInMonth = new Date(year, month, 0).getDate();
                return Math.ceil(this.totalMonthPlan / daysInMonth);
            },
            totalDaySum() {
                return this.plans.reduce(function (a, c) {
                    return c.day_fact + a;
                }, 0);
            },
            totalDayPercent() {
                return Math.floor(100 * this.totalDaySum / this.totalDayPlan);
            },
            ...mapGetters([
                'IS_ADMIN',
                'USER',
                'IS_OBSERVER'
            ]),
        },
        async created() {
            this.loading = !(this.shops.length && this._plans.length && this.planReports.length);
            await this.$store.dispatch(ACTIONS.GET_PLANS);
            await this.$store.dispatch('getPlanReports');
            this.plans = this.shops.map(s => {
                const _plan = this._plans.find(p => p.store_id == s.id);
                const plan = {
                    store_id: s.id,
                    week_plan: _plan?.week_plan ?? 0,
                    month_plan: _plan?.month_plan ?? 0,
                    name: s.name,
                    week_fact: this.planReports.week[s.id] ? this.planReports.week[s.id].amount : 0,
                    month_fact: this.planReports.month[s.id] ? this.planReports.month[s.id].amount : 0,
                    day_fact: this.planReports.today[s.id] ? this.planReports.today[s.id].amount : 0,
                    prize: _plan?.prize ?? 0,
                };

                plan.week_percent = plan.week_plan !== 0 ? Math.floor(100 * plan.week_fact / plan.week_plan) : 0;
                plan.month_percent = plan.month_plan !== 0 ? Math.floor(100 * plan.month_fact / plan.month_plan) : 0;
                plan.day_percent = plan.day_fact !== 0 ? Math.floor(100 * plan.day_fact / this.getDayPlan(plan)) : 0;

                return plan;
            })
            this.loading = false;
        },
        methods: {
            getDayPlan(store) {
                const dt = new Date();
                const month = dt.getMonth() + 1;
                const year = dt.getFullYear();
                const daysInMonth = new Date(year, month, 0).getDate();
                return Math.ceil(store.month_plan / daysInMonth);
            },
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
