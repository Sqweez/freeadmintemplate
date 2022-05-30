<template>
    <v-card>
        <v-card-title>
            Отчеты по продажам
        </v-card-title>
        <v-card-text>
            <v-row>
                <v-col cols="12" xl="3" justify="center">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма продаж:</v-list-item-title>
                                <v-list-item-title>{{ totalSales | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="IS_BOSS || IS_FRANCHISE">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма прибыли:</v-list-item-title>
                                <v-list-item-title>{{ totalMargin | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма бронирований:</v-list-item-title>
                                <v-list-item-title>{{ bookingTotal | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Количество продаж:</v-list-item-title>
                                <v-list-item-title>{{ totalSaleCount }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Средний чек:</v-list-item-title>
                                <v-list-item-title>{{ averageCheck | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
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
                <v-col>
                    <label>Прочие фильтры:</label>
                    <v-select
                        v-if="IS_SUPERUSER"
                        :items="store_types"
                        item-text="type"
                        item-value="id"
                        v-model="currentStoreType"
                        label="Тип магазина:"
                    >
                    </v-select>
                    <v-select
                        v-if="IS_SUPERUSER"
                        :items="shops"
                        item-text="name"
                        item-value="id"
                        v-model="currentCity"
                        label="Город:"
                    >
                    </v-select>
                    <v-select
                        v-if="IS_SUPERUSER"
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
                    <v-autocomplete
                        :items="manufacturers"
                        item-text="manufacturer_name"
                        v-model="manufacturerId"
                        item-value="id"
                        label="Бренд"
                    />
                    <v-row>
                        <v-col>
                            <v-text-field
                                label="Процент скидки, от"
                                type="number"
                                v-model="discountFrom"
                            />
                        </v-col>
                        <v-col>
                            <v-text-field
                                label="Процент скидки, до"
                                type="number"
                                v-model="discountTo"
                            />
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <v-text-field
                label="Поиск по отчетам"
                append-icon="search"
                v-model="search"
                clearable
            />
            <v-text-field
                label="Поиск по комментарию"
                append-icon="search"
                v-model="searchComment"
                clearable
            />
            <v-data-table
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
                :loading="loading"
                loading-text="Отчеты обновляются"
                :items="_salesReport"
                :search="search"
                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
            >
                <template v-slot:item.products="{item}">
                    <v-list>
                        <v-list-item v-for="(product, index) of item.products" :key="index">
                            <v-list-item-content>
                                <v-list-item-title>{{ product.product_name }}</v-list-item-title>
                                <v-list-item-subtitle>{{ product.attributes.join(", ") }}<span v-if="product.manufacturer.manufacturer_name">,</span> {{ product.manufacturer.manufacturer_name }}</v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                                <span>{{ product.count }} шт</span>
                                <span v-if="product.discount > 0">Скидка: {{ product.discount }}%</span>
                            </v-list-item-action>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.sale_data="{item}">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.client.client_name }}</v-list-item-title>
                                <v-list-item-subtitle>Клиент</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.user.name }}</v-list-item-title>
                                <v-list-item-subtitle>Продавец</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.store.name }}</v-list-item-title>
                                <v-list-item-subtitle>Магазин</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="item.sale_type">
                            <v-list-item-content>
                                <v-list-item-title>{{ item.sale_type }}</v-list-item-title>
                                <v-list-item-subtitle>Тип продажи</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.economy="{item}">
                    <v-list>
                        <v-list-item v-if="IS_BOSS">
                            <v-list-item-content >
                                <v-list-item-title>{{ item.purchase_price | priceFilters}}</v-list-item-title>
                                <v-list-item-subtitle>Закупочная стоимость</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.fact_price | priceFilters}}</v-list-item-title>
                                <v-list-item-subtitle>Фактическая стоимость</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.final_price | priceFilters}}</v-list-item-title>
                                <v-list-item-subtitle>Финальная стоимость</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="IS_BOSS">
                            <v-list-item-content>
                                <v-list-item-title>{{ item.margin | priceFilters}} (<span v-if="item.final_price > 0" :class="Math.ceil(item.margin * 100 / item.final_price) > 0 ? 'green--text' : 'red--text'">{{ Math.ceil(item.margin * 100 / item.final_price) }}%</span>)</v-list-item-title>
                                <v-list-item-subtitle>Прибыль</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.additional_data="{item}">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-icon :color="item.is_delivery ? 'success': 'error'">
                                        {{ item.is_delivery ? 'mdi-check' : 'mdi-cancel' }}
                                    </v-icon>
                                </v-list-item-title>
                                <v-list-item-subtitle>Доставка</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.balance | priceFilters}}</v-list-item-title>
                                <v-list-item-subtitle>Списано с баланса</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="item.certificate">
                            <v-list-item-content>
                                <v-list-item-title>{{ item.certificate.amount | priceFilters}}</v-list-item-title>
                                <v-list-item-subtitle>Оплачено сертификатом</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <div v-if="item.split_payment">
                            <v-list-item v-for="payment of item.split_payment" :key="`split_payment-${item.id}-${payment.payment_text}`">
                                <v-list-item-content>
                                    <v-list-item-title>{{ payment.amount | priceFilters }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ payment.payment_text }}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </div>
                        <v-list-item v-if="item.comment">
                            <v-list-item-content>
                                <v-list-item-title style="white-space: normal;">{{ item.comment }}</v-list-item-title>
                                <v-list-item-subtitle style="white-space: normal;">Комментарий</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="item.is_booking">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-icon>mdi-check</v-icon>
                                </v-list-item-title>
                                <v-list-item-subtitle>По брони</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="item.is_booking">
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.booking_paid_sum | priceFilters }}
                                </v-list-item-title>
                                <v-list-item-subtitle>Предоплата по брони</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
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
                <template v-slot:item.action="{item}">
                    <v-list v-if="report.id !== item.id && !item.sale_type">
                        <v-list-item v-if="IS_SUPERUSER">
                            <v-btn small depressed color="error" text @click="purchaseId = item.id; currentProducts = [...item.products]; cancelModal = true;">
                                Отмена <v-icon class="ml-2">mdi-cancel</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item>
                            <v-btn small depressed color="primary" text @click="report = item; editMode = true;">
                                Способ оплаты <v-icon class="ml-2">mdi-pencil</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item v-if="IS_SUPERUSER">
                            <v-btn small depressed color="primary" text @click="report = {...item}; editModal = true;">
                                Заказ <v-icon class="ml-2">mdi-pencil</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item>
                            <v-btn small depressed color="success" text :href="'/check/' + item.id" target="_blank">
                                Чек <v-icon class="ml-2">mdi-printer</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item>
                            <v-btn small depressed color="success" text @click="createWaybill(item)">
                                Накладная <v-icon class="ml-2">mdi-printer</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item>
                            <v-btn small depressed color="success" text @click="createInvoice(item)">
                                Счет-фактура <v-icon class="ml-2">mdi-printer</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item v-if="IS_SUPERUSER">
                            <v-btn small depressed color="success" text @click="sendTelegram(item.id)">
                                Отправить в телегу <v-icon class="ml-2">mdi-email</v-icon>
                            </v-btn>
                        </v-list-item>
                    </v-list>
                    <v-list v-if="editMode && report.id === item.id">
                        <v-list-item>
                            <v-btn depressed color="error" text @click="report = {}; editMode = false">Отмена</v-btn>
                        </v-list-item>
                        <v-list-item>
                            <v-btn depressed color="success" text @click="changeSale">Подтвердить</v-btn>
                        </v-list-item>
                    </v-list>
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
        <SaleEditModal
            :state="editModal"
            :report="report"
            @cancel="editModal = false; report = {}"
        />
    </v-card>
</template>

<script>
import ConfirmationModal from "@/components/Modal/ConfirmationModal";
import moment from 'moment';
import ReportCancelModal from "@/components/Modal/ReportCancelModal";
import ACTIONS from '@/store/actions/index';
import axios from 'axios';
import SaleEditModal from "@/components/Modal/SaleEditModal";

const DATE_FILTERS = {
        ALL_TIME: 1,
        CURRENT_MONTH: 2,
        TODAY: 3,
        CUSTOM_FILTER: 4,
        LAST_3_DAYS: 5,
    };

    const DATE_FORMAT = 'YYYY-MM-DD';

    export default {
        components: {SaleEditModal, ReportCancelModal, ConfirmationModal},
        data: () => ({
            manufacturerId: -1,
            discountFrom: 0,
            discountTo: 100,
            editModal: false,
            bookings: [],
            search: '',
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
                    name: 'Вчера',
                    value: [
                        moment().subtract(1, 'days').format(DATE_FORMAT),
                        moment().subtract(1, 'days').format(DATE_FORMAT),
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
                {text: 'Список проданных товаров', value: 'products', align: ' min-w-250 w-30'},
                {text: 'Дата', value: 'date', align: ' font-weight-black'},
                {text: 'Способ оплаты', value: 'payment_type_text', align: ' font-weight-bold'},
                {text: 'Данные', value: 'sale_data'},
                {text: 'Экономические показатели', value: 'economy'},
                {text: 'Дополнительные данные', value: 'additional_data'},
                {
                    text: 'Действие', value: 'action'
                },
                {text: 'Поиск', value: 'search', align: ' d-none'},
            ],
            searchComment: '',
        }),
        async mounted() {
            await this.init();
        },
        watch: {
            discountFrom(value) {
                this.$nextTick(() => {
                    if (this.discountFrom > 99) {
                        this.discountFrom = 100;
                    }
                    if (value.toString().length > 3) {
                        this.discountFrom = +(value.toString().slice(0, 3));
                    }
                    if (this.discountFrom < 0) {
                        this.discountFrom = 0;
                    }
                })
            },
            discountTo(value) {
                this.$nextTick(() => {
                    if (this.discountTo > 99) {
                        this.discountTo = 100;
                    }
                    if (value.toString().length > 3) {
                        this.discountTo = +(value.toString().slice(0, 3));
                    }
                    if (this.discountTo < 0) {
                        this.discountTo = 0;
                    }
                })
            },
            async manufacturerId (value) {
                this.$loading.enable();
                await this.loadReport();
                this.$loading.disable();
            }
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
                this.loading = true;
                await this.$store.dispatch(ACTIONS.GET_REPORTS, {
                    start: this.currentDate[0],
                    finish: this.currentDate[1],
                });
                await this.$store.dispatch('GET_PREORDERS_REPORT', {
                    start: this.currentDate[0],
                    finish: this.currentDate[1],
                })
                const { data } = await axios.get(`/api/v2/booking?start=${this.currentDate[0]}&finish=${this.currentDate[1]}`);
                this.bookings = data.data;
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
                this.loading = true;
                const dateObject = {
                    start: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.start : this.currentDate[0],
                    finish: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.finish : this.currentDate[1],
                };
                if (this.manufacturerId !== -1) {
                    dateObject.manufacturer_id = this.manufacturerId;
                }
                await this.$store.dispatch(ACTIONS.GET_REPORTS, dateObject);
                await this.$store.dispatch('GET_PREORDERS_REPORT', dateObject);
                const { data } = await axios.get(`/api/v2/booking?start=${dateObject.start}&finish=${dateObject.finish}`);
                this.bookings = data.data;
                this.overlay = false;
                this.loading = false;

            },
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);

                if (this.start && this.finish) {
                    await this.loadReport();
                }

            },
            async createWaybill(report) {
                this.$loading.enable();
                const _report = JSON.parse(JSON.stringify(report));
                const { data } = await axios.post(`/api/v2/documents/waybill`, {
                    cart: _report.products.map(r => {
                        r.attributes = r._attributes;
                        return r;
                    }),
                    organization: report.client.client_name,
                })
                const link = document.createElement('a');
                link.href = `${window.location.origin}/${data.path}`;
                link.click();
                this.$loading.disable();
            },
            async createInvoice(report) {
                this.$loading.enable();
                const _report = JSON.parse(JSON.stringify(report));
                const cart = _report.products.map(r => {
                    r.attributes = r._attributes;
                    return r;
                });
                const { data } = await axios.post(`/api/v2/documents/invoice`, {
                    cart,
                    organization: report.client.client_name,
                    contract: '',
                    location: '',
                    waybill: '',
                    consignee: '',
                    recipient: '',
                    IIK: '',
                    BINLocation: '',
                    product: cart.length > 1 ? 'Спортивные витамины в ассортименте'
                        : `${cart[0].product_name} ${cart[0].attributes.map(a => a.attribute_value).join(' ')} ${cart[0].manufacturer.manufacturer_name}`,
                })
                const link = document.createElement('a');
                link.href = `${window.location.origin}/${data.path}`;
                link.click();
                this.$loading.disable();
            },
            async sendTelegram(saleId) {
                this.$loading.enable();
                const response = await axios.get(`/api/sales/telegram/${saleId}`);
                this.$loading.disable();
            }
        },
        computed: {
            manufacturers() {
                return [
                    {
                        id: -1,
                        manufacturer_name: 'Все'
                    }, ...this.$store.getters.manufacturers];
            },
            is_admin() {
                return this.$store.getters.CURRENT_ROLE === 'admin';
            },
            is_seller() {
                return this.$store.getters.CURRENT_ROLE === 'seller';
            },
            sellers() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.users];
            },
            payment_types() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.payment_types, {id: -2, name: 'Оплачено сертификатом'}];
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
            _bookings() {
                return this.bookings.filter(b => {
                    if (this.currentSeller === -1) {
                        return b
                    } else {
                        return b.user.id === this.currentSeller;
                    }
                }).filter(s => {
                    if (this.currentCity === -1) {
                        return s;
                    } else {
                        return s.store.id === this.currentCity;
                    }
                })
            },
            bookingTotal() {
                return this._bookings.reduce((a, c) => {
                    return a + c.paid_sum
                }, 0)
            },
            totalSales() {
                return this._salesReport
                    .reduce((a, c) => {
                        if (this.currentType === -2) {
                            return a + c.certificate.amount;
                        }

                        const certificateAmount = c.certificate ? c.certificate.amount : 0;

                        if (c.split_payment === null || this.currentType === -1) {
                            let finalPrice =  a + c.final_price;
                            if (this.currentType !== -1) {
                                finalPrice -= certificateAmount;
                            }
                            return finalPrice;
                        }

                        else {
                            return a + +c.split_payment.find(s => s.payment_type == this.currentType).amount - certificateAmount;
                        }
                    }, 0) + this.bookingTotal;
            },
            totalMargin() {
                return this._salesReport
                    .reduce((a, c) => {
                        return a + c.margin
                    }, 0);
            },
            totalSaleCount() {
                return this._salesReport.length;
            },
            averageCheck() {
                return this.totalSaleCount === 0 ? 0 : this.totalSales / this.totalSaleCount;
            },
            salesReport() {
                return [...this.$store.getters.REPORTS, ...this.$store.getters.PREORDERS] || [];
            },
            _salesReport() {
                try {
                    return this.salesReport
                        .filter(s => {
                            return s.comment.toLowerCase().includes(this.searchComment.toLowerCase());
                        })
                        .filter(s => {
                            if (this.currentSeller === -1) {
                                return s
                            } else {
                                return s.user.id === this.currentSeller;
                            }
                        })
                        .filter(s => {
                            if (this.currentCity === -1) {
                                return s;
                            } else {
                                return s.store.id === this.currentCity;
                            }
                        })
                        .filter(s => {
                            if (this.currentType == -1) {
                                return s;
                            }

                            if (this.currentType === -2) {
                                return s.certificate;
                            }

                            else {
                                if (this.currentType == 5 && s.payment_type === 5) {
                                    return true;
                                }
                                if (s.split_payment !== null) {
                                    return s.split_payment.find(s => s.payment_type == this.currentType);
                                }
                                return s.payment_type == this.currentType;
                            }
                        })
                        .filter(s => {
                            if (this.currentStoreType === -1) {
                                return s;
                            }
                            return s.store_type == this.currentStoreType;
                        })
                        .map(s => {
                            if (!this.search) {
                                s.products = [...s._products];
                                return s;
                            }
                            s.products = [...s._products.filter(p => {
                                return p.product_name.toLowerCase().includes(this.search.toLowerCase());
                            })]
                            return s;
                        })
                        .filter(s => {
                            if (this.discountFrom === 0 && this.discountTo === 100) {
                                return true;
                            } else {
                                return s.products.some(i => {
                                    return i.discount >= this.discountFrom && i.discount <= this.discountTo;
                                });
                            }
                        });
                } catch (e) {
                    console.log(e)
                    return [];
                }

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

    .w-30 {
        width: 30%;
    }

    .v-data-table__mobile-row:last-child {
        display: none;
    }

    .v-data-table__mobile-row__header, .v-data-table__mobile-row__cell {
        flex: 1;
    }


</style>
