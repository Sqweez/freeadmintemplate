<template>
    <div>
        <v-card class="background-iron-darkgrey mb-5" v-if="!emptyCart">
            <v-card-title class="justify-space-between">
                <span>Корзина</span>
                <div>
                    <v-btn depressed color="error" class="top-button" @click="waybillModal = true;">
                        Сформировать накладную
                    </v-btn>
                </div>
            </v-card-title>
            <v-card-text style="padding: 0;">
                <div class="">
                    <div class="cart__parameters">
                        <div>
                            <v-checkbox
                                label="Бесплатно"
                                v-model="isFree"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div v-if="!isFree">
                            <v-checkbox
                                label="Kaspi Red"
                                v-model="isRed"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div v-if="!isFree">
                            <v-text-field
                                v-model.number="discountPercent"
                                class="w-100px"
                                type="number"
                                suffix="%"
                                :max="100"
                                color="white darken-2"
                                label="Скидка"
                                outlined
                            />
                        </div>
                        <div class="cart__payment-type" v-if="!isFree">
                            <v-select
                                label="Способ оплаты"
                                v-model="payment_type"
                                :items="payment_types"
                                item-text="name"
                                outlined
                                class="w-150px"
                                item-value="id"></v-select>
                        </div>

                        <div v-if="clientChosen && !isFree">
                            <v-text-field
                                class="w-150px"
                                type="number"
                                color="white darken-2"
                                v-model="balance"
                                label="Списать с баланса"
                                outlined
                            />
                        </div>
                        <div v-if="clientChosen && !isFree">
                            <v-autocomplete
                                label="Партнер"
                                outlined
                                :items="partners"
                                item-value="id"
                                :disabled="promocodeSet"
                                item-text="client_name"
                                v-model="partner_id"
                                append-outer-icon="mdi-close"
                                @click:append-outer="partner_id = null"
                            ></v-autocomplete>
                        </div>
                        <div v-if="clientChosen && !isFree">
                            <v-text-field
                                label="Промокод"
                                :disabled="!!partner_id"
                                v-model="promocode"
                                class="w-100px"
                                color="white darken-2"
                                @keypress.enter="searchPromocode"
                                hint="Для поиска промокода нажмите enter"
                                :persistent-hint="true"
                                outlined
                            />
                        </div>
                    </div>
                </div>
                <v-divider></v-divider>
                <v-simple-table v-slot:default v-if="client && false">
                    <template>
                        <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Сумма покупок</th>
                            <th>Баланс</th>
                            <th>Скидка</th>
                            <th>Отменить</th>
                        </tr>
                        </thead>
                        <tbody class="background-iron-grey fz-18">
                        <tr>
                            <td>{{ client.client_name }}</td>
                            <td>{{ client.client_phone }}</td>
                            <td>{{ client.total_sum | priceFilters}}</td>
                            <td>{{ client.client_balance | priceFilters}}</td>
                            <td>{{ client.client_discount }}%</td>
                            <td>
                                <v-btn depressed icon @click="cancelClient">
                                    <v-icon>mdi-cancel</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-list v-if="client" class="d-flex">
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">ФИО</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Телефон</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_phone }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Сумма покупок</v-list-item-subtitle>
                            <v-list-item-title>{{ client.total_sum | priceFilters}}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Баланс</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_balance | priceFilters }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Скидка</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_discount }}%</v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-btn depressed icon @click="cancelClient">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
                <v-divider></v-divider>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th>#</th>
                            <th>Товар</th>
                            <th>Количество</th>
                            <th>Скидка</th>
                            <th>Цена</th>
                            <th>Стоимость</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-iron-grey">
                        <tr v-for="(item, index) of cart" :key="`product-id-${item.uuid}`">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>

                                <div class="d-flex align-center">
                                    <v-btn depressed text icon color="error" @click="decreaseCartCount(index)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <v-text-field v-model="item.count" type="number" style="min-width: 40px; max-width: 40px; text-align: center"  @change="updateCount($event, item)" @input="updateCount($event, item)"/>
                                    <v-btn depressed text icon color="success" @click="increaseCartCount(index)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </td>
                            <td>
                                <v-text-field
                                    type="number"
                                    v-model="item.discount"
                                    @input="updateDiscount(item)"
                                    suffix="%"
                                    @change="updateDiscount(item)"
                                />
                            </td>
                            <td>{{ item.product_price | priceFilters}}</td>
                            <td>{{ item.product_price * item.count - (Math.max(discountPercent, item.discount) / 100 * item.product_price * item.count) | priceFilters }}</td>
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
                        <thead class="fz-18">
                        <tr>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Процент скидки</th>
                            <th class="text-center">Скидка</th>
                            <th class="green--text darken-1 text-center">Итого к оплате</th>
                        </tr>
                        </thead>
                        <tbody class="background-iron-grey fz-18">
                        <tr class="pt-5">
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal | priceFilters}}</td>
                            <td class="text-center">{{ discount }}%</td>
                            <td class="text-center">{{ discountTotal | priceFilters}}</td>
                            <td class="text-center green--text darken-1">{{ total - balance | priceFilters }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div class="background-iron-grey pa-10">
                    <v-btn depressed color="error" block style="font-size: 16px" @click="clientCartModal = true" v-if="!client">
                        Выбрать клиента
                    </v-btn>
                    <v-btn depressed color="error" block style="font-size: 16px" @click="onSale" v-else>
                        Оформить заказ
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-iron-darkgrey">
            <v-card-title>
                Товары
            </v-card-title>
            <v-card-text >
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
                    <v-col cols="12" xl="2">
                        <v-checkbox
                            v-model="hideNotInStock"
                            label="Скрывать отсутствующие"
                        />
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
                    <v-col cols="12" xl="4" v-if="is_admin">
                        <v-select
                            :items="stores"
                            item-text="name"
                            v-model="storeFilter"
                            item-value="id"
                            label="Склад"
                        />
                    </v-col>
                </v-row>
                <v-btn depressed icon primary @click="refreshProducts">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
                <v-data-table
                    class="background-iron-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
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
                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn depressed icon @click="addToCart(item)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-btn depressed icon @click="addToCart(item, true)" color="success"  v-if="cart.find(c => c.id === item.id)">
                            <span>+1</span>
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
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <ClientCart
            v-on:cancel="clientCartModal = false"
            v-on:onClientChosen="onClientChosen"
            :state="clientCartModal"/>
        <ConfirmationModal
            :state="confirmationModal"
            message="Напечатать чек?"
            :cancel-message="'нет'"
            :on-confirm="printCheck"
            @cancel="confirmationModal = false"
        />
        <ConfirmationModal
            :state="waybillModal"
            message="Сформировать накладную?"
            :on-confirm="getWayBill"
        />
    </div>
</template>

<script>
    import ClientCart from "@/components/Modal/ClientCart";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import WayBillModal from "@/components/Modal/WayBillModal";
    import showToast from "@/utils/toast";
    import {TOAST_TYPE} from "@/config/consts";
    import ACTIONS from "@/store/actions";
    import {mapActions} from 'vuex';
    import CheckModal from "@/components/Modal/CheckModal";
    import axios from "axios";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import cart from "@/mixins/cart";
    export default {
        components: {
            CheckModal,
            ConfirmationModal,
            ClientCart,
            WayBillModal
        },
        async created() {
            this.loading = this.products.length === 0 || false;
            await this.$store.dispatch('GET_PRODUCTS_v2');
            const store_id = this.is_admin ? null : this.user.store_id;
            await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            this.loading = false;
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
        },
        watch: {
            storeFilter() {
                this.cart = [];
            },
            discountPercent(value) {
                this.$nextTick(() => {
                    if (this.discountPercent > 99) {
                        this.discountPercent = 100;
                    }
                    if (value.toString().length > 3) {
                        this.discountPercent = +(value.toString().slice(0, 3));
                    }
                    this.isFree = this.discountPercent === 100;
                })
            },
            balance() {
                this.$nextTick(() => {
                    this.balance = Math.min(this.client.client_balance, Math.max(0, this.balance));
                })
            },
            isFree(value) {
                if (value) {
                    this.discountPercent = 100;
                } else {
                    this.discountPercent = 0;
                }
            }
        },
        mixins: [product, product_search, cart],
        data: () => ({
            waybillModal: false,
            loading: true,
            cart: [],
            isRed: false,
            isFree: false,
            payment_type: 0,
            promocodeSet: false,
            partner_id: null,
            discountPercent: 0,
            promocode: "",
            clientCartModal: false,
            confirmationModal: false,
            wayBillModal: false,
            client: null,
            overlay: false,
            sale_id: null,
            balance: 0,
            showCheckModal: false,
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
                ACTIONS.GET_PRODUCT,
                ACTIONS.GET_CLIENTS,
                ACTIONS.GET_STORES,
            ]),
            cancelClient() {
                this.client = null;
                this.partner_id = null;
                this.promocode = '';
                this.discountPercent = 0;
                this.promocodeSet = false;
            },
            async searchPromocode() {
                this.$loading();
                try {
                    const response = await axios.get(`/api/promocode/search/${this.promocode}`);
                    this.partner_id = response.data.data.partner.id;
                    this.discountPercent = Math.max(this.discountPercent, response.data.data.discount);
                    showToast('Партнер установлен');
                    this.promocodeSet = true;
                } catch (e) {
                    showToast('Промокод не найден', TOAST_TYPE.ERROR)
                } finally {
                    this.$loading();
                }
            },
            async refreshProducts() {
                this.loading = true;
                await this.$store.dispatch('GET_PRODUCTS_v2');
                const store_id = this.is_admin ? null : this.user.store_id;
                await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
                await this.$store.dispatch(ACTIONS.GET_CLIENTS);
                showToast('Список товаров обновлен!');
                await this.getProductQuantities(this.storeFilter);
                this.loading = false;
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
            onClientChosen(client) {
                this.clientCartModal = false;
                this.client = client;
            },
            async onSale() {
                const sale = {
                    cart: this.cart.map(c => {
                        return {id: c.id, product_price: c.product_price, count: c.count, discount: c.discount};
                    }),
                    store_id: this.storeFilter,
                    user_id: this.user.id,
                    client_id: this.client.id,
                    discount: this.discount,
                    kaspi_red: this.isRed && !this.isFree,
                    balance: this.balance,
                    partner_id: this.partner_id,
                    payment_type: this.payment_type
                };
                try {
                    this.overlay = true;
                    this.sale_id = await this.$store.dispatch('MAKE_SALE_v2', sale);
                    showToast('Продажа совершена успешно!');
                    this.confirmationModal = true;
                    this.cart = [];
                    this.client = null;
                    this.discountPercent = '';
                    this.isRed = false;
                    this.isFree = false;
                    this.balance = 0;
                    this.payment_type = 0;
                    this.partner_id = false;
                } catch (e) {
                    showToast('Произошла ошибка', TOAST_TYPE.ERROR);
                } finally {
                    this.overlay = false;
                }
            },
            printCheck() {
                this.confirmationModal = false;
                window.open(`/check/${this.sale_id}`, '_blank');
            },
            async getWayBill() {
                this.waybillModal = false;
                try {
                    this.overlay = true;
                    const { data } = await axios.post('/api/excel/transfer/waybill?type=sale', {
                        child_store: this.storeFilter,
                        parent_store: this.storeFilter,
                        cart: this.cart,
                    });
                    const link = document.createElement('a');
                    link.href = `${window.location.origin}/${data.path}`;
                    link.click();
                } catch (e) {
                    showToast('При создании накладной произошла ошибка!', TOAST_TYPE.ERROR);
                } finally {
                    this.overlay = false;
                }
            },
            async searchProducts() {
            },
        },
        computed: {
            partners() {
                return this.$store.getters.PARTNERS;
            },
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
            discountTotal() {
                return this.cart.reduce((a, c) => {
                    return a + Math.max(this.discount, c.discount) /100 * c.product_price * c.count;
                }, 0);
            },
            total() {
                return this.subtotal - this.discountTotal;
            },
            discount() {
                if (this.isFree) {
                    return 100;
                }
                if (!this.client) {
                    return Math.min(this.discountPercent, 100);
                }
                return Math.min(Math.max(this.discountPercent, this.client.client_discount, 0), 100);
            },
            payment_types() {
                return this.$store.getters.payment_types;
            },
            clientChosen() {
                return this.client && this.client.id !== -1;
            },
        },
    }
</script>

<style>
    .background-iron-grey {
        background-color: #444444;
    }
    .background-iron-darkgrey {
        background-color: #333333;
    }
    .fz-18 > tr > td, th {
        font-size: 16px!important;
    }
    .margin-28 {
        margin-top: 28px;
    }
    .w-50px {
        width: 50px;
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
        background-color: #444444!important;
    }
</style>
