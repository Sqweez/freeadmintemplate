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
                <v-simple-table v-slot:default v-if="schedule">
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
export default {
    data: () => ({
        months: [],
        monthNames: [
            'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ],
        date: null,
        schedule: null,
        currentDays: [],
    }),
    methods: {
        parseMonths() {
            let dateEnd = moment();
            let dateStart = moment().subtract(5, 'months');
            let interim = dateStart.clone();
            let timeValues = [];

            while (dateEnd > interim || interim.format('M') === dateEnd.format('M')) {
                timeValues.push(interim.format('YYYY-MM'));
                interim.add(1,'month');
            }

            return timeValues.map((m) => {
                const dates = m.split('-');
                const year = dates[0];
                const month = this.monthNames[parseInt(dates[1]) - 1];
                return {
                    name: `${month}, ${year} г.`,
                    value: m + '-01',
                };
            }).reverse();
        },
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
            for (let i = 1; i <= numberOfDays; i++) {
                const date = this.months.find(m => m.value === this.date);
                this.currentDays.push(`${i > 9 ? i : `0${i}`}, ${date.name}`);
            }
            const response = await axios.get(`/api/analytics/sales/schedule?date=${this.date}`);
            this.schedule = response.data;
            this.$loading.disable();
        },
    },
    computed: {
        shops() {
            return this.$store.getters.shops;
        }
    },
    async mounted() {
        this.months = this.parseMonths();
        this.date = this.months[0].value;
        console.log(this.date);
    },
}
</script>

<style scoped>

</style>
