<template>
    <div>
        <i-card-page title="Каспи заказы">
            <v-select
                :items="orderStates"
                item-text="title"
                item-value="value"
                label="Состояние заказа"
                v-model="filters.state"
            />
            <v-text-field
                label="Поиск по коду"
                @input="onCodeChange"
            />
            <v-data-table
                :items-per-page="10"
                :headers="headers"
                :items="items"
                :page.sync="page"
                :server-items-length="totalCount"
                :loading="loading"
                hide-default-footer
            >
                <template v-slot:item.code="{item}">
                    <a :href="item.external_url" target="_blank">
                        {{ item.code }}
                    </a>
                </template>
                <template v-slot:item.products="{ item }">
                    <v-list>
                        <v-list-item v-for="(product, idx) of item.products" :key="idx">
                            <v-list-item-content>
                                <v-list-item-title>{{ product.attributes.offer.name }}</v-list-item-title>
                            </v-list-item-content>
                            <v-list-item-action>
                                <span>{{ product.attributes.quantity }} шт</span>
                                <span><b>{{ product.attributes.totalPrice | priceFilters }}</b></span>
                            </v-list-item-action>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.additional="{ item }">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.status }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Статус заказа
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.store.name }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Склад в CRM
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.creation_date_formatted }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Дата поступления заказа
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.delivery_type }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Способ доставки
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.payment_type }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Способ оплаты
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ item.signature_required }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    Требует подписания
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        v-if="item.state === 'NEW'"
                        small
                        color="success"
                        depressed
                        @click="createOrder(item)"
                    >
                        Создать заказ в CRM
                    </v-btn>
                </template>
            </v-data-table>
            <div style="display: flex; align-items: center; justify-content: space-between">
                <div>
                    {{ paginationText }}
                </div>
                <v-pagination
                    :total-visible="6"
                    v-model="page"
                    :length="pageCount"
                />
            </div>
        </i-card-page>
    </div>
</template>

<script>
import KaspiRepository from '@/repositories/KaspiRepository';
import { KASPI_ORDER_STATES } from '@/common/types/KaspiTypes';
import { debounce } from 'lodash';

export default {
    data: () => ({
        kaspiRepository: KaspiRepository,
        orderStates: KASPI_ORDER_STATES,
        filters: {
            state: 'NEW',
            code: null,
        },
        headers: [
            { text: 'Номер заказа', value: 'code' },
            //{ text: "Время на обработку", value: "processing_time" },
            { text: 'Состав заказа', value: 'products' },
            { text: 'Покупатель', value: 'client.fullName' },
            { text: 'Сумма заказа', value: 'total_price' },
            { text: 'Дополнительная информация', value: 'additional' },
            { text: 'Адрес самовывоза / доставки', value: 'destination_point' },
            //{ text: 'Способ оплаты', value: 'payment_type' },
            //{ text: 'Требует подписания', value: 'signature_required' },
            { text: 'Точка передачи заказа', value: 'pickup_point' },
            { text: 'Действие', value: 'actions' }
        ],
        items: [],
        page: 1,
        pageCount: null,
        totalCount: null,
        loading: true,
        searchCode: '',
    }),
    methods: {
        onCodeChange: debounce(function(value) {
            this.filters.code = value.trim();
        }, 500),
        async _getOrders(code = null) {
            this.loading = true;
            this.$loading.enable();
            try {
                const { data: { data, meta } } = await this.kaspiRepository.getOrders({
                    filters: {
                        ...this.filters,
                    },
                    page: this.page - 1
                });
                this.items = data;
                this.pageCount = meta.pageCount;
                this.totalCount = meta.totalCount;
                this.page = meta.current_page + 1;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
                this.$loading.disable();
            }
        },
        createOrder (item) {
            console.log(item);
            this.$toast.success('Эта кнопка будет создавать заказ в CRM')
        },
    },
    computed: {
        paginationText() {
            const start = (this.page - 1) * 10 + 1; // Начало текущей страницы
            const end = Math.min(this.page * 10, this.totalCount); // Конец текущей страницы
            return `${start} - ${end} из ${this.totalCount}`;
        }
    },
    watch: {
        filters: {
            handler() {
                this.page = 1;
                this._getOrders();
            },
            immediate: true,
            deep: true
        },
        page() {
            this._getOrders();
        }
    }
};
</script>

<style scoped>

</style>
