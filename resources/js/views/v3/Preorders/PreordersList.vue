<template>
    <div>
        <v-row>
            <v-col>
                <v-text-field
                    label="Поиск"
                    clearable
                    v-model="searchText"
                />
            </v-col>
            <v-col>
                <v-select
                    :items="[
                {
                    name: 'Все',
                    status: -2
                },
                {
                    name: 'Отмененные',
                    status: -1
                },
                {
                    name: 'Новые',
                    status: 0
                },
                {
                    name: 'Выполненные',
                    status: 1
                }
            ]"
                    item-value="status"
                    item-text="name"
                    v-model="statusFilter"
                    label="Фильтр по статусу"
                />
            </v-col>
        </v-row>
        <v-data-table
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :headers="headers"
            :loading="loading"
            :search="searchText"
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
                    v-if="item.status === 0"
                    icon
                    color="error"
                    @click="cancelModal = true; preorderId = item.id">
                        <v-icon>mdi-cancel</v-icon>
                </v-btn>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <ConfirmationModal
            message="Вы действительно хотите отменить выбранный предзаказ?"
            @cancel="cancelModal = false; preorderId = null;"
            :on-confirm="cancelPreorder"
            :state="cancelModal"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {
            ConfirmationModal
        },
        data: () => ({
            statusFilter: -2,
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
            loading: false,
            searchText: '',
            preorderId: null,
            cancelModal: false,
        }),
        methods: {
            async cancelPreorder() {
                try {
                    await this.$store.dispatch('CANCEL_PREORDER', this.preorderId);
                    this.cancelModal = false;
                    this.preorderId = null;
                    this.$toast.success('Предзаказ отменен');
                } catch (e) {
                    this.$toast.error('Произошла ошибка')
                }
            }
        },
        computed: {
            preorders() {
                return this.$store.getters.PREORDERS.filter(p => {
                    if (this.statusFilter === -2) {
                        return p;
                    }
                    return p.status === this.statusFilter;
                });
            }
        },
        async created() {
            await this.$store.dispatch('GET_PREORDERS');
        }
    }
</script>

<style scoped>

</style>
