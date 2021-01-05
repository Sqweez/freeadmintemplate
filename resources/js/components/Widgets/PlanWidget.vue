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
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>План на неделю</th>
                        <th>Выполнение на неделю, тенге</th>
                        <th>Выполнение на неделю, %</th>
                        <th>План на месяц</th>
                        <th>Выполнение на месяц, тенге</th>
                        <th>Выполнение на месяц, %</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(store) of plans" v-if="(IS_ADMIN || IS_OBSERVER) || USER.store_id == store.store_id" class="plan__data">
                        <td>{{ store.name }}</td>
                        <td>
                            {{ store.week_plan | priceFilters }}
                        </td>
                        <td>
                            {{ store.week_fact | priceFilters}}
                        </td>
                        <td>
                            {{ store.week_percent }}%
                        </td>
                        <td>
                            {{ store.month_plan | priceFilters}}
                        </td>
                        <td>
                            {{ store.month_fact | priceFilters}}
                        </td>
                        <td>
                            {{ store.month_percent }}%
                        </td>
                    </tr>
                    <tr class="total">
                        <td><b>Итого:</b></td>
                        <td>
                            {{ totalWeekPlan | priceFilters }}
                        </td>
                        <td>
                            {{ totalWeekPlanSum | priceFilters }}
                        </td>
                        <td>
                            {{ totalWeekPlanPercent }} %
                        </td>
                        <td>
                            {{ totalMonthPlan | priceFilters }}
                        </td>
                        <td>
                            {{ totalMonthPlanSum | priceFilters }}
                        </td>
                        <td>
                            {{ totalMonthPlanPercent }} %
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
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
            ...mapGetters([
                'IS_ADMIN',
                'USER',
                'IS_OBSERVER'
            ]),
        },
        async created() {
            this.loading = !(this.shops.length && this._plans.length && this.planReports.length);
            await this.$store.dispatch(ACTIONS.GET_PLANS);
            if (!this.shops.length) {
                await this.$store.dispatch(ACTIONS.GET_STORES);
            }
            await this.$store.dispatch('getPlanReports');
            this.plans = this.shops.map(s => {
                const _plan = this._plans.find(p => p.store_id == s.id);
                const plan = {
                    store_id: s.id,
                    week_plan: _plan.week_plan || 0,
                    month_plan: _plan.month_plan || 0,
                    name: s.name,
                    week_fact: this.planReports.week[s.id] ? this.planReports.week[s.id].amount : 0,
                    month_fact: this.planReports.month[s.id] ? this.planReports.month[s.id].amount : 0,
                };

                plan.week_percent = plan.week_plan !== 0 ? Math.floor(100 * plan.week_fact / plan.week_plan) : 100;
                plan.month_percent = plan.month_plan !== 0 ? Math.floor(100 * plan.month_fact / plan.month_plan) : 100;

                return plan;
            })
            this.loading = false;
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
