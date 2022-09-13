<template>
    <div>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card>
            <v-card-text>
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
                    :items="arrivals"
                    :footer-props="{
                        'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                        'items-per-page-text': 'Записей на странице',
                    }"
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
                                        Общая сумма
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
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-btn color="primary" @click="current_arrival = item; arrivalModal = true;">
                                        Информация <v-icon>mdi-information-outline</v-icon>
                                    </v-btn>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-btn color="success" @click="printWaybill(item.id)">
                                        Накладная <v-icon>mdi-file-excel</v-icon>
                                    </v-btn>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-btn color="error" @click="current_arrival = item; confirmationModal = true;" v-if="IS_SUPERUSER && !IS_MARKETOLOG">
                                        Отмена <v-icon>mdi-cancel</v-icon>
                                    </v-btn>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ArrivalInfoModal
            :state="arrivalModal"
            :arrivalProp="current_arrival"
            :confirm-mode="false"
            @cancel="arrivalModal = false; current_arrival = {}"
            :search="search"
        />
        <ConfirmationModal
            :state="confirmationModal"
            message="Вы действительно хотите отменить поставку?"
            :on-confirm="cancelArrival"
            @cancel="confirmationModal = false; current_arrival = {}"
        />
    </div>
</template>

<script>
import {cancelArrival, getArrivals} from "@/api/arrivals";
import ArrivalInfoModal from "@/components/Modal/ArrivalInfoModal";
import axios from "axios";
import ConfirmationModal from "@/components/Modal/ConfirmationModal";

export default {
    components: {ConfirmationModal, ArrivalInfoModal},
    data: () => ({
        search: '',
        overlay: true,
        loading: false,
        confirmationModal: false,
        arrivals: [],
        current_arrival: {},
        arrivalModal: false,
        headers: [
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
        ],
    }),
    methods: {
        async printWaybill(id) {
            this.loading = true;
            const { data } = await axios.get(`/api/excel/transfer/waybill?arrival=${id}`)
            const link = document.createElement('a');
            link.href = data.path;
            link.click();
            this.loading = false;
        },
        async cancelArrival() {
            this.confirmationModal = false;
            this.loading = true;
            await cancelArrival(this.current_arrival.id);
            this.arrivals = this.arrivals.filter(s => s.id !== this.current_arrival.id);
            this.current_arrival = {};
            this.loading = false;
            this.$toast.success('Поставка отменена!');
        }
    },
    computed: {},
    async mounted() {
        const { data } = await getArrivals(true);
        this.arrivals = data.map(arrival => {
            arrival.search = arrival.products.map(product => {
                return `${product.product_name} ${product.manufacturer.manufacturer_name} ${product.attributes.map(a => a.attribute_value).join(' ')}`
            }).join(' ')
            return arrival
        });
        this.overlay = false;
    }
}
</script>

<style scoped>

</style>
