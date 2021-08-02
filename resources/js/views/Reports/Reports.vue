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
                        :items="store_types"
                        item-text="type"
                        item-value="id"
                        v-model="currentStoreType"
                        label="Тип магазина:"
                    >
                    </v-select>
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
                    <v-select
                        :items="payment_types"
                        label="Способ оплаты:"
                        v-model="currentType"
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
                        <li v-for="(product, index) of item.products" :key="index" class="d-flex justify-space-between align-center">
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
                <template v-slot:item.payment_type_text="{item}">
                    <span v-if="report.id !== item.id">
                        {{ item.payment_type_text }}
                    </span>
                    <v-select
                        v-if="editMode && report.id === item.id"
                        v-model="report.payment_type"
                        :items="_payment_types"
                        item-text="name"
                        item-value="id"
                    ></v-select>
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
                    <div class="d-flex" v-if="report.id !== item.id">
                        <v-btn color="primary" icon @click="report = item; editMode = true;">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn color="primary" :href="'/check/' + item.id" target="_blank">
                            печать чека
                        </v-btn>
                    </div>
                    <div class="d-flex" v-if="editMode && report.id === item.id">
                        <v-btn icon @click="report = {}; editMode = false">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn icon color="success" @click="changeSale">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </div>
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

    const DATE_FORMAT = 'YYYY-MM-DD';

    export default {
        components: {ReportCancelModal, ConfirmationModal},
        data: () => ({
            overlay: false,
            loading: false,
            cancelModal: false,
            purchaseId: null,
            currentProducts: [],
            currentStoreType: -1,
            startMenu: null,
            report: {},
            editMode: false,
            start: null,
            finishMenu: null,
            today: moment(),
            finish: null,
            currentDate:  [
                moment().format(DATE_FORMAT),
                moment().format(DATE_FORMAT),
            ],
            currentCity: -1,
            currentSeller: -1,
            currentType: -1,
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
            headers: [
                {text: 'Наименование', value: 'products', align: ' min-w-250'},
                {text: 'Дата', value: 'date'},
                {text: 'Клиент', value: 'client'},
                {text: 'Продавец', value: 'user'},
                {text: 'Магазин', value: 'store'},
                {text: 'Способ оплаты', value: 'payment_type_text'},
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
                    text: 'Действие', value: 'print'
                }
            ],
        }),
        async mounted() {
            await this.init();
        },
        methods: {
            async changeSale() {
                this.overlay = true;
                await this.$store.dispatch('updateSale', {
                    id: this.report.id,
                    payment_type: this.report.payment_type
                });
                this.report = {};
                this.overlay = false;
                this.editMode = false;
            },
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
                    start: this.currentDate[0],
                    finish: this.currentDate[1],
                });
                this.overlay = this.loading = false;
            },
            async onConfirm() {
                this.closeModal();
            },
            printCheck(id) {
                window.open(`/check/${id}`, '_blank');
            },
            async loadReport() {

                if (this.currentDate === DATE_FILTERS.CUSTOM_FILTER) {
                    if (!(this.start || this.finish)) {
                        return;
                    }
                }
                this.overlay = true;
                const dateObject = {
                    start: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.start : this.currentDate[0],
                    finish: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.finish : this.currentDate[1]
                };
                await this.$store.dispatch(ACTIONS.GET_REPORTS, dateObject);
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
            payment_types() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.payment_types];
            },
            _payment_types() {
                return this.$store.getters.payment_types;
            },
            shops() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.shops];
            },
            store_types() {
                return [{id: -1, type: 'Все'}, ...this.$store.getters.store_types];
            },
            totalSales() {
                return new Intl.NumberFormat('ru-RU').format(this._salesReport
                    .reduce((a, c) => {
                        return a + c.final_price
                    }, 0).toFixed());
            },
            totalMargin() {
                return new Intl.NumberFormat('ru-RU').format(this._salesReport
                    .reduce((a, c) => {
                        return a + c.margin
                    }, 0).toFixed());
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
                    .filter(s => {
                        if (this.currentType == -1) {
                            return s;
                        } else {
                            return s.payment_type == this.currentType;
                        }
                    })
                    .filter(s => {
                        if (this.currentStoreType === -1) {
                            return s;
                        }
                        return s.store_type == this.currentStoreType;
                    });
            }
        }
    }
</script>

<style>
    h5 {
        font-size: 18px;
    }

    .min-w-250 {
        width: 300px;
    }

    .v-data-table>.v-data-table__wrapper>table>tbody>tr>td, .v-data-table>.v-data-table__wrapper>table>tfoot>tr>td, .v-data-table>.v-data-table__wrapper>table>thead>tr>td {
        height: auto!important;
    }

    @media (max-width: 550px) {
        .v-data-table__mobile-row__cell {
            text-align: left!important;
        }
    }


</style>
