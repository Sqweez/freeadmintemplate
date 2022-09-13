<template>
    <div>
        <div class="d-flex align-center">
            <v-btn color="error" @click="showProductModal()">Добавить товар
                <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-btn color="error" class="top-button" @click="wayBillModal = true;" style="margin-left: 10px;">
                Сформировать накладную
            </v-btn>
            <v-btn color="primary" class="top-button" @click="clearCache" style="margin-left: 10px;">
                Сбросить кэш
            </v-btn>

            <div style="margin-left: auto; max-width: 200px;">
                <v-text-field
                    label="Курс валюты"
                    v-model="moneyRate"
                    type="number"
                />
                <v-btn color="primary" style="margin-left: auto" @click="calculatePrices">
                    Расчитать цены
                </v-btn>
            </div>
        </div>
        <v-card class="background-tooman-darkgrey mb-5 mt-5" v-if="!emptyCart">
            <v-card-title class="justify-end">
            </v-card-title>
            <v-card-text style="padding: 0;">
                <v-simple-table v-slot:default class="mt-5">
                    <template>
                        <thead class="background-tooman-darkgrey fz-18">
                        <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Количество</th>
                            <th>Базовая стоимость</th>
                            <th>Закупочная стоимость</th>
                            <th>Расчетный закуп</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
                        <tr v-for="(item, index) of cart" :key="item.id * 85">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                                    item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>
                                <div class="d-flex align-center">
                                    <v-btn icon color="error" @click="decreaseCartCount(index)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <v-text-field
                                        v-model.number="item.count"
                                        type="number"
                                        style="min-width: 60px; max-width: 60px; text-align: center"
                                        @input="changeCount($event, item, index)"
                                        @change="changeCount($event, item, index)"
                                    ></v-text-field>
                                    шт.
                                    <v-btn icon color="success" @click="increaseCartCount(index)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>

                            </td>
                            <td>{{ item.product_price | priceFilters}}</td>
                            <td>
                                <v-text-field
                                    label="Закупочная стоимость"
                                    type="number"
                                    @change="updatePurchasePrice($event, index)"
                                    @input="updatePurchasePrice($event, index)"
                                    v-model="item.purchase_price_initial"
                                ></v-text-field>
                            </td>
                            <td>
                                <v-list flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.purchase_price | priceFilters}}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Расчетный закуп по курсу
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ deliverySurcharge | priceFilters}}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Наценка за доставку
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ deliverySurcharge + item.purchase_price | priceFilters}}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Итоговая закупочная
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <span
                                                    :class="$economy.getMarginLevel(item.product_price, deliverySurcharge + item.purchase_price)"
                                                >
                                                    {{ $economy.getMarginPercentage(item.product_price, deliverySurcharge + item.purchase_price, ) }}%
                                                </span>
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Текущая маржинальность
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <span
                                                    :class="$economy.getSurchargeLevel(item.product_price, deliverySurcharge + item.purchase_price)"
                                                >
                                                    {{ $economy.getSurchargePercentage(item.product_price, deliverySurcharge + item.purchase_price) }}%
                                                </span>
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Текущая наценка
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>

                            </td>
                            <td>
                                <v-btn icon color="error" @click="deleteFromCart(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="background-tooman-darkgrey fz-18">
                        <tr>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Склад</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr>
                            <td class="text-center">{{ totalCost | priceFilters }}</td>
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center" style="max-width: 300px; min-width: 300px;">
                                <v-select
                                    :items="stores"
                                    item-text="name"
                                    v-model="child_store"
                                    item-value="id"
                                    label="Склад"
                                />
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div class="px-4">
                    <v-row>
                        <v-col>
                            <v-text-field
                                type="date"
                                label="Ожидаемая дата поступления"
                                v-model="arrivedAt"/>
                        </v-col>
                        <v-col>
                            <v-text-field
                                type="number"
                                label="Стоимость доставки"
                                v-model.number="paymentCost"
                            />
                        </v-col>
                        <v-col>
                            <v-text-field
                                label="Комментарий"
                                v-model="comment"/>
                        </v-col>
                    </v-row>


                </div>
                <div class="background-tooman-grey pa-10">
                    <v-btn color="error" block style="font-size: 16px" @click="onSubmit">
                        Создать поставку
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-tooman-darkgrey">
            <v-card-title>
                Товары
            </v-card-title>
            <v-card-text v-if="loading">
                <div
                    class="text-center d-flex align-center justify-center"
                    style="min-height: 651px">
                    <v-progress-circular
                        indeterminate
                        size="65"
                        color="primary"
                    ></v-progress-circular>
                </div>
            </v-card-text>
            <v-card-text style="padding: 0;" v-if="!loading">
                <v-row>
                    <v-col cols="12" xl="12">
                        <v-text-field
                            class="mt-2"
                            v-model="searchValue"
                            @input="searchInput"
                            solo
                            clearable
                            label="Поиск товара"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="6">
                        <v-autocomplete
                            :items="categories"
                            item-text="name"
                            v-model="categoryId"
                            item-value="id"
                            label="Категория"
                        />
                    </v-col>
                    <v-col cols="12" xl="6">
                        <v-autocomplete
                            :items="manufacturers"
                            item-text="manufacturer_name"
                            v-model="manufacturerId"
                            item-value="id"
                            label="Бренд"
                        />
                    </v-col>
                </v-row>
                <v-data-table
                    class="background-tooman-grey fz-18"
                    :search="searchQuery"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
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
                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                            item.manufacturer.manufacturer_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.product_price="{ item }">
                        {{ getPrice(item, child_store) | priceFilters }}
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn icon @click="addToCart(item)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-btn icon @click="addToCart(item, true)" color="success">
                            +1
                        </v-btn>
                        <v-btn color="success" outlined v-if="item.sku_can_be_created"
                               @click="showProductSkuModal(item.id)">
                            Ассортимент
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <ProductModal
            @cancel="$store.commit('modals/closeProductModal')"
        />
        <SkuModal
            @cancel="$store.commit('modals/closeProductSkuModal')"
        />
        <ConfirmationModal
            message="Распечатать накладную?"
            :state="wayBillModal"
            :on-confirm="getWayBill"
        />
    </div>
</template>

<script>
import ProductModal from "@/components/v2/Modal/ProductModal";
import ConfirmationModal from "@/components/Modal/ConfirmationModal";
import WayBillModal from "@/components/Modal/WayBillModal";
import ACTIONS from "@/store/actions";
import {PRODUCT_MODAL_EVENTS} from "@/config/consts";
import axios from "axios";
import {createArrival} from "@/api/arrivals";
import product_search from "@/mixins/product_search";
import cart from "@/mixins/cart";
import SkuModal from "@/components/v2/Modal/SkuModal";
import { db } from '@/db';
import product from '@/mixins/product';
import {mapActions} from 'vuex';

export default {
    components: {
        SkuModal,
        ConfirmationModal,
        WayBillModal,
        ProductModal
    },
    mixins: [product_search, cart, product],
    data: () => ({
        comment: '',
        arrivedAt: null,
        moneyRate: 1,
        paymentCost: 0,
        hideNotInStock: false,
        cart: [],
        search: '',
        confirmationModal: false,
        wayBillModal: false,
        child_store: 1,
        overlay: false,
        loading: false,
        productModal: false,
        headers: [
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
                text: 'Стоимость',
                value: 'product_price'
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
        productId: -1,
        rangeMode: false,
    }),
    async mounted() {
        this.loading = this.products.length === 0;
        await this.$store.dispatch('GET_PRODUCTS_v2');
        await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
        await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
        await this.$store.dispatch(ACTIONS.GET_ATTRIBUTES);
        const response = await db.arrivals.toArray();
        if (response && response.length > 0) {
            const arrival = response[0];
            this.cart = arrival.products;
            this.child_store = arrival.child_store;
            this.comment = arrival.comment;
            this.arrivedAt = arrival.arrivedAt;
            this.paymentCost = arrival.paymentCost;
            this.moneyRate = arrival.moneyRate;
        }
        this.loading = false;
        setInterval(async () => {
            await db.arrivals.clear();
            db.arrivals.add({
                products: this.cart,
                child_store: this.child_store,
                comment: this.comment,
                arrivedAt: this.arrivedAt,
                paymentCost: this.paymentCost,
                moneyRate: this.moneyRate
            });
        }, 5000);
    },
    methods: {
        ...mapActions({
            '$createArrival': 'createArrival',
        }),
        clearCache() {
            db.arrivals.clear()
        },
        changeCount(e, item, index) {
            this.$nextTick(() => {
                this.$set(this.cart[index], 'count', Math.max(1, Math.min(10000, e)))
            });
        },
        async showProductModal(id = null, action = PRODUCT_MODAL_EVENTS.ADD_PRODUCT) {
            if (id !== null) {
                this.productId = id;
                await this.$store.dispatch('GET_PRODUCT_v2', id);
            }
            return this.$store.commit('modals/showProductModal', {
                id, action
            });
        },
        async showProductSkuModal(id = null, edit = false) {
            if (id === null) {
                return false
            }
            await this.$store.dispatch('GET_PRODUCT_v2', id);
            return this.$store.commit('modals/showProductSkuModal', {
                id, edit
            });
        },
        updatePurchasePrice(e, index) {
            this.$nextTick(() => {
                this.$set(this.cart[index], 'purchase_price_initial', Math.max(0, Math.min(999999999, +e)));
                this.$set(this.cart[index], 'purchase_price', +e * this.moneyRate);
            })
        },
        calculatePrices() {
            this.cart = this.cart.map(item => {
                item.purchase_price = Math.ceil(item.purchase_price_initial * this.moneyRate);
                return item;
            })
        },
        addToCart(item, merge = false) {
            const index = this.cart.map(c => c.id).indexOf(item.id);
            if (index === -1 || merge) {
                this.cart.push({
                    ...item,
                    count: 1,
                    product_price: this.getPrice(item, this.child_store),
                    purchase_price_initial: 0,
                    purchase_price: 0,
                    uuid: Math.random()
                });
            } else {
                this.increaseCartCount(index);
            }
        },
        increaseCartCount(index) {
            this.$set(this.cart[index], 'count', this.cart[index].count + 1);
        },
        decreaseCartCount(index) {
            this.$set(this.cart[index], 'count', Math.max(1, this.cart[index].count - 1))
        },
        async getWayBill() {
            this.wayBillModal = false;
            const {data} = await axios.post('/api/excel/transfer/waybill', {
                child_store: this.storeFilter,
                parent_store: this.storeFilter,
                cart: this.cart,
            });
            const link = document.createElement('a');
            link.href = data.path;
            link.click();
        },
        async onSubmit() {
            if (!this.paymentCost) {
                return this.$toast.error('Введите стоимость доставки!');
            }

            const arrival = {
                products: this.cart.map(c => ({
                    id: c.id,
                    count: c.count,
                    purchase_price: c.purchase_price,
                })),
                store_id: this.child_store,
                user_id: this.user.id,
                comment: this.comment,
                arrived_at: this.arrivedAt,
                payment_cost: this.paymentCost,
            };

            try {
                this.$loading.enable('Поступление создается...');
                await this.$createArrival(arrival);
                await db.arrivals.clear();
                this.$toast.success('Поступление создано успешно!');
                this.cart = [];
                this.comment = '';
                this.arrivedAt = null;
                this.paymentCost = 0;
            } catch (e) {
                this.$toast.error('При создании поступления произошла ошибка!');
            } finally {
                this.$loading.disable();
            }
        },
        getCartCount(id) {
            const index = this.cart.map(c => c.id).indexOf(id);
            if (index === -1) {
                return 0;
            }
            return this.cart[index].count;
        },
        deleteFromCart(index) {
            this.cart.splice(index, 1);
        }
    },
    computed: {
        _stores() {
            const stores = this.stores.filter(s => s.id !== this.storeFilter);
            this.child_store = stores[0].id;
            return stores;
        },
        totalCost() {
            return this.cart.reduce((a, c) => {
                return a + (+c.count * +c.purchase_price);
            }, 0);
        },
        deliverySurcharge () {
            const totalCount = this.cart.reduce((a, c) => {
                return a + c.count;
            }, 0);
            return Math.ceil(this.paymentCost / totalCount);
        }
    },
    watch: {
        child_store () {
            this.cart = this.cart.map(c => {
                return {
                    ...c,
                    product_price: this.getPrice(c, this.child_store)
                }
            });
        },
        moneyRate(value) {
            this.cart = this.cart.map(item => {
                item.purchase_price = item.purchase_price_initial * value;
                return item;
            });
            this.$nextTick(() => {
                this.moneyRate = Math.max(0, Math.min(100000, +value));
            });
        },
        paymentCost (value) {
            this.$nextTick(() => {
                this.paymentCost = value;
            })
        },
    }
}
</script>

<style scoped>
</style>
