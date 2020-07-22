<template>
    <div>
        <v-card class="background-iron-darkgrey mb-5 mt-5"  v-if="!emptyCart">
            <v-card-title class="justify-end">
                <div>
                    <v-btn color="error" class="top-button mr-3" @click="wayBillModal = true;">
                        Сформировать счет на оплату
                    </v-btn>
                    <v-btn color="error" class="top-button" @click="confirmationModal = true;">
                        Сформировать накладную
                    </v-btn>
                </div>
            </v-card-title>
            <v-card-text style="padding: 0;">
                <v-simple-table v-slot:default class="mt-5">
                    <template>
                        <thead class="background-iron-darkgrey fz-18">
                        <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Атрибуты</th>
                            <th>Количество</th>
                            <th>Стоимость</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-iron-grey">
                        <tr v-for="(item, index) of cart" :key="item.id * 85">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.product_name }}</td>
                            <td>
                                <ul>
                                    <li v-for="(attr, index) of item.attributes" :key="index">
                                        {{ attr.attribute }}: {{ attr.attribute_value }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <v-btn icon color="error" @click="decreaseCartCount(index)">
                                    <v-icon>mdi-minus</v-icon>
                                </v-btn>
                                {{ item.count }} шт.
                                <v-btn icon color="success" @click="addToCart(item)">
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </td>
                            <td>{{ item.product_price }} ₸</td>
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
                        <thead class="background-iron-darkgrey fz-18">
                        <tr>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Склад</th>
                        </tr>
                        </thead>
                        <tbody class="background-iron-grey fz-18">
                        <tr>
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal }} ₸</td>
                            <td class="text-center">
                                <v-select
                                    :items="_stores"
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
                <div class="background-iron-grey pa-10">
                    <v-btn color="error" block style="font-size: 16px" @click="onTransfer">
                        Создать перемещение
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-iron-darkgrey">
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
                    <v-col cols="12" xl="8">
                        <v-text-field
                            class="mt-2"
                            v-model="search"
                            solo
                            clearable
                            label="Поиск товара"
                            single-line
                            hide-details
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" xl="4">
                        <v-select
                            :items="stores"
                            item-text="name"
                            v-model="storeFilter"
                            item-value="id"
                            label="Склад"
                        />
                    </v-col>
                </v-row>
                <v-data-table
                    class="background-iron-grey fz-18"
                    :search="search"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :items="products"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.attributes="{ item }">
                        <ul>
                            <li v-for="(attr, index) of item.attributes" :key="index">
                                {{ attr.attribute }}: {{ attr.attribute_value }}
                            </li>
                        </ul>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn icon @click="addToCart(item)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:item.quantity="{item}">
                        {{ getQuantity(item.quantity) - getCartCount(item.id) }}
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
        <ConfirmationModal
            :state="confirmationModal"
            message="Сформировать накладную?"
            :on-confirm="getWayBill"
        />
        <WayBillModal
            :state="wayBillModal"
            v-on:cancel="wayBillModal = false"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "../../components/Modal/ConfirmationModal";
    import WayBillModal from "../../components/Modal/WayBillModal";
    import showToast from "../../utils/toast";
    import {TOAST_TYPE} from "../../config/consts";
    import ACTIONS from "../../store/actions";
    import axios from 'axios';

    export default {
        components: {
            ConfirmationModal,
            WayBillModal
        },
        watch: {
            storeFilter() {
                this.cart = [];
            }
        },
        data: () => ({
            storeFilter: null,
            cart: [],
            search: '',
            confirmationModal: false,
            wayBillModal: false,
            child_store: 1,
            overlay: false,
            loading: false,
            headers: [
                {
                    text: 'Наименование',
                    value: 'product_name',
                    sortable: false,
                    align: ' fz-18'
                },
                {
                    text: 'Атрибуты',
                    value: 'attributes'
                },
                {
                    text: 'Остаток',
                    value: 'quantity'
                },
                {
                    text: 'Стоимость',
                    value: 'product_price'
                },
                {
                    text: 'Добавить',
                    value: 'actions'
                }
            ]
        }),
        async mounted() {
            this.loading = this.products.length === 0;
            await this.$store.dispatch(ACTIONS.GET_PRODUCT);
            await this.$store.dispatch(ACTIONS.GET_STORES);
            this.loading = false;
        },
        methods: {
            addToCart(item) {
                if (!this.checkAvailability(item)) {
                    showToast('Недостаточно товара', TOAST_TYPE.WARNING);
                    return;
                }
                const index = this.cart.map(c => c.id).indexOf(item.id);
                if (index === -1) {
                    this.cart.push({...item, count: 1});
                } else {
                    this.increaseCartCount(index);
                }
            },
            checkAvailability(item = {}) {
                return !((this.getQuantity(item.quantity) - this.getCartCount(item.id)) === 0);
            },
            increaseCartCount(index) {
                this.$set(this.cart[index], 'count', this.cart[index].count + 1);
            },
            decreaseCartCount(index) {
                this.$set(this.cart[index], 'count', Math.max(1, this.cart[index].count - 1))
            },
            onClientChosen(client) {
                this.clientCartModal = false;
                this.client = client;
            },
            async onTransfer() {
                this.overlay = true;
                const sale = {
                    cart: this.cart.map(c => {
                        return {id: c.id, count: c.count};
                    }),
                    parent_store_id: this.storeFilter,
                    user_id: this.user.id,
                    child_store_id: this.child_store,
                };

                await this.$store.dispatch(ACTIONS.MAKE_TRANSFER, sale);

                this.overlay = false;

                this.confirmationModal = true;

                showToast('Перемещение создано успешно!');
                this.cart = [];
            },
            async getWayBill() {
                this.confirmationModal = false;
                const { data } = await axios.post('/api/excel/transfer/waybill', {
                    child_store: this.child_store,
                    parent_store: this.storeFilter,
                    cart: this.cart,
                });

                const link = document.createElement('a');
                link.href = data.path;
                link.click();
            },
            getQuantity(quantity = []) {
                if (typeof quantity === 'number') {
                    return quantity;
                }
                if (!quantity.length) {
                    return 0;
                }
                return quantity
                    .filter(q => +q.store_id === +this.storeFilter)
                    .map(q => q.quantity)
                    .reduce((a, c) => {
                        return +a + +c;
                    }, 0)
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
            products() {
                return this.$store.getters.products;
            },
            emptyCart() {
                return !!!this.cart.length;
            },
            cartCount() {
                return this.cart
                    .map(c => c.count)
                    .reduce((a, c) => {
                        return a + c;
                    }, 0)
            },
            subtotal() {
                return this.cart.reduce((a, c) => {
                    return (c.product_price * c.count) + a;
                }, 0);
            },
            stores() {
                const stores = this.$store.getters.stores;
                if (stores.length > 0) {
                    this.storeFilter = stores[0].id;
                }
                return stores;
            },
            _stores() {
                const stores = this.stores.filter(s => s.id !== this.storeFilter);
                this.child_store = stores[0].id;
                return stores;
            },
            user() {
                return this.$store.getters.USER;
            }
        }
    }
</script>

<style scoped>
    * {
    }

    h5 {
        color: #fff;
        font-weight: 300;
        font-size: 18px;
    }

    .top-button {
        width: 340px;
    }

    .background-iron-grey {
        background-color: #444444;
    }

    .background-iron-darkgrey {
        background-color: #333333;
    }

    .margin-28 {
        margin-top: 28px;
    }

    .fz-18 th, td {
        font-size: 18px!important;
    }

    .v-data-table {
        font-size: 18px!important;
    }
</style>
