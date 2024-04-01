<template>
    <i-card-page :title="getTitle">
        <div v-if="isReady">
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
                <v-text-field
                    class="mx-4"
                    label="Стоимость доставки"
                    type="number"
                    v-model="deliveryPrice"
                />
                <v-btn color="primary" class="ml-4">
                    Обновить
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
                <v-btn @click="$refs.waybillInputRef.click()" class="mr-4" color="primary">
                    Загрузить накладную
                    <v-icon>mdi-upload</v-icon>
                </v-btn>
                <input type="file" ref="waybillInputRef" @change="onWaybillChange" class="d-none">
                <v-btn @click="$refs.invoiceInputRef.click()" class="mr-4" color="primary">
                    Загрузить счет на оплату
                    <v-icon>mdi-upload</v-icon>
                </v-btn>
                <input type="file" ref="invoiceInputRef" @change="onInvoiceChange" class="d-none">
            </div>
            <p class="text-h6">
                Состав заказа:
            </p>
            <div v-if="products">
                <v-simple-table>
                    <template #default>
                        <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Состав</th>
                            <th>Итого</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="product of products" :key="product.id">
                            <td>
                                <div style="display: flex; column-gap: 16px;">
                                    <v-img
                                        max-height="250"
                                        max-width="250"
                                        :src="product.product_image"
                                    />
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
                                <v-list>
                                    <v-list-item v-for="item of product.items" :key="`${product.id}-${item.id}`">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ (item.type || 'Стандартный') }} | {{ item.count }} шт. |
                                                {{ item.total_price | priceFiltersRub }}
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>
                                {{ product.total_price | priceFiltersRub }}
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </div>
        </div>
    </i-card-page>
</template>

<script>
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        isReady: false,
        order: null,
        statuses: [],
        products: [],
        statusId: null,
        deliveryPrice: null,
        invoice: null,
        waybill: null
    }),
    methods: {
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
            console.log(data);

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
            console.log(data);
            this.invoice = data.invoice;
            this.$loading.disable();
        }
    },
    computed: {
        getTitle() {
            if (this.order) {
                return `Заказ #${this.order.id}`;
            } else {
                return 'Загрузка...';
            }
        }
    },
    async mounted() {
        this.$loading.enable();
        this.isReady = false;
        const { data } = await axiosClient.get('/opt/admin/v1/order/' + this.$route.params.id);
        this.order = data.order;
        this.statusId = this.order.status_id;
        this.deliveryPrice = this.order.delivery_price;
        this.waybill = this.order.waybill;
        this.invoice = this.order.invoice;
        this.statuses = data.statuses;
        this.products = data.products;
        this.$nextTick(() => {
            this.isReady = true;
            this.$loading.disable();
        });
    }
};
</script>

<style scoped>

</style>
