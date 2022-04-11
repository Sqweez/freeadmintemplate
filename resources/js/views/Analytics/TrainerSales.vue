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
                        <td>
                            <a :href="`https://instagram.com/${trainer.trainer_instagram}`" v-if="trainer.trainer_instagram" target="_blank">{{ trainer.trainer_instagram }}</a>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <img :src="trainer.trainer_image" height="150">
                        </td>
                        <td>{{ trainer.amount | priceFilters }}</td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <v-card>
            <v-card-title>
                Неактивные тренера
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col md="12" xl="6">
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
                    </v-col>
                    <v-col md="12" xl="6">
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
                    </v-col>
                </v-row>
                <div class="d-flex">
                    <v-btn color="success" @click="getTrainerReport" class="mr-3">Получить отчет</v-btn>
                    <download-excel
                        v-if="inactiveTrainers.length > 0"
                        :data="jsonData"
                        :fields="jsonFields"
                        :exportFields="jsonFields"
                        name="Неактивные_тренера.xls"
                        :stringifyLongNum="true"
                        type="xls"
                    >
                        <v-btn text color="success">
                            Экспортировать
                        </v-btn>
                    </download-excel>
                </div>
                <v-select
                    :items="cities"
                    item-text="name"
                    item-value="id"
                    v-model="cityId"
                />
                <v-checkbox
                    label="Полностью неактивные тренера"
                    v-model="fullyInactive"
                />
                <v-data-table
                    :items="inactiveTrainersList"
                    :headers="headers"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                    }">
                    <template v-slot:item.without_own_sales="{item}">
                        <v-icon color="success" v-if="!item.without_own_sales">
                            mdi-check
                        </v-icon>
                        <v-icon color="error" v-else>
                            mdi-close
                        </v-icon>
                        <span>({{ item.own_sales | priceFilters }})</span>
                    </template>
                    <template v-slot:item.without_partner_sales="{item}">
                        <v-icon color="success" v-if="!item.without_partner_sales">
                            mdi-check
                        </v-icon>
                        <v-icon color="error" v-else>
                            mdi-close
                        </v-icon>
                        <span>({{ item.partner_sales | priceFilters }})</span>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import axios from "axios";
    import moment from 'moment';
    import MonthsRu from "@/common/enums/months.ru";
    import axiosClient from "@/utils/axiosClient";
    export default {
        data: () => ({
            fullyInactive: false,
            months: [],
            date: null,
            trainers: [],
            maxDate: moment().format('YYYY-MM-DD'),
            start: null,
            startMenu: null,
            finish: null,
            finishMenu: null,
            inactiveTrainers: [],
            cityId: -1,
            headers: [
                {
                    text: 'Имя',
                    value: 'client_name'
                },
                {
                    text: 'Телефон',
                    value: 'client_phone'
                },
                {
                    text: 'Есть покупки',
                    value: 'without_own_sales'
                },
                {
                    text: 'Есть продажи',
                    value: 'without_partner_sales'
                },
                {
                    text: 'Город',
                    value: 'city.name'
                }
            ],
            fields: [
                {
                    value: 'client_name',
                    title: 'ФИО',
                },
                {
                    value: 'phone',
                    title: 'Телефон',
                },
                {
                    value: 'without_own_sales',
                    title: 'Есть покупки'
                },
                {
                    value: 'without_partner_sales',
                    title: 'Есть продажи'
                }
            ],
            selectedFields: [
                "ФИО", "Телефон", "Есть покупки", "Есть продажи"
            ],
        }),
        methods: {
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
            },
            async getTrainerReport () {
                if (!(this.start || this.finish)) {
                    return this.$toast.error('Выберите обе даты!');
                }
                this.$loading.enable();
                const { data } = await axios.get(`/api/analytics/trainers/inactive?start=${this.start}&finish=${this.finish}`)
                this.inactiveTrainers = data;
                this.$loading.disable();
            }
        },
        computed: {
            inactiveTrainersList () {
                let trainers = this.inactiveTrainers;
                if (this.fullyInactive) {
                    trainers = trainers.filter(i => i.without_own_sales && i.without_partner_sales);
                }

                if (this.cityId !== -1) {
                    trainers = trainers.filter(i => i.client_city == this.cityId);
                }

                return trainers;
            },
            jsonData() {
                return this.inactiveTrainersList.map((client, key) => {
                    return {
                        key: client.id,
                        client_name: client.client_name,
                        phone: client.client_phone,
                        without_own_sales: !client.without_own_sales ? 'Да' : 'Нет',
                        without_partner_sales: !client.without_partner_sales ? 'Да' : 'Нет',
                    };
                });
            },
            cities () {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.cities];
            },
            jsonFields() {
                const fields = this.selectedFields.map((field) => {
                    return {
                        [field]: this.fields.find(s => s.title === field).value
                    };
                })

                const object = {
                    '#': 'key'
                };

                for (let i = 0; i < this.selectedFields.length; i++) {
                    object[this.selectedFields[i]] = this.fields.find(s => s.title === this.selectedFields[i]).value
                }

                return object;
            },
        },
        async mounted() {
            this.months = this.$date.parseMonthsDiff(6);
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
