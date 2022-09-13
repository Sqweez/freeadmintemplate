<template>
    <div>
        <v-card>
            <v-card-text>
                <h5 v-if="IS_SUPERUSER">Общая закупочная сумма: {{totalPurchasePrice | priceFilters}}</h5>
                <h5>Общая продажная сумма: {{totalProductPrice | priceFilters}}</h5>
                <v-text-field
                    label="Поиск по поступлениям"
                    append-icon="search"
                    clearable
                    v-model="search"
                />
                <v-data-table
                    :search="search"
                    :headers="headers"
                    :items="arrivals"
                >
                    <template v-slot:item.common_info="{ item }">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.store }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Склад
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.user }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Пользователь
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.date }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Дата создания
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.comment">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.comment }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Комментарий
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="item.arrive_date">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.arrive_date }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Ожидаемая дата
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.economy_info="{ item }">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.position_count }} шт
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Количество позиций
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.product_count }} шт
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Количество товаров
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="IS_SUPERUSER">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.total_cost | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Общая закупочная сумма
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.total_sale_cost | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Общая продажная сумма
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item v-if="IS_SUPERUSER">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.payment_cost | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Сумма доставки
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-expansion-panels flat accordion>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Действия
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-list v-if="!editMode">
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-btn
                                                    color="primary"
                                                    @click="current_arrival = item; arrivalModal = true; editArrivalMode = true;"
                                                    v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                                                >
                                                    Редактировать <v-icon>mdi-pencil</v-icon>
                                                </v-btn>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-btn
                                                    color="success"
                                                    @click="current_arrival = item; arrivalModal = true; editArrivalMode = false;"
                                                >
                                                    Подтвердить <v-icon>mdi-check</v-icon>
                                                </v-btn>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-btn
                                                    color="error"
                                                    @click="current_arrival = item; confirmationModal = true;"
                                                    v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                                                >
                                                    Отмена <v-icon>mdi-cancel</v-icon>
                                                </v-btn>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-btn
                                                    color="success"
                                                    @click="printWaybill(item.id)"
                                                    v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                                                >
                                                    Накладная <v-icon>mdi-file-excel</v-icon>
                                                </v-btn>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-btn
                                                    v-if="!IS_MARKETOLOG"
                                                    color="primary"
                                                    @click="current_arrival = {...item}; bookingModal = true;"
                                                >
                                                    Бронь <v-icon>mdi-lock</v-icon>
                                                </v-btn>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                    <v-flex v-else>
                                        <v-btn icon color="danger" @click="arrivalId = null; editMode = false; storeId = null;">
                                            <v-icon>mdi-cancel</v-icon>
                                        </v-btn>
                                        <v-btn icon color="success" @click="editArrival">
                                            <v-icon>mdi-check</v-icon>
                                        </v-btn>
                                    </v-flex>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ArrivalInfoModal
            :state="arrivalModal"
            :arrival-prop="current_arrival"
            :edit-mode="editArrivalMode"
            @cancel="arrivalModal = false; current_arrival = {}"
            :search="search"
        />
        <ConfirmationModal
            :state="confirmationModal"
            message="Вы действительно хотите удалить выбранную поставку?"
            :on-confirm="deleteArrival"
            v-on:cancel="current_arrival = {}; confirmationModal = false"
        />
        <BookingModal
            :state="bookingModal"
            :arrival="current_arrival"
            @cancel="bookingModal = false; current_arrival = {}"
            @submit="onBookingSubmit"
        />
    </div>
</template>

<script>
import {deleteArrival, editArrival, getArrival, getArrivals} from "@/api/arrivals";
import ArrivalInfoModal from "@/components/Modal/ArrivalInfoModal";
import ConfirmationModal from "@/components/Modal/ConfirmationModal";
import axios from "axios";
import {TOAST_TYPE} from "@/config/consts";
import BookingModal from "@/components/Modal/BookingModal";
import ACTIONS from "@/store/actions";
import {mapActions} from 'vuex';

export default {
    components: {BookingModal, ConfirmationModal, ArrivalInfoModal},
    data: () => ({
        search: '',
        loading: false,
        editArrivalMode: false,
        current_arrival: {},
        arrivalModal: false,
        confirmationModal: false,
        editMode: false,
        storeId: null,
        arrivalId: null,
        bookingModal: false,
        paymentCost: 0,
        arrivedAt: null,
        comment: '',
    }),
    methods: {
        ...mapActions({
            '$getNotCompletedArrivals': 'getNotCompletedArrivals',
            '$deleteArrival': 'deleteArrival',
        }),
        async getArrivals() {
            this.$loading.enable('Данные загружаются...');
            await this.$getNotCompletedArrivals();
            this.$loading.disable();
        },
        async onBookingSubmit() {
            await this.getArrivals();
            this.arrival = {};
            this.bookingModal = false;
        },
        async deleteArrival() {
            this.confirmationModal = false;
            this.$loading.enable();
            await this.$deleteArrival(this.current_arrival.id);
            this.$loading.disable();
            this.current_arrival = {}
        },
        async printWaybill(id) {
            this.loading = true;
            const {data} = await axios.get(`/api/excel/transfer/waybill?arrival=${id}`)
            const link = document.createElement('a');
            link.href = data.path;
            link.click();
            this.loading = false;
        },
        async editArrival() {
            try {
                const {data} = await editArrival({
                    id: this.arrivalId,
                    store_id: this.storeId,
                    payment_cost: this.paymentCost,
                    arrived_at: this.arrivedAt,
                    comment: this.comment,
                });
                this.arrivals = this.arrivals.map(s => {
                    if (s.id === data.data.id) {
                        s = data.data;
                    }
                    return s;
                })
                this.editMode = false;
                this.arrivalId = null;
                this.storeId = null;
                this.arrivedAt = null;
                this.$toast.success('Поступление отредактировано!')
            } catch (e) {
                this.$toast.error('Произошла ошибка', TOAST_TYPE.ERROR)
            }
        }
    },
    computed: {
        arrivals () {
            return this.$store.getters.ARRIVALS;
        },
        headers() {
            return [
                {
                    text: 'Общая информация',
                    value: 'common_info'
                },
                {
                    text: 'Общая информация',
                    value: 'economy_info'
                },
                {
                    text: 'Комментарий',
                    value: 'comment',
                    align: ' d-none'
                },
                {
                    text: 'Действие',
                    value: 'actions',
                    sortable: false
                },
                {
                    text: 'Поиск',
                    value: 'search',
                    align: ' d-none'
                }
            ];
        },
        totalPurchasePrice() {
            return this.arrivals.reduce((a, c) => {
                return a + +c.total_cost;
            }, 0);
        },
        totalProductPrice() {
            return this.arrivals.reduce((a, c) => {
                return a + +c.total_sale_cost;
            }, 0);
        },
        stores() {
            return this.$store.getters.stores;
        }
    },
    async mounted() {
        await this.getArrivals();
        await this.$store.dispatch(ACTIONS.GET_CLIENTS);
    }
}
</script>

<style scoped>

</style>
