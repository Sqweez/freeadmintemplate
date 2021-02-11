<template>
    <div>
        <v-card>
            <v-card-title>
                Заказы с интернет-магазина
            </v-card-title>
            <v-card-text>
                <v-data-table
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :items="orders"
                    :items-per-page="10"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                    <template v-slot:item.total_price="{ item }">
                        <span>{{ item.total_price | priceFilters }}</span>
                    </template>
                    <template v-slot:item.delivery="{ item }">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.payment_text }}</v-list-item-title>
                                    <v-list-item-subtitle>Способ оплаты</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.delivery_text }}</v-list-item-title>
                                    <v-list-item-subtitle>Способ доставки</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <span v-if="item.status === -1" class="color-text--red">
                                              {{ item.status_text }}
                                            <v-icon color="red">mdi-cancel</v-icon>
                                        </span>
                                        <span v-if="item.status === 1" class="color-text--green">
                                              {{ item.status_text }}
                                            <v-icon color="success">mdi-check</v-icon>
                                        </span>
                                        <span v-if="item.status === 0">
                                              {{ item.status_text }}
                                        </span>
                                    </v-list-item-title>
                                    <v-list-item-subtitle>Статус заказа</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.products="{item}">
                        <v-list>
                            <v-list-item v-for="(product, index) of item.products" :key="index">
                                <v-list-item-content>
                                    <v-list-item-title>{{ product.product_name }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ product.attributes.map(a => a.attribute_value).join(", ") }}<span v-if="product.manufacturer">,</span> {{ product.manufacturer }}</v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <span>{{ product.count }} шт.</span>
                                </v-list-item-action>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.information="{ item }">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.city_text }}</v-list-item-title>
                                    <v-list-item-subtitle>Город</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.address }}</v-list-item-title>
                                    <v-list-item-subtitle>Адрес</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.comment">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.comment }}</v-list-item-title>
                                    <v-list-item-subtitle>Комментарий</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.discount > 0">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.discount }}%</v-list-item-title>
                                    <v-list-item-subtitle>Скидка</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.balance > 0">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.balance }}</v-list-item-title>
                                    <v-list-item-subtitle>Списано с баланса</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn text v-if="item.status === 0" color="red">
                            Отменить заказ
                            <v-icon >mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn text v-if="item.status === 0" color="success">
                            Заказ выполнен
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                        <v-btn text v-if="item.status !== 0" color="red" @click="orderId = item.id; deleteModal = true;">
                            Удалить заказ из истории
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранный заказ?"
            :on-confirm="deleteOrder"
            @cancel="orderId = null; deleteModal = false;"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    export default {
        components: {ConfirmationModal},
        data: () => ({
            deleteModal: false,
            orderId: null,
            headers: [
                {
                    value: 'client_name',
                    text: 'Клиент',
                    sortable: true,
                },
                {
                    value: 'store.name',
                    text: 'Магазин',
                    sortable: true,
                },
                {
                    value: 'date',
                    text: 'Дата',
                    sortable: false,
                },
                {
                    value: 'products',
                    text: 'Товары',
                    sortable: false,
                },
                {
                    value: 'total_price',
                    text: 'Общая сумма',
                    sortable: false
                },
                {
                    value: 'delivery',
                    text: 'Данные доставки',
                    sortable: false,
                },
                {
                    value: 'information',
                    text: 'Дополнительная информация',
                    sortable: false,
                    align: ' max-width-200'
                },
                {
                    value: 'actions',
                    text: 'Действие',
                    sortable: false
                }
            ]
        }),
        methods: {
            async deleteOrder() {
                await this.$store.dispatch('DELETE_ORDER', this.orderId);
                this.order = null;
                this.deleteModal = false;
            },
        },
        computed: {
            orders() {
                return this.$store.getters.ORDERS;
            }
        },
        async created() {
            await this.$store.dispatch('GET_ORDERS');
        }
    }
</script>

<style scoped>
    .max-width-200 {
        max-width: 200px!important;
    }

    .v-list-item__title {
        white-space: normal;
    }
</style>
