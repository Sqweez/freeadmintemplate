<template>
    <v-dialog
        persistent
        max-width="1000"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span
                    class="white--text">Бронирование</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <v-simple-table v-if="!loading">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Стоимость</th>
                            <th>Количество</th>
                            <th>Ожидается</th>
                            <th>Доступно для бронирования</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, idx) of products" :key="idx">
                            <td>
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
                            </td>
                            <td>
                                <span>
                                    {{ item.product_price | priceFilters }}
                                </span>
                            </td>
                            <td style="min-width: 200px;">
                                <v-btn icon color="error" @click="decreaseCount(idx)">
                                    <v-icon>
                                        mdi-minus
                                    </v-icon>
                                </v-btn>
                                {{ item.quantity }}
                                <v-btn icon color="success" @click="increaseCount(idx)">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </v-btn>
                            </td>
                            <td>
                                {{ item.count }} шт
                            </td>
                            <td>
                                {{ item.available_booking_count }} шт
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <h5>Общая сумма: {{ totalPrice | priceFilters }}</h5>
                <v-flex>
                    <v-row>
                        <v-col>
                            <v-text-field
                                label="Внесено предоплаты"
                                v-model="paidSum"
                                type="number"
                            />
                        </v-col>
                        <v-col>
                            <v-autocomplete
                                label="Клиент"
                                :items="clients"
                                item-value="id"
                                item-text="client_name"
                                v-model="clientId"
                            />
                        </v-col>
                        <v-col>
                            <v-select
                                label="Магазин"
                                :items="stores"
                                item-value="id"
                                item-text="name"
                                v-model="storeId"
                                :disabled="!IS_SUPERUSER"
                            />
                        </v-col>
                    </v-row>
                </v-flex>
                <div
                    class="text-center d-flex align-center justify-center"
                    style="min-height: 300px"
                    v-if="loading">
                    <v-progress-circular
                        indeterminate
                        size="65"
                        color="primary"
                    ></v-progress-circular>
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Закрыть
                </v-btn>
                <v-spacer/>
                <v-btn color="success" text :disabled="!hasAccepted" @click="onSubmit">
                    Создать
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import ACTIONS from '@/store/actions'

    export default {
        props: {
            state: {
                default: false
            },
            arrival: {
                type: Object,
                default: {}
            },
        },
        watch: {
            state() {
                if (this.state) {
                    this.storeId = this.user.store_id;
                    this.products = JSON.parse(JSON.stringify(
                        this.arrival.products.map(p => {
                            p.quantity = 0;
                            p.accepted = false;
                            return p;
                        })
                    ));
                } else {
                    this.products = [];
                    this.arrival = {};
                }
            }
        },
        data: () => ({
            selected: [],
            products: [],
            booking: [],
            loading: false,
            clientId: null,
            storeId: null,
            paidSum: 0,
            headers: [
                {
                    text: 'Наименование',
                    value: 'product_name',
                    sortable: false,
                },
                {
                    text: 'Атрибуты',
                    value: 'attributes',
                    sortable: false,
                },
                {
                    text: 'Количество',
                    value: 'count',
                    sortable: false
                }
            ],
        }),
        methods: {
            async onSubmit() {
                const booking = {
                    arrival_id: this.arrival.id,
                    user_id: this.user.id,
                    client_id: this.clientId,
                    store_id: this.storeId,
                    paid_sum: this.paidSum
                };

                if (!booking.client_id) {
                    return this.$toast.error('Выберите клиента!');
                }

                if (!booking.store_id) {
                    return this.$toast.error('Выберите магазин!');
                }

                if (!booking.paid_sum) {
                    return this.$toast.error('Введите сумму предоплаты!');
                }


                const products = this.products.map(product => {
                    return {
                        product_id: product.id,
                        arrival_product_id: product.arrival_product_id,
                        product_price: product.product_price,
                        count: product.quantity,
                        purchase_price: product.purchase_price,
                    };
                })

                try {
                    await this.$store.dispatch(ACTIONS.CREATE_BOOKING, {
                        booking,
                        products
                    });
                    this.$toast.success('Товар успешно забронирован!');
                    this.$emit('submit');
                } catch {
                    this.$toast.error('При создании бронирования произошла ошибка');
                }


            },
            decreaseCount(idx) {
                const newValue = {
                    ...this.products[idx],
                    quantity: Math.max(0, this.products[idx].quantity - 1)
                };
                this.$set(this.products, idx, newValue)
            },
            increaseCount(idx) {
                const newValue = {
                    ...this.products[idx],
                    quantity: Math.min(this.products[idx].available_booking_count, this.products[idx].quantity + 1)
                };
                this.$set(this.products, idx, newValue)
            },
        },
        computed: {
            hasAccepted() {
                return this.products.reduce((a, c) => {
                    return a + c.quantity
                }, 0) > 0;
            },
            clients() {
                return this.$store.getters.clients;
            },
            totalPrice() {
                return this.products.reduce((a, c) => {
                    return a + c.quantity * c.product_price;
                }, 0);
            },
            stores() {
                return this.$store.getters.shops;
            },
            user() {
                return this.$store.getters.USER;
            }
        }
    }
</script>

<style scoped>

</style>
