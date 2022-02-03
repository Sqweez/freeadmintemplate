<template>
    <div>
        <v-card>
            <v-card-title>
                Информация о клиенте
            </v-card-title>
            <v-card-text v-if="!IS_LOADING">
                <h5>Клиент: {{ client.client_name }}</h5>
                <h5>Телефон: +{{ client.client_phone }}</h5>
                <h5>Номер карты: {{ client.client_card }}</h5>
                <h5>Сумма покупок: {{ client.total_sum | priceFilters }}</h5>
                <h5>Город: {{ client.city }}</h5>
                <h5>Тип лояльности: {{ client.loyalty.name }}</h5>
                <h5>Пол: {{ client.gender_name }}</h5>
                <h5>Дата рождения: {{ client.birth_date_formatted }}</h5>
                <v-data-table
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :loading="IS_LOADING"
                    loading-text="Отчеты обновляются"
                    :items="sales"
                    :search="search"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.products="{item}">
                        <v-list>
                            <v-list-item v-for="(product, index) of item.products" :key="index">
                                <v-list-item-content>
                                    <v-list-item-title>{{ product.product_name }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ product.attributes.join(", ") }}<span v-if="product.manufacturer.manufacturer_name">,</span> {{ product.manufacturer.manufacturer_name }}</v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <span>{{ product.count }} шт</span>
                                    <span v-if="product.discount > 0">Скидка: {{ product.discount }}%</span>
                                </v-list-item-action>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.sale_data="{item}">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.client.client_name }}</v-list-item-title>
                                    <v-list-item-subtitle>Клиент</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.user.name }}</v-list-item-title>
                                    <v-list-item-subtitle>Продавец</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.store.name }}</v-list-item-title>
                                    <v-list-item-subtitle>Магазин</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.sale_type">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.sale_type }}</v-list-item-title>
                                    <v-list-item-subtitle>Тип продажи</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.economy="{item}">
                        <v-list>
                            <v-list-item v-if="IS_BOSS">
                                <v-list-item-content >
                                    <v-list-item-title>{{ item.purchase_price | priceFilters}}</v-list-item-title>
                                    <v-list-item-subtitle>Закупочная стоимость</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.fact_price | priceFilters}}</v-list-item-title>
                                    <v-list-item-subtitle>Фактическая стоимость</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.final_price | priceFilters}}</v-list-item-title>
                                    <v-list-item-subtitle>Финальная стоимость</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="IS_BOSS">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.margin | priceFilters}} (<span v-if="item.final_price > 0" :class="Math.ceil(item.margin * 100 / item.final_price) > 0 ? 'green--text' : 'red--text'">{{ Math.ceil(item.margin * 100 / item.final_price) }}%</span>)</v-list-item-title>
                                    <v-list-item-subtitle>Прибыль</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.additional_data="{item}">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-icon :color="item.is_delivery ? 'success': 'error'">
                                            {{ item.is_delivery ? 'mdi-check' : 'mdi-cancel' }}
                                        </v-icon>
                                    </v-list-item-title>
                                    <v-list-item-subtitle>Доставка</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.balance | priceFilters}}</v-list-item-title>
                                    <v-list-item-subtitle>Списано с баланса</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.certificate">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.certificate.amount | priceFilters}}</v-list-item-title>
                                    <v-list-item-subtitle>Оплачено сертификатом</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <div v-if="item.split_payment">
                                <v-list-item v-for="payment of item.split_payment" :key="`split_payment-${item.id}-${payment.payment_text}`">
                                    <v-list-item-content>
                                        <v-list-item-title>{{ payment.amount | priceFilters }}</v-list-item-title>
                                        <v-list-item-subtitle>{{ payment.payment_text }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </div>
                            <v-list-item v-if="item.comment">
                                <v-list-item-content>
                                    <v-list-item-title style="white-space: normal;">{{ item.comment }}</v-list-item-title>
                                    <v-list-item-subtitle style="white-space: normal;">Комментарий</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.is_booking">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <v-icon>mdi-check</v-icon>
                                    </v-list-item-title>
                                    <v-list-item-subtitle>По брони</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.is_booking">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.booking_paid_sum | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>Предоплата по брони</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.payment_type_text="{item}">
                        {{ item.payment_type_text }}
                    </template>
                    <template v-slot:item.discount="{item}">
                        {{ item.discount }}%
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
import axios from 'axios';
export default {
    data: () => ({
        client: null,
        sales: [],
        search: '',
        headers: [
            {text: 'Список проданных товаров', value: 'products', align: ' min-w-250 w-30'},
            {text: 'Дата', value: 'date', align: ' font-weight-black'},
            {text: 'Способ оплаты', value: 'payment_type_text', align: ' font-weight-bold'},
            {text: 'Данные', value: 'sale_data'},
            {text: 'Экономические показатели', value: 'economy'},
            {text: 'Дополнительные данные', value: 'additional_data'},
        ],
    }),
    async mounted() {
        await this.$loading.enable();
        const { data } = await axios.get(`/api/clients/${this.$route.params.id}`)
        this.client = data.client;
        this.sales = data.sales;
        await this.$loading.disable();
    }
}
</script>

<style lang="scss" scoped></style>
