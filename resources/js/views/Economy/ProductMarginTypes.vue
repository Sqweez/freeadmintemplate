<template>
    <div>
        <v-card v-if="!emptyCart">
            <v-card-title class="justify-space-between">
                <span>Корзина</span>
            </v-card-title>
            <v-card-text style="padding: 0;">
                <v-divider></v-divider>
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header>
                            Товары ({{ cart.length }})
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-virtual-scroll :items="cart" height="400" item-height="60">
                                <template v-slot:default="{ item, index }">
                                    <v-list-item style="border-bottom: 1px solid mintcream">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                                    item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                        <v-list-item-action>
                                            <v-btn color="error" icon @click="deleteFromCart(index)">
                                                <v-icon>
                                                    mdi-close
                                                </v-icon>
                                            </v-btn>
                                        </v-list-item-action>
                                    </v-list-item>
                                </template>
                            </v-virtual-scroll>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <div class="px-4 d-flex">
                    <v-row>
                        <v-col cols="3">
                            <v-select
                                label="Тип маржинальности"
                                :items="marginTypes"
                                item-value="id"
                                item-text="title"
                                v-model="currentMarginType"
                            />
                        </v-col>
                        <v-col cols="3">
                            <v-btn color="success" class="px-4 mt-2" @click="onSave">
                                Сохранить <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-iron-darkgrey">
            <v-card-title>
                Товары
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" xl="10">
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
                    </v-col>
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
                        <v-btn color="success" @click="addToCartAll">
                            Добавить все <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
                <v-data-table
                    class="background-iron-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    @current-items="getFiltered"
                    :headers="headers"
                    :loading="loading"
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
                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                            item.manufacturer.manufacturer_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn depressed icon @click="addToCart(item)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:item.quantity="{item}">
                        {{ item.quantity - getCartCount(item.id) }}
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
import ACTIONS from "@/store/actions";
import {mapActions} from 'vuex';
import axios from "axios";
import product from "@/mixins/product";
import product_search from "@/mixins/product_search";
import cart from "@/mixins/cart";
import shiftModule from "@/store/modules/shifts";
import store from "@/store";

export default {
    components: {
    },
    async created() {
        this.$loading.enable();
        await this.$store.dispatch('GET_PRODUCTS_v2');
        const store_id = (this.is_admin || this.IS_BOSS) ? null : this.user.store_id;
        await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
        await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
        await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        this.loading = false;
        this.$loading.disable();
    },
    watch: {
        storeFilter() {
            this.cart = [];
        },
    },
    mixins: [product, product_search, cart],
    data: () => ({
        showCart: false,
        hideNotInStock: false,
        waybillModal: false,
        invoiceModal: false,
        invoicePaymentModal: false,
        certificateModal: false,
        productCheckModal: false,
        loading: true,
        cart: [],
        certificate: null,
        used_certificate: null,
        isRed: false,
        isFree: false,
        isSplitPayment: false,
        splitPayment: [],
        payment_type: 0,
        promocodeSet: false,
        partner_id: null,
        discountPercent: 0,
        promocode: "",
        clientCartModal: false,
        confirmationModal: false,
        client: null,
        overlay: false,
        sale_id: null,
        balance: 0,
        showCheckModal: false,
        currentItems: [],
        currentMarginType: 1,
        headers: [
            {
                text: 'Наименование',
                value: 'product_name',
                sortable: false,
                align: ' fz-18'
            },
            {
                text: 'Атрибуты',
                value: 'attributes',
                align: ' d-none'
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
                text: 'Текущий тип',
                value: 'margin_type.title'
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
        ]
    }),
    methods: {
        ...mapActions([
            ACTIONS.GET_PRODUCTS_v2,
            ACTIONS.GET_CLIENTS,
            ACTIONS.GET_STORES,
        ]),
        async onSave() {
            const products = this.cart
                .map(c => c.id)
            const payload = {
                margin_type: this.currentMarginType,
                products,
            };
            await this.$store.dispatch(ACTIONS.SET_MARGIN_TYPES, payload);
            this.cart = [];
            this.$toast.success('Тип маржинальности установлен!')
        },
        async createCertificate(certificate) {
            this.certificateModal = false;
            try {
                this.$loading.enable();
                const {data} = await axios.post(`/api/v2/certificates`, certificate);
                this.certificate = data;
            } catch (e) {
                this.$toast.error('При создании сертификата произошла ошибка');
            } finally {
                this.$loading.disable();
            }
        },
        getFiltered(e) {
            if (e.length === 1 && e[0].product_barcode === this.searchQuery) {
                this.addToCart(e[0], false);
                this.searchQuery = "";
                this.searchValue = "";
            }
        },
        toggleInput(index) {
            this.$set(this.cart[index], 'inputMode', !this.cart[index].inputMode);
        },
        changeCount(item, index) {
            this.$set(this.cart[index], 'count', Math.min(this.cart[index]._count, item.quantity));
            this.toggleInput(index);
        },
        checkAvailability(item = {}) {
            return !((this.getQuantity(item.quantity) - this.getCartCount(item.id)) === 0);
        },
        addToCartAll() {
            this.cart = this.products.map(product => {
                return {
                    ...product, count: 1, product_price: product.product_price, discount: 0, uuid: Math.random()
                }
            });
        }
    },
    computed: {
        is_admin() {
            return this.$store.getters.IS_ADMIN;
        },
        marginTypes () {
            return this.$store.getters.MARGIN_TYPES;
        },
        products() {
            let products = this.$store.getters.PRODUCTS_v2;
            if (this.manufacturerId !== -1) {
                products = products.filter(product => product.manufacturer.id === this.manufacturerId);
            }
            if (this.hideNotInStock) {
                products = products.filter(product => product.quantity > 0);
            }
            if (this.categoryId !== -1) {
                products = products.filter(product => product.category.id === this.categoryId);
            }
            return products.map(p => {
                p.quantity = 10000;
                return p;
            });
        },
        shops() {
            return this.$store.getters.shops.map(shop => {
                return {
                    ...shop,
                    percent: 0
                }
            });
        },
    },
}
</script>

<style lang="scss">
.background-iron-grey {
    background-color: #444444;
}

.background-iron-darkgrey {
    background-color: #333333;
}

.fz-18 > tr > td, th {
    font-size: 16px !important;
}

.margin-28 {
    margin-top: 28px;
}

.w-50px {
    width: 50px;
}

.cart__parameters__checkboxes {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-gap: 10px;
    padding: 15px 25px;
}

.cart__parameters {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 10px;
    padding: 15px 25px;
}

.cart__parameters > div:nth-child(2n+1):last-child {
    grid-column: 1 / 3;
}

.client__table-heading {
    padding-bottom: 10px;
}

.product__list {
    background-color: #444444 !important;
}

.split__payment {
    padding: 15px 25px;

    div {
        display: grid;
        grid-template-columns: 200px 1fr;
        grid-gap: 10px;
        align-content: center;
        align-items: center;
    }
}
</style>
