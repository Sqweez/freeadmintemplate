<template>
    <div>
        <v-card class="background-tooman-darkgrey mb-5" v-if="!emptyCart">
            <v-card-title class="justify-space-between">
                <span>Корзина</span>
                <div>
                   <!-- <v-btn color="error" class="top-button mr-3" @click="wayBillModal = true;">
                        Сформировать счет на оплату
                    </v-btn>-->
                    <v-btn color="error" class="top-button" @click="waybillModal = true;">
                        Сформировать накладную
                    </v-btn>
                </div>
            </v-card-title>
            <v-card-text style="padding: 0;">
                <div class="background-tooman-grey">
                    <h3 class="text-center my-2">Для списания с баланса после ввода суммы нажимайте "ENTER"!</h3>
                    <h3 class="text-center my-2">Для поиска партнера по промокоду после ввода нажимайте Enter!</h3>
                    <div class="d-flex align-center">
                        <v-row class="ml-2">
                            <v-col class="d-flex" cols="12" xl="3" md="6" style="padding: 0">
                                <div class="d-flex align-center ml-2 mr-2">
                                    <h5>Бесплатно:</h5>
                                    <v-checkbox
                                        v-model="isFree"
                                        class="ml-2 margin-28"
                                        color="white darken-2"
                                    />
                                </div>
                                <div class="d-flex align-center ml-5" v-if="!isFree">
                                    <h5>Kaspi Red:</h5>
                                    <v-checkbox
                                        v-model="isRed"
                                        class="ml-2 margin-28"
                                        color="white darken-2"
                                    />
                                </div>
                            </v-col>
                            <v-col cols="12" xl="8" md="6" style="padding: 0" v-if="!isFree">
                                <div class="d-flex mt-2">
                                    <div class="d-flex">
                                        <h5>Скидка:</h5>
                                        <v-text-field
                                            v-model="discountPercent"
                                            class="ml-2"
                                            suffix="%"
                                            type="number"
                                            :max="100"
                                            color="white darken-2"
                                        />
                                    </div>
                                    <div class="d-flex ml-4" v-if="client && client.id !== -1 && !partner_id">
                                        <h5>Промокод:</h5>
                                        <v-text-field
                                            v-model="promocode"
                                            class="ml-2"
                                            color="white darken-2"
                                            @keypress.enter="searchPromocode"
                                        />
                                    </div>
                                    <div class="d-flex ml-4" v-if="client && client.id !== -1">
                                        <h5>Списать с баланса:</h5>
                                        <v-text-field
                                            class="ml-2"
                                            type="number"
                                            color="white darken-2"
                                            v-model="balance"
                                        />
                                    </div>
                                    <div class="d-flex ml-4" v-if="client && client.id !== -1">
                                        <h5>Партнер:</h5>
                                        <v-autocomplete
                                            :items="partners"
                                            item-value="id"
                                            :disabled="promocodeSet"
                                            item-text="client_name"
                                            v-model="partner_id"
                                        ></v-autocomplete>
                                    </div>
                                    <div class="d-flex ml-4">
                                        <h5>Способ оплаты:</h5>
                                        <v-select
                                            label="Способ оплаты"
                                            v-model="payment_type"
                                            :items="payment_types"
                                            item-text="name"
                                            class="ml-2"
                                            item-value="id"></v-select>
                                    </div>
                                </div>
                            </v-col>
                        </v-row>
                    </div>
                </div>
                <v-simple-table v-slot:default v-if="client">
                    <template>
                        <thead class="background-tooman-darkgrey fz-18">
                        <tr>
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Сумма покупок</th>
                            <th>Баланс</th>
                            <th>Скидка</th>
                            <th>Отменить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr>
                            <td>{{ client.client_name }}</td>
                            <td>{{ client.client_phone }}</td>
                            <td>{{ client.total_sum }} ₸</td>
                            <td>{{ client.client_balance }} ₸</td>
                            <td>{{ client.client_discount }}%</td>
                            <td>
                                <v-btn icon @click="client = null; partner_id = null; promocode = ''; discountPercent = ''; promocodeSet = false;">
                                    <v-icon>mdi-cancel</v-icon>
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
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Атрибуты</th>
                            <th>Количество</th>
                            <th>Стоимость</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
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
                                <div v-if="!item.inputMode">
                                    <v-btn icon color="error" @click="decreaseCartCount(index)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <span
                                        @click="toggleInput(index); $set(cart[index], '_count', cart[index].count);">
                                        {{ item.count }} шт.
                                    </span>
                                    <v-btn icon color="success" @click="addToCart(item)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                                <v-text-field
                                    style="max-width: 50px"
                                    v-else
                                    solo
                                    v-model="item._count"
                                    @keyup.enter="changeCount(item, index)"
                                />

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
                        <thead class="background-tooman-darkgrey fz-18">
                        <tr>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Процент скидки</th>
                            <th class="text-center">Скидка</th>
                            <th class="color-text--green text-center">Итого к оплате</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr>
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal }} ₸</td>
                            <td class="text-center">{{ discount }}%</td>
                            <td class="text-center">{{ discountTotal }} ₸</td>
                            <td class="text-center color-text--green">{{ total - balance }}₸</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div class="background-tooman-grey pa-10">
                    <v-btn color="error" block style="font-size: 16px" @click="clientCartModal = true" v-if="!client">
                        Выбрать клиента
                    </v-btn>
                    <v-btn color="error" block style="font-size: 16px" @click="onSale" v-else>
                        Оформить заказ
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-tooman-darkgrey">
            <v-card-title>
                Товары
            </v-card-title>
            <v-card-text style="padding: 0;">
                <v-row>
                    <v-col cols="12" xl="8">
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
                <v-btn icon primary @click="refreshProducts">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
                <v-data-table
                    class="background-tooman-grey fz-18"
                    :search="searchQuery"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :loading="loading"
                    loading-text="Идет загрузка товаров..."
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
                    <template v-slot:item.product_price="{item}">
                        {{ getPrice(item) }}
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
        <!--<WayBillModal
            :state="wayBillModal"
            v-on:cancel="wayBillModal = false"
        />-->
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
    import ACTIONS from "@/store/actions";
    import {mapActions} from 'vuex';
    import CheckModal from "@/components/Modal/CheckModal";
    import axios from "axios";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";

    export default {
        components: {
            CheckModal,
            ConfirmationModal,
            ClientCart,
            WayBillModal
        },
        async created() {
            this.loading = this.products.length === 0 || false;
            const store_id = this.is_admin ? null : this.user.store_id;
            await this.$store.dispatch(ACTIONS.GET_PRODUCT, store_id);
            await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
            this.loading = false;
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
        },
        watch: {
            storeFilter() {
                this.cart = [];
            },
            stores() {
                this.storeFilter = this.stores[0].id;
            },
            balance() {
                this.balance = Math.min(this.client.client_balance, Math.max(0, this.balance));
            },
        },
        mixins: [product, product_search],
        data: () => ({
            searchQuery: '',
            searchValue: '',
            storeFilter: null,
            waybillModal: false,
            loading: true,
            cart: [],
            isRed: false,
            isFree: false,
            payment_type: 0,
            promocodeSet: false,
            partner_id: null,
            discountPercent: '',
            promocode: "",
            search: '',
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
                    value: 'attributes'
                },
                {
                    value: 'manufacturer',
                    text: 'Производитель'
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
            async searchPromocode() {
                this.$loading.enable();
                try {
                    const response = await axios.get(`/api/promocode/search/${this.promocode}`);
                    this.partner_id = response.data.data.partner.id;
                    this.discountPercent = Math.max(this.discountPercent, response.data.data.discount);
                    this.$toast.success('Партнер установлен');
                    this.promocodeSet = true;
                } catch (e) {
                    this.$toast.error('Промокод не найден')
                } finally {
                    this.$loading.disable();
                }

            },
            async refreshProducts() {
                this.loading = true;
                const store_id = this.is_admin ? null : this.user.store_id;
                await this.$store.dispatch(ACTIONS.GET_PRODUCT, store_id);
                this.loading = false;
                this.$toast.success('Список товаров обновлен!')
            },
            setBalance(e) {
                this.balance = Math.min(+e.target.value, this.client.client_balance);
            },
            addToCart(item) {
                if (!this.checkAvailability(item)) {
                    this.$toast.error('Недостаточно товара');
                    return;
                }
                const index = this.cart.map(c => c.id).indexOf(item.id);
                if (index === -1) {
                    this.cart.push({...item, count: 1, product_price: this.getPrice(item)});
                } else {
                    this.increaseCartCount(index);
                }
            },
            toggleInput(index) {
                this.$set(this.cart[index], 'inputMode', !this.cart[index].inputMode);
            },
            changeCount(item, index) {
                this.$set(this.cart[index], 'count', Math.min(this.cart[index]._count, this.getQuantity(item.quantity)));
                this.toggleInput(index);
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
            async onSale() {
                this.overlay = true;
                const sale = {
                    cart: this.cart.map(c => {
                        return {id: c.id, product_price: c.product_price, count: c.count};
                    }),
                    store_id: this.storeFilter,
                    user_id: this.user.id,
                    client_id: this.client.id,
                    discount: this.discount,
                    kaspi_red: this.isRed,
                    balance: this.balance,
                    partner_id: this.partner_id,
                    payment_type: this.payment_type
                };

                this.sale_id = await this.$store.dispatch(ACTIONS.MAKE_SALE, sale);

                this.overlay = false;

                this.$toast.success('Продажа совершена успешно!');
                this.confirmationModal = true;

                this.cart = [];
                this.client = null;
                this.discountPercent = '';
                this.isRed = false;
                this.isFree = false;
                this.balance = 0;
                this.payment_type = 0;
                this.partner_id = false;
            },
            printCheck() {
                this.confirmationModal = false;
                window.open(`/check/${this.sale_id}`, '_blank');
            },
            getQuantity(quantity = []) {
                if (typeof quantity === 'number') {
                    return quantity;
                }
                if (!quantity.length) {
                    return 0;
                }
                return quantity
                    .filter(q => {
                        return +q.store_id === +this.storeFilter
                    })
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
            },
            async getWayBill() {
                this.waybillModal = false;
                const { data } = await axios.post('/api/excel/transfer/waybill?type=sale', {
                    child_store: this.storeFilter,
                    parent_store: this.storeFilter,
                    cart: this.cart,
                });

                const link = document.createElement('a');
                link.href = data.path;
                link.click();
            }
        },
        computed: {
            user() {
                return this.$store.getters.USER;
            },
            partners() {
                return this.$store.getters.PARTNERS;
            },
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
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
            discountTotal() {
                return this.subtotal * (this.discount / 100);
            },
            total() {
                return this.subtotal - this.discountTotal;
            },
            discount() {
                if (this.isFree) {
                    return 100;
                }
                if (!this.client) {
                    if (!this.discountPercent.length) {
                        return 0;
                    }
                    return Math.min(this.discountPercent, 100);
                }
                return Math.min(Math.max(this.discountPercent, this.client.client_discount, 0), 100);
            },
            stores() {
                return this.$store.getters.stores;
            },
            payment_types() {
                return this.$store.getters.payment_types;
            }
        },
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

    .background-tooman-grey {
        background-color: #444444;
    }

    .background-tooman-darkgrey {
        background-color: #333333;
    }

    .margin-28 {
        margin-top: 28px;
    }

    .fz-18 th, td {
        font-size: 18px !important;
    }

    .v-data-table {
        font-size: 18px !important;
    }
</style>
