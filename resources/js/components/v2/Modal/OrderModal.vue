<template>
    <v-dialog v-model="state" max-width="1200" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">Редактирование заказа</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text v-if="!loading">
                <div>
                    <v-divider></v-divider>
                    <h6 class="d-flex align-center">
                        <span>
                            <b>Клиент:</b> {{ order.client_name }}
                        </span>
                        <v-icon :color="order.is_authorized ? 'success' : 'error'" class="ml-2">
                            mdi-{{ order.is_authorized ? 'check' : 'cancel' }}
                        </v-icon>
                        <v-btn icon v-if="!order.is_authorized" class="ml-3" @click="editClient = !editClient">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </h6>
                    <v-row v-if="editClient">
                        <v-col>
                            <v-text-field
                                class="mt-2"
                                v-model="searchClients"
                                solo
                                clearable
                                label="Поиск клиента"
                                single-line
                                hide-details
                            ></v-text-field>
                            <v-data-table
                                :loading="clients.length === 0"
                                loading-text="Идет загрузка клиентов"
                                :search="searchClients"
                                no-results-text="Нет результатов"
                                no-data-text="Нет данных"
                                :headers="clientsTableHeaders"
                                :page.sync="pagination.page"
                                :items="clients"
                                @page-count="pageCount = $event"
                                :items-per-page="10"
                                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                                <template v-slot:item.client_balance="{item}">
                                    {{ item.client_balance }} ₸
                                </template>
                                <template v-slot:item.client_discount="{item}">
                                    {{ item.client_discount }}%
                                </template>
                                <template v-slot:item.is_partner="{item}">
                                    <v-icon :color="item.is_partner ? 'success' : 'error'">
                                        {{ item.is_partner ? 'mdi-check' : 'mdi-close' }}
                                    </v-icon>
                                </template>
                                <template v-slot:item.actions="{ item }">
                                    <v-btn icon @click="changeClient(item.id)" color="success">
                                        <v-icon>mdi-check</v-icon>
                                    </v-btn>
                                </template>
                                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                                </template>
                            </v-data-table>
                            <div class="text-xs-center pt-2">
                                <v-pagination
                                    v-model="pagination.page"
                                    :total-visible="10"
                                    :length="pageCount"></v-pagination>
                            </div>
                        </v-col>
                    </v-row>
                    <v-divider></v-divider>
                    <h6>Параметры доставки</h6>
                    <v-text-field
                        label="Скидка"
                        v-model="orderDiscount"
                        type="number"
                    />
                    <v-text-field
                        v-model="order.address"
                        label="Адрес доставки"
                    />
                    <v-select
                        v-model="order.city"
                        label="Город"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                    />
                    <v-text-field
                        v-model="order.comment"
                        label="Комментарий"
                    />
                    <v-text-field
                        v-model="order.email"
                        label="E-mail"
                    />
                    <v-select
                        label="Доставка"
                        v-model="order.delivery"
                        :items="deliveries"
                        item-value="id"
                        item-text="text"
                    />
                    <v-select
                        label="Способ оплаты"
                        v-model="order.payment"
                        :items="payments"
                        item-value="id"
                        item-text="text"
                    />

                    <v-btn color="success" @click="changeDeliveryInfo">
                        Сохранить
                        <v-icon>mdi-check</v-icon>
                    </v-btn>
                </div>
                <v-divider></v-divider>
                <h6>Товары</h6>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th>#</th>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Склад</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
                        <tr v-for="(item, index) of order._products" :key="`product-id-${item.id}`">
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
                                                item.manufacturer }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>{{ item.product_price | priceFilters}}</td>
                            <td>
                                {{ item.store.name }}
                            </td>
                            <td>
                                <v-btn icon color="error" @click="deleteFromProducts(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
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
                        />
                    </v-col>
                </v-row>
                <v-data-table
                    class="background-tooman-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="productHeaders"
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
                        <v-btn depressed icon @click="addToOrder(item)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:item.quantity="{item}">
                        {{ item.quantity - getOrderCount(item)}}
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
                <v-btn color="success" @click="saveOrderProducts">
                    Сохранить товары
                </v-btn>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    import axios from 'axios';
    import ACTIONS from "@/store/actions";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import cart from "@/mixins/cart";

    export default {
        mixins: [product, product_search, cart],
        data: () => ({
            order: null,
            loading: true,
            editClient: false,
            editDiscount: false,
            orderDiscount: 0,
            searchClients: '',
            pagination: {
                ascending: true,
                rowsPerPage: 10,
                page: 1
            },
            pageCount: 1,
            deliveries: [
                {
                    id: 0,
                    text: 'Доставка курьером'
                },
                {
                    id: 1,
                    text: 'Самовывоз'
                }
            ],
            payments: [
                {
                    id: 0,
                    text: 'Оплата наличными'
                },
                {
                    id: 1,
                    text: 'Оплата картой'
                },
                {
                    id: 2,
                    text: 'Онлайн-оплата'
                },
            ],
            clientsTableHeaders: [
                {
                    value: 'client_name',
                    text: 'ФИО',
                    sortable: false
                },
                {
                    value: 'client_phone',
                    text: 'Телефон',
                    sortable: false,
                },
                {
                    value: 'client_balance',
                    text: 'Баланс'
                },
                {
                    value: 'client_card',
                    text: 'Номер карты'
                },
                {
                    value: 'client_discount',
                    text: 'Процент скидки'
                },
                {
                    value: 'is_partner',
                    text: 'Партнер'
                },
                {
                    value: 'city',
                    text: 'Город'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ],
            productHeaders: [
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
        async created() {
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        },
        methods: {
            deleteFromProducts(index) {
                this.order._products.splice(index, 1);
            },
            async changeClient(client_id) {
                this.loading = true;
                this.$loading.enable();
                const response = await axios.patch(`/api/v2/orders/client/${this.order.id}`, {
                    client_id: client_id,
                });

                this.order = response.data.data;
                this.orderDiscount = this.order.discount;
                this.editClient = false;
                this.loading = false;
                this.$loading.disable();
            },
            async changeDeliveryInfo() {
                this.loading = true;
                this.$loading.enable();
                const response = await axios.patch(`/api/v2/orders/${this.order.id}`, {
                    discount: this.orderDiscount,
                    delivery: this.order.delivery,
                    payment: this.order.payment,
                    email: this.order.email,
                    address: this.order.address,
                    comment: this.order.comment,
                    city: this.order.city,
                });

                this.order = response.data.data;
                this.editDiscount = false;

                this.loading = false;
                this.$loading.disable();
                this.$toast.success('Заказ отредактирован');
            },
            addToOrder(item) {
                if (item.quantity - this.getOrderCount(item) === 0) {
                    return;
                }
                this.order._products.push({
                    ...item,
                    manufacturer: item.manufacturer.manufacturer_name,
                    store: this.stores.find(s => s.id === this.storeFilter)
                })
            },
            getOrderCount(item) {
                return this.order._products.filter(p => {
                    return p.id == item.id && typeof (p.product_sku_id) === "undefined" && p.store.id == this.storeFilter;
                }).length;
            },
            async saveOrderProducts() {
                this.loading = true;
                const response = await axios.post(`/api/v2/orders/products/${this.order.id}`, {
                    products: this.order._products,
                });

                this.order = response.data.data;
                this.orderDiscount = this.order.discount;
                await this.getProductQuantities(this.storeFilter);
                this.editClient = false;
                this.loading = false;

            }
        },
        computed: {
            clients() {
                return this.$store.getters.clients;
            },
            cities() {
                return this.$store.getters.cities;
            },
            stores() {
                return this.$store.getters.stores;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                type: Number,
                default: -1,
            }
        },
        watch: {
            async state(val) {
                if (val) {
                    this.loading = true;
                    const response = await axios.get(`/api/v2/orders/${this.id}`);
                    this.order = response.data.data;
                    this.orderDiscount = this.order.discount;
                    this.loading = false;
                }
            },
            orderDiscount(val) {
                this.$nextTick(() => {
                    this.orderDiscount = Math.max(0, Math.min(100, val));
                });
            }

        }
    }
</script>

<style scoped>

</style>
