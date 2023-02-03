<template>
    <v-card>
        <v-card-title>
            Отчеты по продаже №{{ $route.params.id }}
        </v-card-title>
        <v-card-text>
            <v-data-table
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
                :loading="loading"
                loading-text="Отчеты обновляются"
                :items="reports"
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
                                <v-list-item-title>
                                    {{ item.client.client_name }}
                                    <v-btn :to="`/clients/${item.client.id}`" icon text target="_blank" v-if="item.client.id !== -1">
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                </v-list-item-title>
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
                        <v-list-item v-if="item.is_kaspi_red">
                            <v-list-item-content>
                                <v-list-item-title>{{ item.kaspi_red_commission | priceFilters }}</v-list-item-title>
                                <v-list-item-subtitle>Комиссия RED</v-list-item-subtitle>
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
                        <v-list-item v-if="!item.is_confirmed">
                            <v-list-item-content>
                                <v-list-item-title>
                                    <v-icon color="success">
                                        mdi-check
                                    </v-icon>
                                </v-list-item-title>
                                <v-list-item-subtitle>Ждет подтверждения</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
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
                        <v-list-item v-if="item.promocode.id">
                            <v-list-item-content>
                                <v-list-item-title style="white-space: normal;">{{ item.promocode.promocode }}</v-list-item-title>
                                <v-list-item-subtitle style="white-space: normal;">Промокод</v-list-item-subtitle>
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
                        <v-btn color="primary" :href="'/print/check/' + item.id" target="_blank">
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
                    <div v-if="item.is_confirmed">
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
                            <v-list-item v-if="!item.is_full_wholesale_purchase && IS_SUPERUSER">
                                <v-btn small depressed color="primary" text @click="markAsOpt(item)">
                                    Сделать оптовой <v-icon class="ml-2">mdi-check</v-icon>
                                </v-btn>
                            </v-list-item>
                            <v-expansion-panels style="min-width: 284px;" flat>
                                <v-expansion-panel>
                                    <v-expansion-panel-header ripple>
                                        <span
                                            class="text-button"
                                            style="
                                            padding-left: 5px;
                                            font-size: 12px!important;
                                            color: #43a047;"
                                        >
                                            Документы
                                        </span>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-list-item>
                                            <v-btn small depressed color="success" text :href="'/print/check/' + item.id" target="_blank">
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
                                        <v-list-item>
                                            <v-btn small depressed color="success" text @click="createPaymentInvoice(item)">
                                                Счет на оплату <v-icon class="ml-2">mdi-printer</v-icon>
                                            </v-btn>
                                        </v-list-item>
                                        <v-list-item v-if="IS_SUPERUSER">
                                            <v-btn small depressed color="success" text @click="sendTelegram(item.id)">
                                                Отправить в телегу <v-icon class="ml-2">mdi-email</v-icon>
                                            </v-btn>
                                        </v-list-item>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-list>
                        <v-list v-if="editMode && report.id === item.id">
                            <v-list-item>
                                <v-btn depressed color="error" text @click="report = {}; editMode = false">Отмена</v-btn>
                            </v-list-item>
                            <v-list-item>
                                <v-btn depressed color="success" text @click="changeSale">Подтвердить</v-btn>
                            </v-list-item>
                        </v-list>
                    </div>
                    <v-list v-else>
                        <v-list-item>
                            <v-btn small depressed color="error" text @click="purchaseId = item.id; currentProducts = [...item.products]; cancelModal = true;">
                                Отмена <v-icon class="ml-2">mdi-cancel</v-icon>
                            </v-btn>
                        </v-list-item>
                        <v-list-item>
                            <v-btn small depressed color="success" text @click="report = {...item}; wholeSaleConfirmationModal = true;">
                                Подтвердить <v-icon class="ml-2">mdi-check</v-icon>
                            </v-btn>
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
        <WholeSaleConfirmation
            :state="wholeSaleConfirmationModal"
            :report="report"
            @cancel="wholeSaleConfirmationModal = false; report = {}"
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
import WholeSaleConfirmation from '@/components/Modal/WholeSaleConfirmation';
import {__deepClone} from '@/utils/helpers';
import axiosClient from '@/utils/axiosClient';

const DATE_FILTERS = {
    ALL_TIME: 1,
    CURRENT_MONTH: 2,
    TODAY: 3,
    CUSTOM_FILTER: 4,
    LAST_3_DAYS: 5,
};

const DATE_FORMAT = 'YYYY-MM-DD';

export default {
    components: {WholeSaleConfirmation, SaleEditModal, ReportCancelModal, ConfirmationModal},
    data: () => ({
        reports: [],
        currentReport: null,
        searchPromocode: '',
        showOnlyUnconfirmed: false,
        wholeSaleConfirmationModal: false,
        manufacturerId: -1,
        editModal: false,
        bookings: [],
        search: '',
        overlay: false,
        loading: false,
        cancelModal: false,
        purchaseId: null,
        currentProducts: [],
        report: {},
        editMode: false,
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
    }),
    async mounted() {
        await this.init();
    },
    methods: {
        async markAsOpt (item) {
            this.$loading.enable();
            await this.$store.dispatch('updateSale', {
                id: item.id,
                is_opt: true
            });
            this.$loading.disable();
        },
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
            this.$loading.enable();
            const { data: { data } } = await axiosClient.get(`reports/${this.$route.params.id}`);
            this.reports.push(data);
            this.$loading.disable();
        },
        async onConfirm() {
            this.closeModal();
        },
        printCheck(id) {
            window.open(`/check/${id}`, '_blank');
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
        async createPaymentInvoice (report) {
            this.$loading.enable();
            const _report = __deepClone(report);
            const cart = _report.products.map(r => {
                r.attributes = r._attributes;
                return r;
            });
            const { data } = await axios.post(`/api/v2/documents/invoice-payment`, {
                cart,
                customer: report.client.client_name,
            })
            const link = document.createElement('a');
            link.href = `${window.location.origin}/${data.path}`;
            link.click();
            this.$loading.disable();
        },
        async sendTelegram(saleId) {
            this.$loading.enable();
            await axios.get(`/api/sales/telegram/${saleId}`);
            this.$loading.disable();
        }
    },
    computed: {
        is_admin() {
            return this.$store.getters.CURRENT_ROLE === 'admin';
        },
        is_seller() {
            return this.$store.getters.CURRENT_ROLE === 'seller';
        },
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
