<template>
    <div>
        <v-card>
            <v-card-title>
                Рейтинг тренеров
            </v-card-title>
            <v-card-text>
                <v-select
                    :items="months"
                    item-value="value"
                    item-text="name"
                    v-model="date"
                />
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Тренер</th>
                        <th>Место работы</th>
                        <th>Инстаграм</th>
                        <th>Аватар</th>
                        <th>Сумма покупок</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(trainer, key) of trainers" :key="key">
                        <td>{{ key + 1 }}</td>
                        <td>{{ trainer.name }}</td>
                        <td>{{ trainer.trainer_job }}</td>
                        <td>{{ trainer.trainer_instagram }}</td>
                        <td>
                            <img :src="trainer.trainer_image" height="150">
                        </td>
                        <td>{{ trainer.amount | priceFilters }}</td>
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
            trainers: [],
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
        },
        computed: {},
        async mounted() {
            this.months = this.parseMonths();
            this.date = this.months[0].value;
            console.log(this.date);
        },
        watch: {
            async date(value) {
                if (value) {
                    const response = await axios.get(`/api/shop/partners?date=${value}`);
                    this.trainers = response.data.top10;
                }
            }
        }
    }
</script>

<style scoped>

</style>
