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
                <v-simple-table v-slot:default :dense="false">
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
                    <tr v-for="(store) of plans" v-if="IS_ADMIN || USER.store_id == store.store_id">
                        <td>{{ store.name }}</td>
                        <td>
                            {{ store.week_plan }}₸
                        </td>
                        <td>
                            {{ store.week_fact }}₸
                        </td>
                        <td>
                            {{ store.week_percent }}%
                        </td>
                        <td>
                            {{ store.month_plan }}₸
                        </td>
                        <td>
                            {{ store.month_fact }}₸
                        </td>
                        <td>
                            {{ store.month_percent }}%
                        </td>
                    </tr>
                    <tr class="total">
                        <td><b>Итого:</b></td>
                        <td>
                            {{ new Intl.NumberFormat('ru-RU').format(totalWeekPlan) }}₸
                        </td>
                        <td>
                            {{ new Intl.NumberFormat('ru-RU').format(totalWeekPlanSum) }}₸
                        </td>
                        <td>
                            {{ totalWeekPlanPercent }} %
                        </td>
                        <td>
                            {{new Intl.NumberFormat('ru-RU').format(totalMonthPlan) }}₸
                        </td>
                        <td>
                            {{ new Intl.NumberFormat('ru-RU').format(totalMonthPlanSum) }}₸
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
        methods: {
            getTotalWeek(store_id) {
                const sales = this.planReports.week.filter(s => s.store_id === store_id) || [];
                return sales.reduce((a, c) => {
                    return c.total_cost + a;
                }, 0);
            },
            getTotalMonth(store_id) {
                const sales = this.planReports.month.filter(s => s.store_id === store_id) || [];
                return sales.reduce((a, c) => {
                    return c.total_cost + a;
                }, 0);
            },
        },
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
                    return c._week_plan + a;
                }, 0);
            },
            totalWeekPlanSum() {
                return this.plans.reduce(function (a, c) {
                    return c._week_fact + a;
                }, 0);
            },
            totalWeekPlanPercent() {
                return Math.floor(100 * this.totalWeekPlanSum / this.totalWeekPlan);
            },
            totalMonthPlan() {
                return this.plans.reduce(function (a, c) {
                    return c._month_plan + a;
                }, 0);
            },
            totalMonthPlanSum() {
                return this.plans.reduce(function (a, c) {
                    return c._month_fact + a;
                }, 0);
            },
            totalMonthPlanPercent() {
                return Math.floor(100 * this.totalMonthPlanSum / this.totalMonthPlan);
            },
            ...mapGetters([
                'IS_ADMIN',
                'USER'
            ]),
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_PLANS);
            await this.$store.dispatch(ACTIONS.GET_STORES);
            await this.$store.dispatch('getPlanReports');
            this.plans = this.shops.map(s => {
                const plan = this._plans.find(p => p.store_id == s.id);
                if (!plan) {
                    return {
                        store_id: s.id,
                        week_plan: 0,
                        _week_plan: 0,
                        month_plan: 0,
                        _month_plan: 0,
                        name: s.name,
                        week_fact: new Intl.NumberFormat('ru-RU').format(this.getTotalWeek(s.id)),
                        _week_fact: this.getTotalWeek(s.id),
                        month_fact: new Intl.NumberFormat('ru-RU').format(this.getTotalMonth(s.id)),
                        _month_fact: this.getTotalMonth(s.id),
                        week_percent: 100,
                        month_percent: 100,
                    }
                }
                plan.name = s.name;
                plan.month_percent = Math.floor(100 * this.getTotalMonth(s.id) / plan.month_plan);
                plan.week_percent = Math.floor(100 * this.getTotalWeek(s.id) / plan.week_plan);
                plan._week_plan = plan.week_plan;
                plan.week_plan = new Intl.NumberFormat('ru-RU').format(plan.week_plan);
                plan._month_plan = plan.month_plan;
                plan.month_plan = new Intl.NumberFormat('ru-RU').format(plan.month_plan);
                plan._week_fact = this.getTotalWeek(s.id);
                plan.week_fact = new Intl.NumberFormat('ru-RU').format(this.getTotalWeek(s.id));
                plan._month_fact =this.getTotalMonth(s.id);
                plan.month_fact = new Intl.NumberFormat('ru-RU').format(this.getTotalMonth(s.id));
                return plan;
            });

            this.loading = false;
        }
    }
</script>

<style scoped>
    .total td{
        border-top: 1px solid #ccc;
    }
</style>
