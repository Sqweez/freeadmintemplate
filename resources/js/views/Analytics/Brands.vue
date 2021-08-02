<template>
    <div>
        <v-card>
            <v-card-title>
                Статистика по брендам
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" xl="3">
                        <v-select
                            :items="dateFilters"
                            item-text="name"
                            item-value="value"
                            v-model="currentDate"
                            label="Время:"
                        />
                    </v-col>
                    <v-col v-if="currentDate === 4">
                        <label>Произвольная дата</label>
                        <v-menu
                            ref="startMenu"
                            v-model="startMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="start"
                            transition="scale-transition"
                            min-width="290px"
                            offset-y
                            full-width
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
                            full-width
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
                            >
                                <div class="flex-grow-1"></div>
                                <v-btn
                                    text
                                    outline
                                    color="primary"
                                    @click="finishMenu = false"
                                >
                                    Отмена
                                </v-btn>
                                <v-btn
                                    text
                                    outline
                                    color="primary"
                                    @click="changeCustomDate(finishMenu, finish) "
                                >
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col>
                        <v-select
                            :items="shops"
                            item-text="name"
                            item-value="id"
                            v-model="currentCity"
                            label="Город:"
                        >
                        </v-select>
                    </v-col>
                </v-row>
                <v-btn block color="primary" class="my-2" @click="loadReports()">
                    Загрузить отчет
                </v-btn>
                <v-simple-table v-if="brandReport.length > 0">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Название</th>
                            <th>Сумма продаж</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) of brandReport">
                            <td>{{ key + 1 }}</td>
                            <td>{{ item.manufacturer }}</td>
                            <td>{{ item.total | priceFilters }}</td>
                        </tr>
                        <tr>
                            <td>
                                #
                            </td>
                            <td><b>Итого:</b></td>
                            <td>{{ totalAmount | priceFilters }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import moment from "moment";
    import ACTIONS from "@/store/actions";
    import {getBrandsAnalytics} from "@/api/analytics";

    const DATE_FILTERS = {
        ALL_TIME: 1,
        CURRENT_MONTH: 2,
        TODAY: 3,
        CUSTOM_FILTER: 4,
        LAST_3_DAYS: 5,
    };
    const DATE_FORMAT = 'YYYY-MM-DD';

    export default {
        data: () => ({
            dateFilters: [
                {
                    name: 'Сегодня',
                    value: [
                        moment().format(DATE_FORMAT),
                        moment().format(DATE_FORMAT),
                    ],
                },
                {
                    name: 'Последние 3 дня',
                    value: [
                        moment().subtract(3, 'days').format(DATE_FORMAT),
                        moment().format(DATE_FORMAT),
                    ],
                },
                {
                    name: 'За текущий месяц',
                    value: [
                        moment().startOf('month').format(DATE_FORMAT),
                        moment().format(DATE_FORMAT),
                    ],
                },
                {
                    name: 'За все время',
                    value: [
                        moment.unix(1).format(DATE_FORMAT),
                        moment().format(DATE_FORMAT)
                    ],
                },
                {
                    name: 'Произвольно',
                    value: DATE_FILTERS.CUSTOM_FILTER
                },
            ],
            currentDate:  [
                moment().startOf('month').format(DATE_FORMAT),
                moment().format(DATE_FORMAT),
            ],
            start: null,
            startMenu: null,
            finishMenu: null,
            today: moment(),
            finish: null,
            currentCity: -1,
            brandReport: [],
        }),
        methods: {
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
            },
            async loadReports() {
                this.$loading.enable();
                const date_start = this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.start : this.currentDate[0];
                const date_finish = this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.finish : this.currentDate[1];
                const response = await getBrandsAnalytics(this.currentCity, date_start, date_finish);
                this.brandReport = response.data;
                this.$loading.disable();
            }
        },
        computed: {
            shops() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.shops];
            },
            totalAmount() {
                return this.brandReport.reduce((a, c) => {
                    return a + c.total;
                }, 0)
            }
        },
        async created() {

        }
    }
</script>

<style scoped>

</style>
