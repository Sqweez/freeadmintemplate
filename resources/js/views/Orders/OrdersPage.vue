<template>
    <div>
        <v-card>
            <v-card-title>
                Заказы с интернет-магазина
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Статусы заказов"
                    :items="statuses"
                    v-model="statusFilter"
                    item-text="text"
                    item-value="id"
                />
                <input type="file" ref="fileInput" class="d-none" @change="uploadFile">
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
                        <span style="white-space: nowrap">{{ item.total_price | priceFilters }}</span>
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
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.phone }}</v-list-item-title>
                                    <v-list-item-subtitle>Телефон</v-list-item-subtitle>
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
                            <v-list-item v-if="item.payment === 2">
                                <v-list-item-content>
                                    <v-list-item-title>{{ item.is_paid_text }}</v-list-item-title>
                                    <v-list-item-subtitle>Статус оплаты</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.image">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        <img :src="'../../storage/' + item.image" width="150">
                                    </v-list-item-title>
                                    <v-list-item-subtitle>Изображение</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn text v-if="item.status === 0" color="red" @click="orderId = item.id; declineModal = true;">
                            Отменить заказ
                            <v-icon >mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn text v-if="item.status === 0" color="success" @click="orderId = item.id; acceptModal = true;">
                            Заказ выполнен
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                        <v-btn text v-if="item.status === 1 && IS_SUPERUSER" color="success" @click="orderId = item.id; restoreModal = true;">
                            Восстановить заказ
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                        <v-btn text v-if="item.status !== 0" color="red" @click="orderId = item.id; deleteModal = true;">
                            Удалить заказ из истории
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <v-btn text color="success" @click="orderId = item.id; $refs.fileInput.click()" v-if="item.status === 0">
                            Загрузить накладную
                            <v-icon>mdi-image</v-icon>
                        </v-btn>
                        <v-btn text color="primary" @click="orderId = item.id; orderModal = true;" v-if="item.status === 0">
                            Редактировать заказ
                            <v-icon>mdi-pencil</v-icon>
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
        <ConfirmationModal
            :state="declineModal"
            message="Вы действительно хотите отменить выбранный заказ?"
            :on-confirm="declineOrder"
            @cancel="orderId = null; declineModal = false;"
        />
        <ConfirmationModal
            :state="acceptModal"
            message="Вы действительно хотите подтвердить выбранный заказ?"
            :on-confirm="acceptOrder"
            @cancel="orderId = null; acceptModal = false;"
        />
        <ConfirmationModal
            :state="restoreModal"
            message="Вы действительно хотите восстановить выбранный заказ?"
            :on-confirm="restoreOrder"
            @cancel="orderId = null; restoreModal = false;"
        />
        <OrderModal :state="orderModal" :id="orderId" @cancel="orderModal = false; orderId = null;"/>
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import uploadFile from "@/api/upload";
    import OrderModal from "@/components/v2/Modal/OrderModal";

    export default {
        components: {OrderModal, ConfirmationModal},
        data: () => ({
            deleteModal: false,
            declineModal: false,
            acceptModal: false,
            orderModal: false,
            restoreModal: false,
            statusFilter: -2,
            orderId: null,
            statuses: [
                {
                    id: -2,
                    text: 'Все'
                },
                {
                    id: 0,
                    text: 'В обработке'
                },
                {
                    id: -1,
                    text: 'Отмененные'
                },
                {
                    id: 1,
                    text: 'Выполненные'
                },
            ],
            headers: [
                {
                    value: 'id',
                    text: 'Номер заказа',
                    sortable: true
                },
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
            async restoreOrder() {
                await this.$store.dispatch('RESTORE_ORDER', this.orderId);
                this.orderId = null;
                this.restoreModal = false;
            },
            async deleteOrder() {
                await this.$store.dispatch('DELETE_ORDER', this.orderId);
                this.orderId = null;
                this.deleteModal = false;
            },
            async acceptOrder() {
                await this.$store.dispatch('ACCEPT_ORDER', this.orderId);
                this.orderId = null;
                this.acceptModal = false;
            },
            async declineOrder() {
                await this.$store.dispatch('DECLINE_ORDER', this.orderId);
                this.orderId = null;
                this.declineModal = false;
            },
            async uploadFile(e) {
                try {
                    const file = e.target.files[0];
                    const response = await uploadFile(file, 'file', 'orders');
                    const image = response.data;
                    await this.$store.dispatch('SET_ORDER_IMAGE', {
                        order_id: this.orderId,
                        image: image,
                    })
                } catch (e) {
                    this.$toast.error('Во время загрузки файла произошла ошибка, попробуйте загрузить другую фотографию');
                } finally {
                    this.$refs.fileInput.value = null;
                }
            }
        },
        computed: {
            orders() {
                let orders = this.$store.getters.ORDERS;
                if (this.IS_FRANCHISE) {
                   orders = orders.filter(o => o.city == this.$user.store.city_id);
                }
                if (this.statusFilter === -2) {
                    return orders;
                } else {
                    return orders.filter(order => order.status === this.statusFilter);
                }
            }
        },
        async created() {
            await this.$store.dispatch('GET_ORDERS');
            await this.$store.dispatch('GET_PRODUCTS_v2');
            await this.$store.dispatch('GET_PRODUCTS_QUANTITIES', 1);
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
