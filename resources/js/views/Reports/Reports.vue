<template>
    <v-card>
        <v-card-title>
            Отчеты по продажам
        </v-card-title>
        <v-card-text>
            <v-overlay :value="overlay">
                <v-progress-circular indeterminate size="64"></v-progress-circular>
            </v-overlay>
            <v-row>
                <v-col cols="12" xl="3" justify="center">
                    <h5><b>Общая сумма продаж:</b> {{ totalSales }}₸</h5>
                    <h5><b>Общая сумма прибыли:</b> {{ totalMargin }}₸</h5>
                </v-col>
                <v-col cols="12" xl="3">
                    <v-select
                        :items="dateFilters"
                        item-text="name"
                        item-value="value"
                        v-model="currentDate"
                        label="Время:"
                        @change="loadReport"
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
                    <label>Прочие фильтры:</label>
                    <v-select
                        :items="shops"
                        item-text="name"
                        item-value="id"
                        v-model="currentCity"
                        label="Город:"
                    >
                    </v-select>
                    <v-select
                        :items="sellers"
                        label="Продавец:"
                        v-model="currentSeller"
                        item-value="id"
                        item-text="name">
                    </v-select>
                </v-col>
            </v-row>
            <v-data-table
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
                :loading="loading"
                loading-text="Отчеты обновляются"
                :items="_salesReport"
                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
            >
                <template v-slot:item.products="{item}">
                    <ul>
                        <li v-for="(product, index) of item.products" :key="index" class="d-flex justify-space-between">
                            <span>• {{ product.product_name }}</span> <b style="white-space: nowrap">{{ product.count }}
                            шт.</b>
                        </li>
                    </ul>
                </template>
                <template v-slot:item.purchase_price="{item}">
                    {{ item.purchase_price }} ₸
                </template>
                <template v-slot:item.fact_price="{item}">
                    {{ item.fact_price }} ₸
                </template>
                <template v-slot:item.final_price="{item}">
                    {{ item.final_price }} ₸
                </template>
                <template v-slot:item.margin="{item}">
                    {{ item.margin }} ₸
                </template>
                <template v-slot:item.discount="{item}">
                    {{ item.discount }}%
                </template>
                <template v-slot:item.actions="{item}">
                    <v-btn icon color="error"
                           @click="purchaseId = item.id; currentProducts = [...item.products]; cancelModal = true;">
                        <v-icon>mdi-cancel</v-icon>
                    </v-btn>
                </template>
                <template v-slot:item.print="{item}">
                    <v-btn color="primary" :href="'/check/' + item.id" target="_blank">
                        печать чека
                    </v-btn>
                </template>
                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                </template>
            </v-data-table>
        </v-card-text>
        <ReportCancelModal
            :state="cancelModal"
            :products="currentProducts"
            :id="purchaseId"
            v-on:cancel="closeModal"
            v-on:confirm="onConfirm"
        />
    </v-card>
</template>

<script>
    import ConfirmationModal from "../../components/Modal/ConfirmationModal";
    import moment from 'moment';
    import ReportCancelModal from "../../components/Modal/ReportCancelModal";
    import ACTIONS from '../../store/actions/index';

    const DATE_FILTERS = {
        ALL_TIME: 1,
        CURRENT_MONTH: 2,
        TODAY: 3,
        CUSTOM_FILTER: 4,
        LAST_3_DAYS: 5,
    };

    export default {
        components: {ReportCancelModal, ConfirmationModal},
        data: () => ({
            overlay: false,
            loading: false,
            cancelModal: false,
            purchaseId: null,
            currentProducts: [],
            startMenu: null,
            start: null,
            finishMenu: null,
            today: moment(),
            finish: null,
            currentDate: DATE_FILTERS.LAST_3_DAYS,
            currentCity: -1,
            currentSeller: -1,
            dateFilters: [
                {
                    name: 'Последние 3 дня',
                    value: DATE_FILTERS.LAST_3_DAYS,
                },
                {
                    name: 'Сегодня',
                    value: DATE_FILTERS.TODAY,
                },
                {
                    name: 'За текущий месяц',
                    value: DATE_FILTERS.CURRENT_MONTH,
                },
                {
                    name: 'За все время',
                    value: DATE_FILTERS.ALL_TIME,
                },
                {
                    name: 'Произвольно',
                    value: DATE_FILTERS.CUSTOM_FILTER
                },
            ],
            headers: [
                {text: 'Наименование', value: 'products', align: ' min-w-250'},
                {text: 'Дата', value: 'date'},
                {text: 'Клиент', value: 'client'},
                {text: 'Продавец', value: 'user'},
                {text: 'Магазин', value: 'store'},
                {text: 'Закупочная цена', value: 'purchase_price'},
                {text: 'Фактическая цена', value: 'fact_price'},
                {text: 'Продажная цена', value: 'final_price'},
                {text: 'Прибыль', value: 'margin'},
                {text: 'Скидка', value: 'discount'},
                {text: 'Списано с баланса', value: 'balance'},
                {
                    text: 'Отмена', value: 'actions'
                },
                {
                    text: 'Печать', value: 'print'
                }
            ],
        }),
        async mounted() {
            await this.init();
        },
        methods: {
            closeModal() {
                this.currentProducts = [];
                this.purchaseId = null;
                this.cancelModal = false;
            },
            async init() {
                if (this.salesReport.length === 0) {
                    this.overlay = true;
                    this.loading = false;
                } else {
                    this.overlay = false;
                    this.loading = true;
                }
                await this.$store.dispatch(ACTIONS.GET_REPORTS, {
                    filter: this.currentDate,
                });
                await this.$store.dispatch(ACTIONS.GET_STORES);
                await this.$store.dispatch(ACTIONS.GET_USERS);
                this.overlay = this.loading = false;
            },
            async onConfirm() {
                this.closeModal();
            },
            printCheck(id) {
                window.open(`/check/${id}`, '_blank');
            },
            async loadReport() {

                console.log('uea');

                if (this.currentDate === DATE_FILTERS.CUSTOM_FILTER) {
                    if (!(this.start || this.finish)) {
                        console.log(1);
                        return;
                    }
                }
                this.overlay = true;
                await this.$store.dispatch(ACTIONS.GET_REPORTS, {
                    filter: this.currentDate,
                    start: this.start,
                    finish: this.finish
                });
                this.overlay = false;

            },
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);

                if (this.start && this.finish) {
                    await this.loadReport();
                }

            }
        },
        computed: {
            sellers() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.users];
            },
            shops() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.stores];
            },
            totalSales() {
                return this._salesReport
                    .reduce((a, c) => {
                        return a + c.final_price
                    }, 0).toFixed();
            },
            totalMargin() {
                return this._salesReport
                    .reduce((a, c) => {
                        return a + c.margin
                    }, 0).toFixed();
            },
            salesReport() {
                return this.$store.getters.REPORTS || [];
            },
            _salesReport() {
                return this.salesReport
                    .filter(s => {
                        if (this.currentSeller === -1) {
                            return s
                        } else {
                            return s.user_id === this.currentSeller;
                        }
                    })
                    .filter(s => {
                        if (this.currentCity === -1) {
                            return s;
                        } else {
                            return s.store_id === this.currentCity;
                        }
                    })
                /*.filter(s => {
                    const momentDate = moment(s.date, 'DD.MM.YYYY HH:mm');
                    switch (this.currentDate) {
                        case DATE_FILTERS.ALL_TIME:
                            return s;
                        case DATE_FILTERS.LAST_3_DAYS:

                        case DATE_FILTERS.CURRENT_MONTH:
                            const monthStart = moment().startOf('month');
                            return momentDate.isSameOrBefore(this.today, 'day') && momentDate.isSameOrAfter(monthStart, 'day');
                        case DATE_FILTERS.TODAY:
                            return momentDate.isSameOrBefore(this.today, 'day') && momentDate.isSameOrAfter(this.today, 'day');
                        case DATE_FILTERS.CUSTOM_FILTER:
                            return momentDate.isSameOrBefore(moment(this.finish), 'day') && momentDate.isSameOrAfter(moment(this.start), 'day');
                    }
                })*/
            }
        }
    }
</script>

<style scoped>
    h5 {
        font-size: 18px;
    }

    .min-w-250 {
        width: 300px;
    }
</style>
