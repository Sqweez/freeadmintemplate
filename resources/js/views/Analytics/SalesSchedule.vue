<template>
    <div>
        <v-card>
            <v-card-title>
                График продаж
            </v-card-title>
            <v-card-text>
                <v-select
                    :items="months"
                    item-value="value"
                    item-text="name"
                    v-model="date"
                />
                <v-btn color="primary" @click="getSchedule">
                    Получить отчет
                </v-btn>
                <div v-for="shop of shops" :key="`line-chart-shop-${shop.id}`" v-if="dataCollection">
                    <h4>{{ shop.name }}</h4>
                    <LineChart :chart-data="dataCollection[shop.id]" :options="options"/>
                </div>
                <v-simple-table v-slot:default v-if="schedule" class="mt-2">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th v-for="shop of shops">
                            {{ shop.name }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(day, idx) of currentDays">
                        <td>{{ day }}</td>
                        <td v-for="shop of shops">
                            {{ getAmount(shop.id, idx) | priceFilters }}
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from "axios";
import moment from 'moment';
import LineChart from "@/components/Charts/LineChart";

export default {
    components: {LineChart},
    data: () => ({
        months: [],
        date: null,
        schedule: null,
        currentDays: [],
        dataCollection: null,
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
        getAmount(shopId, day) {
            if (!this.schedule) {
                return 0;
            }
            let shopSchedule = this.schedule[shopId];
            if (!shopSchedule) {
                return 0;
            } else {
                return shopSchedule[day];
            }
        },
        async getSchedule() {
            this.$loading.enable();
            let numberOfDays = moment(this.date).daysInMonth();
            this.currentDays = [];
            for (let i = 1; i <= numberOfDays; i++) {
                const date = this.months.find(m => m.value === this.date);
                this.currentDays.push(`${i > 9 ? i : `0${i}`}, ${date.name}`);
            }
            const response = await axios.get(`/api/analytics/sales/schedule?date=${this.date}`);
            this.schedule = response.data;
            this.shops.forEach(shop => {
                const schedule = this.schedule[shop.id] || new Array(numberOfDays).fill(0);
                this.fillData(this.currentDays, schedule, shop.id);
            });

            this.$loading.disable();
        },
        fillData(days, data, idx) {
            this.dataCollection = {
                ...this.dataCollection,
                [idx]: {
                    labels: [
                        ...days
                    ],
                    datasets: [
                        {
                            borderColor: '#f00', // цвет линии
                            pointBackgroundColor: '#000',
                            label: 'Data One',
                            backgroundColor: '#f00',
                            data: [...data],
                            pointRadius: 4,
                        }
                    ]
                },
            };
        },
    },
    computed: {
        shops() {
            return this.$store.getters.shops;
        }
    },
    async mounted() {
        this.months = this.$date.parseMonthsDiff(12);
        this.date = this.months[0].value;
        console.log(this.date);
    },
}
</script>

<style scoped>

</style>
