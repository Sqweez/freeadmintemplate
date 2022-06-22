<template>
    <div>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card>
            <v-card-title>
                Продажа брони
            </v-card-title>
            <v-card-text>
                <div class="">
                    <div class="cart__parameters__checkboxes">
                        <div>
                            <v-checkbox
                                label="Бесплатно"
                                v-model="isFree"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div v-if="!isFree">
                            <v-checkbox
                                label="Kaspi Red"
                                v-model="isRed"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div>
                            <v-checkbox
                                label="Доставка"
                                v-model="isDelivery"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>
                        <div v-if="!isFree">
                            <v-checkbox
                                label="Раздельная оплата"
                                v-model="isSplitPayment"
                                class="ml-2 margin-28"
                                color="white darken-2"
                            />
                        </div>

                    </div>
                    <div class="cart__parameters">
                        <div v-if="!isFree">
                            <v-text-field
                                v-model.number="discountPercent"
                                class="w-100px"
                                type="number"
                                suffix="%"
                                :max="100"
                                color="white darken-2"
                                label="Скидка"
                                outlined
                            />
                        </div>
                        <div class="cart__payment-type" v-if="!isFree">
                            <v-select
                                label="Способ оплаты"
                                v-model="payment_type"
                                :items="payment_types"
                                :disabled="isRed || isSplitPayment"
                                item-text="name"
                                outlined
                                class="w-150px"
                                item-value="id"></v-select>
                        </div>
                        <div v-if="clientChosen && !isFree">
                            <v-text-field
                                class="w-150px"
                                type="number"
                                color="white darken-2"
                                v-model="balance"
                                label="Списать с баланса"
                                outlined
                            />
                        </div>
                        <v-textarea
                            rows="3"
                            auto-grow
                            v-model="comment"
                            label="Комментарий"
                        />
                    </div>
                    <div class="split__payment" v-if="isSplitPayment">
                        <div v-for="(type, index) of payment_types_without_split" :key="`split_type-${type.id}`">
                            <p>{{ type.name }}</p>
                            <v-text-field
                                class="w-100px"
                                type="number"
                                color="white darken-2"
                                outlined
                                v-model.number="splitPayment[index].amount"
                            />
                        </div>
                        <p>Оставьте значение 0, там где оплата не производится</p>
                        <p>Остаток: {{ splitPrice | priceFilters }}</p>
                    </div>
                </div>
                <v-divider></v-divider>
                <v-list v-if="client" class="d-flex">
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">ФИО</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Телефон</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_phone }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Скидка</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_discount }}%</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th>#</th>
                            <th>Товар</th>
                            <th>Количество</th>
                            <th>Цена</th>
                            <th>Стоимость</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
                        <tr v-for="(item, index) of booking.products" :key="`product-id-${item.id}`">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.join(', ') }}, {{ item.manufacturer }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>

                                <div class="d-flex align-center">
                                   {{ item.count }}
                                </div>
                            </td>
                            <td>{{ item.product_price | priceFilters}}</td>
                            <td>{{ item.product_price * item.count - (Math.max(discountPercent, 0) / 100 * item.product_price * item.count) | priceFilters }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Процент скидки</th>
                            <th class="text-center">Скидка</th>
                            <th class="text-center">Предоплата</th>
                            <th class="green--text darken-1 text-center">Итого к оплате</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr class="pt-5">
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal | priceFilters}}</td>
                            <td class="text-center">{{ discount }}%</td>
                            <td class="text-center">{{ discountTotal | priceFilters}}</td>
                            <td class="text-center">{{ paidSum | priceFilters}}</td>
                            <td class="text-center green--text darken-1">{{ finalPrice | priceFilters }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-btn depressed color="error" block style="font-size: 16px; margin-top: 10px;" @click="onSale">
                    Оформить заказ
                </v-btn>
            </v-card-text>
        </v-card>
        <ConfirmationModal
            :state="confirmationModal"
            message="Напечатать чек?"
            :cancel-message="'нет'"
            :on-confirm="printCheck"
            @cancel="confirmationModal = false"
        />
    </div>
</template>

<script>
    import axios from 'axios';
    import {TOAST_TYPE} from "@/config/consts";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal},
        data: () => ({
            overlay: false,
            clientCartModal: false,
            booking: {},
            isFree: false,
            isRed: false,
            isDelivery: false,
            isSplitPayment: false,
            splitPayment: [],
            payment_type: 0,
            comment: '',
            client: null,
            balance: 0,
            discountPercent: 0,
            products: [],
            paidSum: 0,
            confirmationModal: false
        }),
        methods: {
            printCheck() {
                this.confirmationModal = false;
                window.open(`/check/${this.sale_id}`, '_blank');
            },
            cancelClient() {
                this.client = null;
                this.partner_id = null;
                this.promocode = '';
                this.discountPercent = 0;
                this.promocodeSet = false;
            },
            async onSale() {
                const split_payment = this.isSplitPayment ? this.splitPayment.filter(p => p.amount > 0) : null;
                if (split_payment !== null && !split_payment.length) {
                    this.$toast.error('Раздельная оплата не заполнена');
                    return;
                }
                if (split_payment !== null) {
                    const total = split_payment.reduce((a, c) => {
                        return a + c.amount;
                    }, 0)
                    if (total !== this.finalPrice) {
                        this.$toast.error('Суммарная раздельная оплата не совпадает с итоговой суммой');
                        return;
                    }
                }
                const sale = {
                    user_id: !this.isDelivery ?  this.user.id : 1,
                    client_id: this.client.id,
                    discount: this.discount,
                    kaspi_red: this.isRed && !this.isFree,
                    payment_type: this.payment_type,
                    certificate: this.certificate,
                    split_payment: split_payment,
                    comment: this.comment,
                    is_delivery: this.isDelivery,
                    booking_id: this.booking.id
                };
                try {
                    this.overlay = true;
                    await this.createSale(sale);
                } catch (e) {
                    this.$toast.error('Произошла ошибка', TOAST_TYPE.ERROR);
                } finally {
                    this.overlay = false;
                }
            },
            async createSale(sale) {
                try {
                    this.sale_id = await this.$store.dispatch('MAKE_BOOKING_SALE_v2', sale);
                    this.$toast.success('Продажа совершена успешно!');
                    this.confirmationModal = true;
                } catch (e) {
                    throw e;
                }
            },
        },
        computed: {
            user() {
                return this.$store.getters.USER;
            },
            clientChosen() {
                return this.client && this.client.id !== -1;
            },
            payment_types() {
                return this.$store.getters.payment_types;
            },
            splitPrice() {
                return this.finalPrice - this.splitPayment.reduce((a, c) => {
                    return a + +c.amount;
                }, 0);
            },
            total() {
                return this.subtotal - this.discountTotal;
            },
            payment_types_without_split() {
                const payments = this.$store.getters.payment_types.filter(p => p.id !== 5);
                this.splitPayment = payments.map(p => ({payment_type: p.id, amount: 0}));
                return payments;
            },
            discount() {
                if (this.isFree) {
                    return 100;
                }
                if (!this.client) {
                    return Math.min(this.discountPercent, 100);
                }
                return Math.min(Math.max(this.discountPercent, (!this.isRed ? this.client.client_discount : 0), 0), 100);
            },
            finalPrice() {
                let total = this.total;
                if (this.balance > 0) {
                    total -= this.balance;
                }

                total -= this.paidSum;

                return Math.max(0, Math.ceil(total));
            },
            cartCount() {
                return this.products
                    .reduce((a, c) => {
                        return a + c.count;
                    }, 0);
            },
            subtotal() {
                return this.products.reduce((a, c) => {
                    return (c.product_price * c.count) + a;
                }, 0);
            },
            discountTotal() {
                return this.products.reduce((a, c) => {
                    return a + Math.max(this.discount, 0) /100 * c.product_price * c.count;
                }, 0);
            },
        },
        async created() {
            const { data } = await axios.get(`/api/v2/booking/${this.$route.params.id}`);
            this.booking = data.data;
            this.products = this.booking.products;
            this.paidSum = this.booking.paid_sum;
            this.client = this.booking.client;
        },
    }
</script>

<style scoped>

</style>
