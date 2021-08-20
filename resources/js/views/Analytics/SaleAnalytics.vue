<template>
    <div>
        <v-card>
            <v-card-title>
                Аналитика продаж
            </v-card-title>
            <v-card-text>
                <v-col>
                    <v-menu
                        ref="startMenu"
                        v-model="startMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="start"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="start"
                                label="Дата начала"
                                prepend-icon="event"
                                readonly
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="start"
                            locale="ru"
                            no-title
                            scrollable
                            type="month"
                            :max="maxDate"
                        >
                            <div class="flex-grow-1"></div>
                            <v-btn
                                text
                                outlined
                                color="primary"
                                @click="startMenu = false"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                text
                                outlined
                                color="primary"
                                @click="changeCustomDate(startMenu, start)"
                            >
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                    <v-menu
                        ref="finishMenu"
                        v-model="finishMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="finish"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="finish"
                                label="Дата окончания"
                                prepend-icon="event"
                                readonly
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="finish"
                            locale="ru"
                            no-title
                            scrollable
                            type="month"
                            :max="maxDate"
                        >
                            <div class="flex-grow-1"></div>
                            <v-btn
                                text
                                outlined
                                color="primary"
                                @click="finishMenu = false"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                text
                                outlined
                                color="primary"
                                @click="changeCustomDate(finishMenu, finish) "
                            >
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                    <v-btn block color="primary" @click="getSaleAnalytics">
                        Получить данные
                    </v-btn>
                </v-col>
                <SaleChart
                    v-if="saleAnalyticsLoaded"
                    :chart-data="chartData"
                    :options="options"
                />
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import SaleChart from "@/components/Charts/SaleChart";
    import months from '@/common/enums/months.ru';
    import moment from "moment";
    import ACTIONS from "@/store/actions";
    import { mapGetters } from 'vuex';

    export default {
        data: () => ({
            saleAnalyticsLoaded: false,
            startMenu: null,
            start: null,
            finishMenu: null,
            finish: null,
            maxDate: moment().format('YYYY-MM-DD'),
            chartData: {
                labels: [
                    ...months
                ],
                datasets: [
                    {
                        label: 'Продажи',
                        data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11],
                        backgroundColor: '#e53935',
                        borderColor: '#000'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            return `Продажи: ${new Intl.NumberFormat('ru-RU').format(Math.ceil(tooltipItem.yLabel))}₸`
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            precision: 4,
                            maxTicksLimit: 10,
                            callback: function (value, index, values) {
                                return `${new Intl.NumberFormat('ru-RU').format(Math.ceil(value))}₸`                     }
                        }
                    }],
                    xAxes: [{
                        display: true,
                    }],
                }
            }
        }),
        methods: {
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
            },
            async getSaleAnalytics() {
                if (!(this.start && this.finish)) {
                    return this.$toast.error('Выберите обе даты!');
                }
                this.saleAnalyticsLoaded = false;
                await this.$store.dispatch(ACTIONS.GET_SALE_ANALYTICS, {
                    start: `${this.start}-01`,
                    finish: `${this.finish}-01`
                })
                this.saleAnalyticsLoaded = true;
            },
        },
        computed: {
            ...mapGetters(['SALE_ANALYTICS', 'SALE_ANALYTIC_LABELS'])
        },
        components: {
            SaleChart
        },
        watch: {
            SALE_ANALYTICS(val) {
                this.chartData = {
                    labels: [
                        ...this.SALE_ANALYTIC_LABELS
                    ],
                    datasets: [
                        {
                            label: 'Продажи',
                            data: [...val],
                            backgroundColor: '#e53935',
                            borderColor: '#000'
                        }
                    ]
                };
            }
        }
    }
</script>

<style scoped>

</style>
