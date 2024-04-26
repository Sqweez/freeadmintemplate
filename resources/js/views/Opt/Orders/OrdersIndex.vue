<template>
    <div>
        <i-card-page title="Оптовые заказы">
            <v-simple-table>
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
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="order of orders" :key="order.id">
                        <td>{{ order.id }}</td>
                        <td>{{ order.client.name }}</td>
                        <td>{{ order.total_price | priceFiltersRub}}</td>
                        <td>{{ order.position_count }} шт.</td>
                        <td>{{ order.products_count }} шт.</td>
                        <td>{{ order.created_at }}</td>
                        <td>{{ (order.delivery_cost || 0) | priceFiltersRub }}</td>
                        <td>{{ (order.is_paid ? 'Да' : 'Нет')}}</td>
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
                        <td>
                            <v-btn icon @click="$router.push('/opt/orders/' + order.id)">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </i-card-page>
    </div>
</template>

<script>
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        orders: [],
        statuses: [],
    }),
    methods: {},
    computed: {},
    async mounted() {
        this.$loading.enable();
        const { data } = await axiosClient.get('/opt/admin/v1/order');
        this.orders = data.orders;
        this.$loading.disable();
    }
};
</script>

<style scoped>

</style>
