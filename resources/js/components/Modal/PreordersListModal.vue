<template>
    <div>
        <v-dialog persistent v-model="state" max-width="1100">
            <v-card>
                <v-card-title class="headline justify-space-between">
                    <span class="white--text">Предзаказы</span>
                    <v-btn icon text class="float-right" @click="$emit('cancel')">
                        <v-icon color="white">
                            mdi-close
                        </v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-data-table
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :headers="headers"
                        loading-text="Отчеты обновляются"
                        :items="preorders"
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
                                        <v-list-item-title>{{ item.user }}</v-list-item-title>
                                        <v-list-item-subtitle>Продавец</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>{{ item.store }}</v-list-item-title>
                                        <v-list-item-subtitle>Магазин</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>{{ item.status_text }}</v-list-item-title>
                                        <v-list-item-subtitle>Статус</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>{{ item.amount | priceFilters }}</v-list-item-title>
                                        <v-list-item-subtitle>Предоплата</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </template>
                        <template v-slot:item.additional_data="{item}">
                            <v-list>
                                <v-list-item v-if="item.comment">
                                    <v-list-item-content>
                                        <v-list-item-title>{{ item.comment }}</v-list-item-title>
                                        <v-list-item-subtitle>Комментарий</v-list-item-subtitle>
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
                        <template v-slot:item.action="{item}">
                            <v-btn
                                icon
                                color="success"
                                @click="onPreorderChoose(item)">
                                <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    export default {
        data: () => ({
            headers: [
                {text: 'Список товаров', value: 'products', align: ' min-w-250 w-30'},
                {text: 'Дата', value: 'date', align: ' font-weight-black'},
                {text: 'Способ оплаты', value: 'payment_type_text', align: ' font-weight-bold'},
                {text: 'Данные', value: 'sale_data'},
                {text: 'Дополнительные данные', value: 'additional_data'},
                {
                    text: 'Действие', value: 'action'
                },
            ],
        }),
        methods: {
            onPreorderChoose(preorder) {
                this.$emit('preorder', preorder);
            },
        },
        computed: {
            preorders() {
                return this.$store.getters.ACTIVE_PREORDERS;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
                required: true
            },
        }
    }
</script>

<style scoped>

</style>
