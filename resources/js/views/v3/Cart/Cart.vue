<template>
    <div>
        <v-card class="background-tooman-darkgrey mb-5" v-if="!emptyCart || certificate">
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
                    <div class="cart__parameters__checkboxes">
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
                        <div>
                            <v-checkbox
                                label="Доставка"
                                v-model="isDelivery"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div v-if="!isFree">
                            <v-checkbox
                                label="Раздельная оплата"
                                v-model="isSplitPayment"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div>
                            <v-checkbox
                                label="Оплачен"
                                v-model="is_paid"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div>
                            <v-checkbox
                                label="Оптовая продажа"
                                v-model="isOpt"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div>
                            <v-checkbox
                                label="Отправить в телеграм"
                                v-model="isSendTelegram"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>

                    </div>
                    <div class="cart__parameters">
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
                                :disabled="isRed || isSplitPayment"
                                item-text="name"
                                outlined
                                class="w-150px"
                                item-value="id"></v-select>
                        </div>
                        <div v-if="!isFree">
                            <v-autocomplete
                                :items="certificates"
                                :item-text="function(item) {
                                  return `${item.barcode} номинал: ${item.amount} тенге`;
                                }"
                                :item-value="function(item) {
                                  return item;
                                }"
                                label="Сертификат"
                                no-data-text="Нет данных"
                                v-model="used_certificate"
                                append-outer-icon="mdi-close"
                                @click:append-outer="used_certificate = null"
                            />
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
                        <div v-if="!isFree">
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
                        <div v-if="!isFree">
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
                        <v-textarea
                            rows="3"
                            auto-grow
                            v-model="comment"
                            label="Комментарий"
                        />
                    </div>
                    <div class="split__payment" v-if="isSplitPayment">
                        <div v-for="(type, index) of payment_types_without_split" :key="`split_type-${type.id}`">
                            <p>{{ type.name }}</p>
                            <v-text-field
                                class="w-100px"
                                type="number"
                                color="white darken-2"
                                outlined
                                v-model.number="splitPayment[index].amount"
                            />
                        </div>
                        <p>Оставьте значение 0, там где оплата не производится</p>
                        <p>Остаток: {{ splitPrice | priceFilters }}</p>
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
                        <tbody class="background-tooman-grey fz-18">
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
                        <tbody class="background-tooman-grey">
                        <tr v-if="certificate">
                            <td>#</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Сертификат
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                На сумму {{ certificate.amount | priceFilters }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>
                                1
                            </td>
                            <td>
                                0%
                            </td>
                            <td>
                                {{ certificate.amount | priceFilters }}
                            </td>
                            <td>
                                {{ certificate.amount | priceFilters }}
                            </td>
                            <td>
                                <v-btn icon color="error" @click="deleteCertificate">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
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
                            <th class="text-center" v-if="preorder">Предоплата</th>
                            <th class="green--text darken-1 text-center">Итого к оплате</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr class="pt-5">
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal | priceFilters}}</td>
                            <td class="text-center">{{ discount }}%</td>
                            <td class="text-center">{{ discountTotal | priceFilters}}</td>
                            <td class="text-center" v-if="preorder">{{ preorder.amount | priceFilters}}</td>
                            <td class="text-center green--text darken-1">{{ finalPrice | priceFilters }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div class="background-tooman-grey pa-10">
                    <v-btn depressed color="error" block style="font-size: 16px" @click="clientCartModal = true" v-if="!client">
                        Выбрать клиента
                    </v-btn>
                    <v-btn depressed color="error" block style="font-size: 16px" @click="onSale" v-else>
                        Оформить заказ
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-tooman-darkgrey">
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
                    <v-col cols="12" xl="4">
                        <v-select
                            :items="stores"
                            item-text="name"
                            v-model="storeFilter"
                            item-value="id"
                            label="Склад"
                            :disabled="!(is_admin || IS_BOSS)"
                        />
                    </v-col>
                </v-row>
                <v-row>
                    <v-btn depressed icon primary @click="refreshProducts">
                        <v-icon>mdi-refresh</v-icon>
                    </v-btn>
                </v-row>
                <v-row>
                    <v-btn depressed color="success" class="float-right ml-2 mt-2" @click="certificateModal = true;">
                        Добавить сертификат +
                    </v-btn>
                    <v-btn depressed color="success" class="float-right ml-2 mt-2" @click="preorderModal = true;">
                        По предоплате
                    </v-btn>
                </v-row>
                <v-data-table
                    class="background-tooman-grey fz-18"
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
                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.attributes="{ item }">
                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}
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
        <CertificateModal
            @cancel="certificateModal = false"
            @submit="createCertificate"
            :state="certificateModal"/>
        <PreordersListModal
            :state="preorderModal"
            @cancel="preorderModal = false;"
            @preorder="onPreorderChosen"
        />
    </div>
</template>

<script>
    import ClientCart from "@/components/Modal/ClientCart";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import WayBillModal from "@/components/Modal/WayBillModal";
    import {TOAST_TYPE} from "@/config/consts";
    import ACTIONS from "@/store/actions";
    import {mapActions} from 'vuex';
    import CheckModal from "@/components/Modal/CheckModal";
    import axios from "axios";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import cart from "@/mixins/cart";
    import CertificateModal from "@/components/Modal/CertificateModal";
    import PreordersListModal from "@/components/Modal/PreordersListModal";
    export default {
        components: {
            PreordersListModal,
            CertificateModal,
            CheckModal,
            ConfirmationModal,
            ClientCart,
            WayBillModal
        },
        async created() {
            this.loading = this.products.length === 0 || false;
            await this.$store.dispatch('GET_PRODUCTS_v2');
            const store_id = (this.IS_SUPERUSER) ? null : this.user.store_id;
            await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch('GET_CERTIFICATES');
            await this.$store.dispatch('GET_PREORDERS');
            this.client = this.user.store.type_id === 3 ? {
                    id: -1,
                    client_name: 'Гость',
                    sale_sum: 0,
                    client_balance: 0,
                    client_discount: 0,
                    total_sum: 0,
                } : null;
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
            },
            isRed(value) {
                if (value) {
                    this.payment_type = 2;
                } else {
                    this.payment_type = 0;
                }
            },
            isSplitPayment(value) {
                if (value) {
                    this.payment_type = 5;
                } else {
                    this.payment_type = 0;
                }
            },
            searchQuery(value) {
                if (value.toString().length > 4 && /^\d+$/.test(value)) {
                    this.categoryId = this.manufacturerId = -1;
                }
            }
        },
        mixins: [product, product_search, cart],
        data: () => ({
            isSendTelegram: false,
            isOpt: false,
            isDelivery: false,
            awaitingForKaspiPayment: false,
            preorderModal: false,
            preorder: null,
            comment: '',
            waybillModal: false,
            certificateModal: false,
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
            wayBillModal: false,
            client: null,
            overlay: false,
            sale_id: null,
            balance: 0,
            showCheckModal: false,
            currentItems: [],
            is_paid: false,
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
            ],
            kaspiProcessId: null,
            kaspiTransactionId: null,
        }),
        methods: {
            ...mapActions([
                ACTIONS.GET_PRODUCT,
                ACTIONS.GET_CLIENTS,
                ACTIONS.GET_STORES,
            ]),
            onPreorderChosen(preorder) {
                this.preorderModal = false;
                const preorderProducts = preorder.products.map(p => p.product_id);
                const products = this.products.filter(p => {
                    return preorderProducts.findIndex(pr => pr === p.id) !== -1;
                });
                if (products.length !== preorderProducts.length) {
                    this.$toast.error('На складе не хватает выбранных товаров');
                    return;
                }
                products.forEach(item => {
                    this.addToCart(item);
                });


                if (preorder.client_id !== -1) {
                    this.client = this.clients.find(c => c.id === preorder.client_id);
                }

                this.preorder = {...preorder};

            },
            async sendTelegram(saleId) {
                this.$loading.enable();
                const response = await axios.get(`/api/sales/telegram/${saleId}`);
                console.log(response);
                this.$loading.disable();
            },
            async createCertificate(certificate) {
                this.certificateModal = false;
                try {
                    this.$loading.enable();
                    const { data } = await axios.post(`/api/v2/certificates`, certificate);
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
            async deleteCertificate() {
                try {
                    this.$loading.enable();
                    await axios.delete(`/api/v2/certificates/${this.certificate.id}`);
                    this.certificate = null;
                }
                catch (e) {
                    this.$toast.error('Произошла ошибка!');
                } finally {
                    this.$loading.disable();
                }
            },
            cancelClient() {
                this.client = null;
                this.partner_id = null;
                this.promocode = '';
                this.discountPercent = 0;
                this.promocodeSet = false;
            },
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
                await this.$store.dispatch('GET_PRODUCTS_v2');
                const store_id = this.IS_SUPERUSER ? null : this.user.store_id;
                await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
                await this.$store.dispatch(ACTIONS.GET_CLIENTS);
                this.$toast.success('Список товаров обновлен!');
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
            async createKaspiResponse() {
                const kaspiTerminalIp = localStorage.getItem('kaspi_terminal_ip') ? localStorage.getItem('kaspi_terminal_ip') : this.currentStore.kaspi_terminal_ip;
                console.log(kaspiTerminalIp);
                const url = `http://${kaspiTerminalIp}:8080/payment`;
                const amount = this.isSplitPayment ? this.splitPayment
                    .filter(s => (s.payment_type === 1 || s.payment_type === 2))
                    .reduce((a, c) => {
                        return a + c.amount;
                    }, 0) : this.finalPrice;
                const queryParams = new URLSearchParams({
                    type: 'card',
                    amount
                });
                const link = `${url}?${queryParams}`;
                this.awaitingForKaspiPayment = true;
                try {
                    let popup = await window.open(link, Date.now().toString(), 'width=300,height=300,status=no,scrollbar=no,location=no');
                    window.focus();
                } catch (e) {
                    console.log(e);
                }
            },
            async onSale() {
                const split_payment = this.isSplitPayment ? this.splitPayment.filter(p => p.amount > 0) : null;
                if (split_payment !== null && !split_payment.length) {
                    this.$toast.error('Раздельная оплата не заполнена');
                    return;
                }
                if (split_payment !== null) {
                    const total = split_payment.reduce((a, c) => {
                        return a + c.amount;
                    }, 0)
                    if (total !== this.finalPrice) {
                        this.$toast.error('Суммарная раздельная оплата не совпадает с итоговой суммой');
                        return;
                    }
                }
                const sale = {
                    cart: this.cart.map(c => {
                        return {id: c.id, product_price: c.product_price, count: c.count, discount: c.discount};
                    }),
                    store_id: this.storeFilter,
                    user_id: !this.isDelivery ?  this.user.id : 1,
                    client_id: this.client.id,
                    discount: this.discount,
                    kaspi_red: this.isRed && !this.isFree,
                    balance: this.balance,
                    partner_id: this.partner_id,
                    payment_type: this.payment_type,
                    certificate: this.certificate,
                    used_certificate: this.used_certificate,
                    split_payment: split_payment,
                    comment: this.comment,
                    preorder: this.preorder,
                    is_delivery: this.isDelivery,
                    is_paid: this.is_paid,
                    is_opt: this.isOpt,
                };
                try {
                    this.overlay = true;
                    if (this.isKaspiTerminalEnabled) {
                        await this.createKaspiResponse();
                    }
                    await this.createSale(sale);
                } catch (e) {
                    this.$toast.error('Произошла ошибка', TOAST_TYPE.ERROR);
                } finally {
                    this.overlay = false;
                }
            },
            async createSale(sale) {
                try {
                    this.sale_id = await this.$store.dispatch('MAKE_SALE_v2', sale);
                    if (this.isSendTelegram) {
                        await this.sendTelegram(this.sale_id);
                    }
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
                    this.certificate = null;
                    this.used_certificate = null;
                    this.comment = '';
                    this.preorder = null;
                    this.isSplitPayment = false;
                    this.isDelivery = false;
                    this.isOpt = false;
                    this.isSendTelegram = false;
                    this.is_paid = false;
                } catch (e) {
                    throw e;
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
                    this.$toast.error('При создании накладной произошла ошибка!');
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
            certificates() {
                return this.$store.getters.CERTIFICATES;
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
                return Math.min(Math.max(this.discountPercent, (!this.isRed ? this.client.client_discount : 0), 0), 100);
            },
            payment_types() {
                return this.$store.getters.payment_types;
            },
            payment_types_without_split() {
                const payments = this.$store.getters.payment_types.filter(p => p.id !== 5);
                this.splitPayment = payments.map(p => ({payment_type: p.id, amount: 0}));
                return payments;
            },
            clientChosen() {
                return this.client && this.client.id !== -1;
            },
            finalPrice() {
                let total = this.total;
                if (this.balance > 0) {
                    total -= this.balance;
                }
                if (this.used_certificate) {
                    total -= this.used_certificate.amount;
                }

                if (this.preorder) {
                    total -= this.preorder.amount;
                }

                return Math.max(0, Math.ceil(total));
            },
            splitPrice() {
                return this.finalPrice - this.splitPayment.reduce((a, c) => {
                    return a + +c.amount;
                }, 0);
            },
            clients() {
                return this.$store.getters.clients;
            },
            currentStore() {
                return this.stores.find(s => s.id === this.storeFilter)
            },
            isKaspiTerminalEnabled() {
                return (this.currentStore.has_kaspi_terminal || localStorage.getItem('kaspi_terminal_ip'))
                    && (this.payment_type === 1
                        || this.payment_type === 2
                        || this.splitPayment.filter(s => (s.payment_type === 1 || s.payment_type === 2) && s.amount > 0).length > 0);
            }
        },
    }
</script>

<style lang="scss">
    .background-tooman-grey {
        background-color: #444444;
    }
    .background-tooman-darkgrey {
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
        background-color: #444444!important;
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
