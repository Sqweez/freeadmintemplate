<template>
    <div>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card>
            <v-card-text>
                <h5 v-if="IS_SUPERUSER">Общая сумма: {{totalPurchasePrice | priceFilters}}</h5>
                <h5>Общая продажная сумма: {{totalProductPrice | priceFilters}}</h5>
                <v-text-field
                    label="Поиск по поступлениям"
                    append-icon="search"
                    clearable
                    v-model="search"
                />
                <v-data-table
                    :search="search"
                    class="background-tooman-grey fz-18 mt-2"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :items="_arrivals"
                    :footer-props="{
                        'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                        'items-per-page-text': 'Записей на странице',
                    }"
                >
                    <template v-slot:item.total_cost="{item}">
                        <span v-if="IS_SUPERUSER">
                            {{ item.total_cost | priceFilters }}
                        </span>
                        <span v-else>
                            {{ 0 | priceFilters }}
                        </span>
                    </template>
                    <template v-slot:item.total_sale_cost="{item}">
                        {{ item.total_sale_cost | priceFilters }}
                    </template>
                    <template v-slot:item.product_count="{item}">
                        {{ item.product_count }} шт.
                    </template>
                    <template v-slot:item.position_count="{item}">
                        {{ item.position_count }} шт.
                    </template>
                    <template v-slot:item.store="{item}">
                        <span v-if="!editMode">
                            {{ item.store }}
                        </span>
                        <v-select
                            :items="stores"
                            item-value="id"
                            item-text="name"
                            v-model="storeId"
                            v-else/>
                    </template>
                    <template v-slot:item.arrived_at="{item}">
                        <span v-if="!editMode">{{ item.arrived_at }}</span>
                        <v-text-field
                            v-else
                            label="Ожидаемое поступление"
                            v-model="arrivedAt"
                            type="date"
                        />
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-flex v-if="!editMode">
                            <v-btn
                                icon
                                color="primary"
                                @click="current_arrival = item; arrivalModal = true; editArrivalMode = true;"
                                v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                icon
                                color="primary"
                                @click="current_arrival = item; arrivalModal = true; editArrivalMode = false;"
                            >
                                <v-icon>mdi-information-outline</v-icon>
                            </v-btn>
                            <v-btn
                                icon
                                color="error"
                                @click="current_arrival = item; confirmationModal = true;"
                                v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                            >
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                            <v-btn
                                icon
                                color="success"
                                @click="printWaybill(item.id)"
                                v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                            >
                                <v-icon>mdi-file-excel</v-icon>
                            </v-btn>
                            <v-btn
                                icon
                                color="primary"
                                @click="editMode = true; arrivalId = item.id; storeId = item.store_id; paymentCost = item.payment_cost; comment = item.comment; arrivedAt = item.arrived_at"
                                v-if="IS_SUPERUSER && !IS_MARKETOLOG"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                v-if="!IS_MARKETOLOG"
                                icon
                                color="primary"
                                @click="current_arrival = {...item}; bookingModal = true;"
                            >
                                <v-icon>mdi-lock</v-icon>
                            </v-btn>
                        </v-flex>
                        <v-flex v-else>
                            <v-btn icon color="danger" @click="arrivalId = null; editMode = false; storeId = null;">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                            <v-btn icon color="success" @click="editArrival">
                                <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </v-flex>

                    </template>
                    <template v-slot:item.payment_cost="{item}">
                        <span v-if="!editMode">
                            {{ item.payment_cost | priceFilters }}
                        </span>
                        <v-text-field
                            v-else
                            label="Стоимость доставки"
                            type="number"
                            v-model="paymentCost"
                        />
                    </template>
                    <template v-slot:item.comment="{item}">
                        <span v-if="!editMode">
                            {{ item.comment }}
                        </span>
                        <v-text-field
                            v-else
                            label="Комментарий"
                            v-model="comment"
                        />
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ArrivalInfoModal
            :state="arrivalModal"
            :arrival="current_arrival"
            :edit-mode="editArrivalMode"
            @cancel="arrivalModal = false; current_arrival = {}"
            @submit="onSubmit"
            @edit="onEdit"
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

    export default {
        components: {BookingModal, ConfirmationModal, ArrivalInfoModal},
        data: () => ({
            search: '',
            overlay: true,
            loading: false,
            editArrivalMode: false,
            arrivals: [],
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
            async getArrivals() {
                this.overlay = true;
                const {data} = await getArrivals(false);
                this.arrivals = data.map(arrival => {
                    arrival.search = arrival.products.map(product => {
                        return `${product.product_name} ${product.manufacturer.manufacturer_name} ${product.attributes.map(a => a.attribute_value).join(' ')}`
                    }).join(' ')
                    return arrival
                });
                this.overlay = false;
            },
            async getArrival(id) {
                this.overlay = true;
                const {data} = await getArrival(id);
                this.arrivals = this.arrivals.map(arrival => {
                    if (arrival.id === data.id) {
                        arrival = data;
                    }
                    return arrival;
                })
                this.overlay = false;
            },
            async onBookingSubmit() {
                await this.getArrivals();
                this.arrival = {};
                this.bookingModal = false;
            },
            async onSubmit() {
                this.arrivals = this.arrivals.filter(a => a.id !== this.current_arrival.id);
                this.arrivalModal = false;
                this.current_arrival = {}
            },
            async onEdit() {
                this.loading = true;
                await this.getArrival(this.current_arrival.id);
                this.arrivalModal = false;
                this.current_arrival = {};
            },
            async deleteArrival() {
                await deleteArrival(this.current_arrival.id);
                this.arrivals = this.arrivals.filter(a => a.id !== this.current_arrival.id);
                this.confirmationModal = false;
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
            headers() {
                return this.IS_SUPERUSER ?
                    [
                        {
                            text: 'Количество позиций',
                            value: 'position_count',
                        },
                        {
                            text: 'Количество товаров',
                            value: 'product_count',
                        },
                        {
                            text: 'Общая сумма',
                            value: 'total_cost'
                        },
                        {
                            text: 'Общая продажная сумма',
                            value: 'total_sale_cost'
                        },
                        {
                            text: 'Пользователь',
                            value: 'user',
                        },
                        {
                            text: 'Склад',
                            value: 'store',
                        },
                        {
                            text: 'Дата создания',
                            value: 'date',
                        },
                        {
                            text: 'Ожидаемое поступление',
                            value: 'arrived_at',
                        },
                        {
                            text: 'Комментарий',
                            value: 'comment',
                        },
                        {
                            text: 'Стоимость доставки',
                            value: 'payment_cost'
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
                    ] : [
                        {
                            text: 'Количество позиций',
                            value: 'position_count',
                        },
                        {
                            text: 'Количество товаров',
                            value: 'product_count',
                        },
                        {
                            text: 'Общая продажная сумма',
                            value: 'total_sale_cost'
                        },
                        {
                            text: 'Пользователь',
                            value: 'user',
                        },
                        {
                            text: 'Склад',
                            value: 'store',
                        },
                        {
                            text: 'Дата создания',
                            value: 'date',
                        },
                        {
                            text: 'Ожидаемое поступление',
                            value: 'arrived_at',
                        },
                        {
                            text: 'Комментарий',
                            value: 'comment',
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
            _arrivals() {
                return this.arrivals.filter(s => {
                    if (this.arrivalId !== null) {
                        return s.id === this.arrivalId;
                    }
                    return s;
                })
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
