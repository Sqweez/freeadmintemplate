<template>
    <i-card-page :title="getTitle">
        <div v-if="isReady">
<!--            <v-tabs
                v-model="tab"
                align-with-title
            >
                <v-tabs-slider></v-tabs-slider>

                <v-tab
                    v-for="item in items"
                    :key="item"
                >
                    {{ item }}
                </v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab">
                <v-tab-item v-for="tab of items">
                    <div v-if="tab === 'Основная информация'">
                        <order-base-info :order="order" />
                    </div>
                    <div v-if="tab === 'Состав заказа'">
                        <order-products :order="order" :order-products="orderProducts" />
                    </div>
                </v-tab-item>
            </v-tabs-items>-->
            <v-simple-table v-if="order">
                <template #default>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Клиент</th>
                        <th>Общая стоимость</th>
                        <th>Количество позиций</th>
                        <th>Количество товаров</th>
                        <th>Дата создания</th>
                        <th>Стоимость доставки</th>
                        <th>Оплачен?</th>
                        <th>Статус</th>
                        <th>Контактные данные</th>
                        <th>Комментарий</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ order.id }}</td>
                        <td>{{ order.client.name }}</td>
                        <td>{{ order.total_price | priceFilters }}</td>
                        <td>{{ order.position_count }} шт.</td>
                        <td>{{ order.products_count }} шт.</td>
                        <td>{{ order.created_at }}</td>
                        <td>{{ (order.delivery_cost || 0) | priceFiltersRub }}</td>
                        <td>{{ (order.is_paid ? 'Да' : 'Нет') }}</td>
                        <td>
                            <v-list>
                                <v-list-item
                                    v-for="(item, i) in order.contacts"
                                    :key="`${order.id}-${i}`"
                                >
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.value }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ item.label }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </td>
                        <td>{{ order.status }}</td>
                        <td>{{ order.comment }}</td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
            <div class="d-flex align-center my-4">
                <v-select
                    class="mr-4"
                    label="Статус заказа"
                    :items="statuses"
                    item-value="id"
                    item-text="description"
                    v-model="statusId"
                />
<!--                <v-text-field
                    class="mx-4"
                    label="Стоимость доставки"
                    type="number"
                    v-model="deliveryPrice"
                />-->
                <v-btn color="primary" class="ml-4" @click="updateOrderInfo" :loading="orderInfoUpdating">
                    Обновить <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </div>
            <div class="d-flex align-center my-4">
                <v-btn :href="waybill" class="mr-4" color="primary" v-if="waybill">
                    Скачать накладную
                    <v-icon>mdi-download</v-icon>
                </v-btn>
                <v-btn :href="invoice" class="mr-4" color="primary" v-if="invoice">
                    Скачать счет на оплату
                    <v-icon>mdi-download</v-icon>
                </v-btn>
            </div>
            <div class="d-flex align-center my-4">
                <v-btn @click="$refs.waybillInputRef.click()" class="mr-4" color="primary" :disabled="order.is_editing_disabled">
                    Загрузить накладную
                    <v-icon>mdi-upload</v-icon>
                </v-btn>
                <input type="file" ref="waybillInputRef" @change="onWaybillChange" class="d-none">
                <v-btn @click="$refs.invoiceInputRef.click()" class="mr-4" color="primary" :disabled="order.is_editing_disabled">
                    Загрузить счет на оплату
                    <v-icon>mdi-upload</v-icon>
                </v-btn>
                <input type="file" ref="invoiceInputRef" @change="onInvoiceChange" class="d-none">
            </div>
            <p class="text-h6">
                Состав заказа:
            </p>
            <div>
                <v-simple-table>
                    <template #default>
                        <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Тип</th>
                            <th>Количество</th>
                            <th>Цена за ед.</th>
                            <th>Скидка</th>
                            <th>Итого</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(product, index) of orderProducts" :key="product.id">
                            <td>
                                <div style="display: flex; column-gap: 16px;">
                                    <div>
                                        <p class="title">
                                            {{ product.product_name }}
                                        </p>
                                        <p class="subtitle-2">
                                            {{ product.product_sub_name }} {{ product.manufacturer }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ product.type }}
                            </td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <v-btn icon color="error" @click="onCountInputBlur(index, product.count - 1)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <v-text-field
                                        style="max-width: 250px;"
                                        type="number"
                                        v-model.number="orderProducts[index].count"
                                        persistent-hint
                                        :hint="`Максимальное количество ${product.stock_count} шт`"
                                        @blur="onCountInputBlur(index, $event.target.value)"
                                        readonly
                                    />
                                    <v-btn icon color="success" @click="onCountInputBlur(index, product.count + 1)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </td>
                            <td>
                                <v-text-field
                                    label="Стоимость за шт"
                                    @blur="onPriceChange"
                                    v-model="orderProducts[index].price_per_item"

                                />
                            </td>
                            <td>
                                <v-text-field
                                    @blur="onPriceChange"
                                    label="Скидка"
                                    v-model="orderProducts[index].discount"
                                />
                            </td>
                            <td>
                                {{ getSubtotalPrice(product) | priceFiltersRub }}
                            </td>
                            <td>
                                <v-btn icon color="error" @click="onProductDelete(index, product)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-btn color="primary" class="my-4 mx-4" @click="updateOrderProducts(true)">
                    Обновить состав заказа <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </div>

            <div>
                <v-expansion-panels>
                    <v-expansion-panel
                        @change="onPanelChange"
                    >
                        <v-expansion-panel-header>
                            <h4>Товары</h4>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-card class="background-iron-darkgrey">
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
                                        <!--                            <v-col cols="12" xl="4">
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
                                                                            :items="$stores"
                                                                            item-text="name"
                                                                            v-model="storeFilter"
                                                                            item-value="id"
                                                                            label="Склад"
                                                                        />
                                                                    </v-col>-->
                                    </v-row>
                                    <v-data-table
                                        class="background-iron-grey fz-18"
                                        no-results-text="Нет результатов"
                                        no-data-text="Нет данных"
                                        :headers="headers"
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
                                                            {{ item.attributes.map(a => a.attribute_value).join(', ') }},
                                                            {{ item.manufacturer.manufacturer_name }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </template>
                                        <template v-slot:item.product_price="{ item }">
                                            <ul v-if="item.wholesale_prices">
                                                <li v-for="price of item.wholesale_prices" :key="price.id">
                                                    {{ price.currency.name }}: {{ $formatPrice(price.price, price.currency.unicode_symbol) }}
                                                </li>
                                            </ul>
                                            <div v-if="item.wholesale_prices && item.wholesale_prices.length === 0">
                                   <span>
                                       Цены не установлены
                                   </span>
                                            </div>
                                        </template>
                                        <template v-slot:item.attributes="{ item }">
                                            {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                        </template>
                                        <template v-slot:item.actions="{item}">
                                            <v-btn icon color="success" @click="pushToOrder(item)">
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </template>
                                        <template v-slot:item.quantity="{item}">
                                            {{ retrieveQuantity(item) }}
                                        </template>
                                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                                        </template>
                                    </v-data-table>
                                </v-card-text>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
        </div>
    </i-card-page>
</template>

<script>
import axiosClient from '@/utils/axiosClient';
import ACTIONS from '@/store/actions';
import product from '@/mixins/product';
import product_search from '@/mixins/product_search';
import OrderBaseInfo from '@/components/Opt/OrderBaseInfo.vue';
import OrderProducts from '@/components/Opt/OrderProducts.vue';
import { debounce } from 'lodash';

export default {
    components: { OrderProducts, OrderBaseInfo },
    data: () => ({
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
        isReady: false,
        order: null,
        statuses: [],
        orderProducts: [],
        statusId: null,
        deliveryPrice: null,
        invoice: null,
        waybill: null,
        storeFilter: null,
        isPanelOpened: false,
        productsWasLoaded: false,
        orderInfoUpdating: false,
        deletedProducts: [],
        tab: 'Основная информация',
        items: [
            'Основная информация', 'Состав заказа'
        ]
    }),
    methods: {
        onPriceChange: debounce(function() {
            this.updateOrderProducts(false);
        }, 500),
        async updateOrderInfo () {
            this.orderInfoUpdating = true;
            try {
                const payload = {
                    status_id: this.statusId,
                };
                const { data } = await axiosClient.patch('/opt/admin/v1/order/' + this.order.id, payload);
                this.assignOrderFields(data);
                this.$toast.success('Данные заказа обновлены')
            } catch (e) {
                this.$toast.error('Что-то пошло не так')
            } finally {
                this.orderInfoUpdating = false;
            }
        },
        async onWaybillChange(event) {
            const file = event.target.files[0];
            if (!file) {
                return undefined;
            }
            this.$refs.waybillInputRef.value = null;
            const formData = new FormData();
            formData.append('waybill', file);
            const { data } = await axiosClient.post('/opt/admin/v1/order/' + this.$route.params.id + '/waybill',
                formData);
            this.$toast.success('Накладная успешно загружена');

            this.waybill = data.waybill;
            this.$loading.disable();
        },
        async onInvoiceChange(event) {
            this.$loading.enable();
            const file = event.target.files[0];
            if (!file) {
                return undefined;
            }
            this.$refs.invoiceInputRef.value = null;
            const formData = new FormData();
            formData.append('invoice', file);
            const { data } = await axiosClient.post('/opt/admin/v1/order/' + this.$route.params.id + '/invoice',
                formData);
            this.invoice = data.invoice;
            this.$toast.success('Счет успешно загружен');
            this.$loading.disable();
        },
        onCountInputBlur (index, value) {
            const product = this.orderProducts.at(index);
            const count = Math.min(product.stock_count, Math.max(1, value));
            this.$set(this.orderProducts, index, {
                ...product,
                count
            })
            if (count !== product.count) {
                this.updateOrderProducts(false);
            }
        },
        async onPanelChange (e, v) {
            if (this.productsWasLoaded) {
                return undefined;
            }
            this.$loading.enable();
            await this.loadProducts();
            this.$loading.disable();
            this.productsWasLoaded = true;
        },
        async loadProducts () {
            await this.$store.dispatch('GET_PRODUCTS_v2', {
                only_opt: true
            });
            await this.$store.dispatch(ACTIONS.GET_STORES);
            this.storeFilter = this.$stores.at(-1).id;
        },
        retrieveQuantity (product) {
            const inOrderProduct = this.orderProducts.find(p => p.product_id === product.id)
            if (!inOrderProduct || inOrderProduct.count === inOrderProduct.initial_count) {
                return product.quantity;
            }
            return product.quantity - (inOrderProduct.count - inOrderProduct.initial_count);
        },
        pushToOrder (product) {
            const inOrderProduct = this.orderProducts.findIndex(p => p.product_id === product.id && p.id === null)
            if (inOrderProduct === -1) {
                const needlePrice = product.wholesale_prices.find(wp => wp.currency_id === this.order.currency_id);
                if (!needlePrice) {
                    return this.$toast.error('Для товара не установлена стоимость в валюте заказа');
                }
                this.orderProducts.push({
                    'id': null,
                    'product_image': null,
                    'product_name': product.product_name,
                    'manufacturer': product.manufacturer.manufacturer_name,
                    'product_sub_name': product.attributes.map(a => a.attribute_value).join(', '),
                    'link' : null,
                    'count' : 1,
                    'type': product.attributes.map(a => a.attribute_value).join(', '),
                    'stock_count': product.quantity,
                    'price_per_item': needlePrice.price,
                    'initial_count': 0,
                    'product_id': product.id,
                    discount: 0,
                });
            } else {
                this.onCountInputBlur(inOrderProduct, this.orderProducts[inOrderProduct].count + 1);
            }
        },
        getSubtotalPrice (product) {
            return (product.price_per_item * product.count * (1 - product.discount / 100))
        },
        updateOrderProducts: debounce(async function (refreshDocs = false) {
            try {
                this.$loading.enable();
                const products = [];
                this.orderProducts.forEach(product => {
                    const productObject = {
                        product_id: product.product_id,
                        count: product.count,
                        initial_count: product.initial_count,
                        deltaCount: product.count - product.initial_count,
                        discount: product.discount,
                        price: product.price_per_item,
                        ids: product?.ids || [],
                    };
                    products.push(productObject);
                })

                const payload = {
                    products,
                    deleted: this.deletedProducts,
                    refreshDocs
                };

                const { data } = await axiosClient.patch(`/opt/admin/v1/order/products/${this.order.id}`, payload)
                this.assignOrderFields(data);
                this.$toast.success('Заказ успешно обновлен')
            } catch (e) {
                console.log(e);
                this.$report(e);
            } finally {
                this.$loading.disable();
            }
        }, 500),
        onProductDelete (index, product) {
            if (product?.ids) {
                this.deletedProducts.push(...product.ids);
            }
            this.orderProducts.splice(index, 1);
        },
        assignOrderFields (data) {
            this.order = data.order;
            this.statusId = this.order.status_id;
            this.deliveryPrice = this.order.delivery_price;
            this.waybill = this.order.waybill;
            this.invoice = this.order.invoice;
            this.statuses = data.statuses;
            this.orderProducts = data.products;
        }
    },
    computed: {
        getTitle() {
            if (this.order) {
                return `Заказ #${this.order.id}`;
            } else {
                return 'Загрузка...';
            }
        },
        products () {
            return this.$store.getters.PRODUCTS_v2
                .filter(product => {
                    return product.quantity > 0;
            });
        },
    },
    async mounted() {
        this.$loading.enable();
        this.isReady = false;
        const { data } = await axiosClient.get('/opt/admin/v1/order/' + this.$route.params.id);
        this.assignOrderFields(data);
        this.$nextTick(() => {
            this.isReady = true;
            this.$loading.disable();
        });
    },
    mixins: [product, product_search],
    watch: {},
};
</script>

<style scoped>

</style>
