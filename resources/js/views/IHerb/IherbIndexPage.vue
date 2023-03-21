<template>
    <v-card>
        <v-card-title>
            Товары IHerb
        </v-card-title>
        <v-card-text>
            <v-row>
                <v-col cols="6"></v-col>
                <v-col cols="2">
                    <v-text-field
                        label="Курс"
                        type="number"
                        v-model="moneyRate"
                        hint="Курс закупочной валюты"
                    />
                </v-col>
                <v-col cols="2">
                    <v-text-field
                        label="Процент надбавки"
                        type="number"
                        v-model="ratio"
                        hint="На сколько прибавить стоимость по закупу"
                        append-icon="mdi-percent"
                    />
                </v-col>
                <v-col cols="2">
<!--                    <v-btn color="primary" class="mt-3" @click="calculateFinalPrice">
                        Расчет
                    </v-btn>-->
                    <v-btn
                        id="menu-activator"
                        color="primary"
                        @click="calculateFinalPrice(2)"
                    >
                        Расчет
                    </v-btn>

<!--                    <v-menu activator="#menu-activator">
                        <v-list>
                            <v-list-item
                                v-for="(item, index) in items"
                                :key="index"
                                :value="index"
                            >
                                <v-list-item-title @click="calculateFinalPrice(item.mode)">
                                    {{ item.title }}
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>-->
                </v-col>
            </v-row>
            <v-virtual-scroll
                height="300"
                item-height="64"
                :items="cart"
                v-if="cart.length"
            >
                <template v-slot:default="{ item, index }">
                    <v-row style="max-width: 100%;">
                        <v-col cols="4">
                            <v-list class="product__list" flat>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.product_name }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }} | {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-col>
                        <v-col cols="3">
                            <v-text-field
                                :disabled="item.is_locked"
                                label="Стоимость"
                                type="number"
                                v-model="item.product_price" />
                        </v-col>
                        <v-col cols="3">
                            <v-text-field
                                :disabled="item.is_locked"
                                label="Итоговая стоимость (в рублях)"
                                type="number"
                                v-model="item.product_price_rub"
                                :append-outer-icon="item.is_locked ? 'mdi-lock' : ''"
                            />
                        </v-col>
<!--                        <v-col cols="2">
                            <v-text-field
                                label="Итоговая стоимость (в тенге)"
                                type="number"
                                v-model="item.product_price"
                            />
                        </v-col>-->
                        <v-col cols="2">
                            <div style="width: 100%" class="d-flex justify-center">
                                <v-btn icon color="success" title="Зафиксировать цену" @click="lockPrice(item, index)">
                                    <v-icon>mdi-lock</v-icon>
                                </v-btn>
                                <v-btn icon color="error" @click="deleteList(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </div>
                        </v-col>
                    </v-row>
                </template>
            </v-virtual-scroll>
            <div v-if="cart.length">
                <v-btn block color="primary" class="my-2" @click="loadReports" :disabled="!cart.length">
                    Загрузить прайс
                </v-btn>
                <v-btn block color="error" class="my-2" @click="updatePrices" :disabled="!cart.length">
                    Обновить цены
                </v-btn>
            </div>
            <v-text-field
                class="mt-2"
                v-on:input="searchInput"
                v-model="searchValue"
                solo
                clearable
                label="Поиск товара"
                single-line
                hide-details
            ></v-text-field>
            <v-row class="d-flex align-center">
                <v-col cols="12" xl="4">
                    <v-autocomplete
                        :items="categories"
                        item-text="name"
                        v-model="categoryId"
                        item-value="id"
                        label="Категория"
                    />
                </v-col>
                <v-col cols="12" xl="4">
                    <v-autocomplete
                        :items="manufacturers"
                        item-text="manufacturer_name"
                        v-model="manufacturerId"
                        item-value="id"
                        label="Бренд"
                    />
                </v-col>
                <v-col cols="12" xl="4">
                    <v-btn color="success" @click="chooseAllProduct">
                        Выбрать текущие товары <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </v-col>
                <v-col cols="12" xl="4">
                    <v-checkbox
                        label="Скрыть отсутствующие"
                        v-model="hideNotInStock"
                    />
                </v-col>
            </v-row>
            <v-data-table
                class="background-iron-grey fz-18"
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="product_headers"
                :search="searchQuery"
                loading-text="Идет загрузка товаров..."
                :items="products"
                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
            >
                <template v-slot:item.product_name="{item}">
                    <v-list flat>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.product_name }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }} | {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.product_price="{item}">
                   {{ item.product_price | priceFilters }}
                </template>
                <template v-slot:item.product_price_rub="{item}">
                    <span v-if="item.product_price_rub">
                        {{ item.product_price_rub | priceFiltersRub }}
                    </span>
                    <span v-else>
                        Не установлена
                    </span>
                </template>
                <template v-slot:item.actions="{item}">
                    <v-btn depressed icon color="success" @click="addToList(item)">
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                </template>
            </v-data-table>
        </v-card-text>
        <v-card-text v-if="productLoaded">
            <v-row>
                <v-col cols="12" xl="3" justify="center">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма продаж:</v-list-item-title>
                                <v-list-item-title>{{ totalSales | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="is_admin || IS_BOSS">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма прибыли:</v-list-item-title>
                                <v-list-item-title>{{ totalMargin | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-col>
            </v-row>
            <v-data-table
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
                :loading="loading"
                loading-text="Отчеты обновляются"
                :items="report"
                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
            >
                <template v-slot:item.product_name="{item}">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.product_name }}</v-list-item-title>
                                <v-list-item-subtitle>{{ item.attributes }}<span v-if="item.manufacturer">,</span> {{ item.manufacturer }}</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.total_purchase_price="{item}">
                    {{ item.total_purchase_price | priceFilters }}
                </template>
                <template v-slot:item.total_product_price="{item}">
                    {{ item.total_product_price | priceFilters }}
                </template>
                <template v-slot:item.margin="{item}">
                    {{ item.margin | priceFilters }}
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
import ConfirmationModal from "@/components/Modal/ConfirmationModal";
import moment from 'moment';
import ReportCancelModal from "@/components/Modal/ReportCancelModal";
import ACTIONS from '@/store/actions/index';
import product_search from "@/mixins/product_search";
import axios from 'axios';
import axiosClient from '@/utils/axiosClient';
import { db } from '@/db';

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
    mixins: [product_search],
    data: () => ({
        items: [
            {
                title: 'Все стоимости',
                mode: 1,
            },
            {
                title: 'Только рубли',
                mode: 2,
            },
            {
                title: 'Только тенге',
                mode: 3,
            }
        ],
        moneyRate: 1,
        ratio: 100,
        cart: [],
        overlay: false,
        loading: false,
        productLoaded: false,
        cancelModal: false,
        purchaseId: null,
        currentProducts: [],
        currentStoreType: -1,
        startMenu: null,
        report: [],
        editMode: false,
        start: null,
        finishMenu: null,
        today: moment(),
        finish: null,
        currentDate:  [
            moment().startOf('month').format(DATE_FORMAT),
            moment().format(DATE_FORMAT),
        ],
        currentCity: -1,
        currentSeller: -1,
        currentType: -1,
        reports: [],
        hideNotInStock: false,
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
            {text: 'Товар', value: 'product_name', align: ' min-w-250 w-30'},
            {text: 'Количество', value: 'count', align: ' font-weight-black'},
            {text: 'Общая закупочная', value: 'total_purchase_price'},
            {text: 'Общая продажная', value: 'total_product_price'},
            {text: 'Прибыль', value: 'margin'},
        ],
        product_headers: [
            {
                text: 'Наименование',
                value: 'product_name',
                sortable: false,
                align: ' fz-18'
            },
            {
                value: 'manufacturer.manufacturer_name',
                text: 'Производитель',
                align: ' d-none'
            },
            {
                text: 'Стоимость (тенге)',
                value: 'product_price'
            },
            {
                text: 'Стоимость (рубли)',
                value: 'product_price_rub'
            },
            {
                text: 'Количество',
                value: 'total_quantity'
            },
            {
                text: 'Добавить',
                value: 'actions'
            },
            {
                text: 'Штрих-код',
                value: 'product_barcode',
                align: ' d-none'
            }
        ],
        categoryId: -1,
        manufacturerId: -1,
    }),
    async mounted() {
        await this.$store.dispatch('GET_IHERB_PRODUCTS');
        await this.$store.dispatch('GET_MAIN_STORE_QUANTITIES');
        await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
        await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        await this.init();
    },
    methods: {
        async lockPrice (item, key) {
            this.$set(this.cart, key, {
                ...this.cart[key],
                is_locked: !this.cart[key].is_locked
            })
        },
        async updatePrices () {
            const products = this.cart.filter(c => c.is_locked).map(i => ({
                product_id: i.product_id,
                product_price_rub: i.product_price_rub,
                product_price: i.product_price,
            }));

            await axiosClient.patch('/v2/products/iherb/price', { products });
            await this.$store.dispatch('GET_IHERB_PRODUCTS');
        },
        async chooseAllProduct() {
            this.products.forEach(item => {
                this.addToList(item);
            })
        },
        async loadReports() {
            this.$loading.enable();
            try {
                const { data } = await axios.post(`/api/v2/documents/iherb`, {
                    cart: this.cart,
                });
                const link = document.createElement('a');
                link.href = `${window.location.origin}/${data.path}`;
                link.click();
            } catch (e) {
                console.log(e);
                this.$toast.error('Произошла ошибка при создании прайса')
            } finally {
                this.$loading.disable();
            }
        },
        async calculateFinalPrice (mode) {
            this.cart = this.cart.map(item => {
                if (item.is_locked) {
                    return item;
                }
               const price = item.base_price / this.moneyRate;
               if (mode === 1 || mode === 2) {
                   item.product_price_rub = Math.ceil(price * (this.ratio / 100));
               }
               if (mode === 1 || mode === 3) {
                   item.product_price = Math.ceil(item.base_price * (this.ratio / 100));
               }
               return item;
            });
        },
        async addToList(item) {
            const findIndex = this.cart.findIndex(p => p.id === item.id);
            if (findIndex === -1) {
                this.cart.push({
                    ...item,
                    tenge_price: item.product_price,
                    is_locked: false,
                });
            }
        },
        async deleteList(key) {
            this.cart.splice(key, 1);
        },
        closeModal() {
            this.currentProducts = [];
            this.purchaseId = null;
            this.cancelModal = false;
        },
        async init() {
            this.loading = true;
            this.overlay = this.loading = false;
            const response = await db.iherb.toArray();
            if (response && response.length > 0) {
                this.cart = [...response[0].cart];
                await db.iherb.clear();
            }
            setInterval(async () => {
                await db.iherb.clear();
                db.iherb.add({
                    cart: this.cart,
                })
            }, 2000)
        },
        async onConfirm() {
            this.closeModal();
        },
    },
    computed: {
        is_admin() {
            return this.$store.getters.CURRENT_ROLE === 'admin';
        },
        products() {
            let products = this.$store.getters.IHERB_PRODUCTS;
            if (this.manufacturerId !== -1) {
                products = products.filter(product => product.manufacturer.id === this.manufacturerId);
            }
            if (this.categoryId !== -1) {
                products = products.filter(product => product.category.id === this.categoryId);
            }
            if (this.hideNotInStock) {
                products = products.filter(p => p.total_quantity > 0);
            }
            return products;
        },
        is_seller() {
            return this.$store.getters.CURRENT_ROLE === 'seller';
        },
        sellers() {
            return [{id: -1, name: 'Все'}, ...this.$store.getters.users];
        },
        shops() {
            return [{id: -1, name: 'Все'}, ...this.$store.getters.shops];
        },
        store_types() {
            return [{id: -1, type: 'Все'}, ...this.$store.getters.store_types];
        },
        manufacturers() {
            return [
                {
                    id: -1,
                    manufacturer_name: 'Все'
                }, ...this.$store.getters.manufacturers];
        },
        categories() {
            return [
                {
                    id: -1,
                    name: 'Все'
                }, ...this.$store.getters.categories
            ];
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


</style>
