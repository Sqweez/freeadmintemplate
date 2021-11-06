<template>
    <div>
        <v-card>
            <v-card-title>
                Рейтинг продаж партнерам
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
                        <th>Партнер</th>
                        <th>Сумма продаж</th>
                        <th>Сумма маржи</th>
                        <th>Товары</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(partner, idx) of partners" :key="idx">
                        <td>{{ idx + 1 }}</td>
                        <td>{{ partner.name }}</td>
                        <td>{{ partner.amount | priceFilters }}</td>
                        <td>{{ partner.margin | priceFilters }}</td>
                        <td>
                            <v-list>
                                <v-list-item v-for="(product, i) of partner.products" :key="`product=${i}`">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ product.product_name }} | {{ product.count }} шт.
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ product.manufacturer }}, {{ product.attributes.join(' ') }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
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
            partners: [],
        }),
        methods: {
            parseMonths() {
                let dateEnd = moment();
                let dateStart = moment().subtract(12, 'months');
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
        },
        watch: {
            async date(value) {
                if (value) {
                    const response = await axios.get(`/api/analytics/partner/sales?date=${value}`);
                    this.partners = response.data;
                }
            }
        }
    }
</script>

<style scoped>

</style>
