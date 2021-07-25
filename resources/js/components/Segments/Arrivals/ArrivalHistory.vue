<template>
    <div>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card>
            <v-card-text>
                <v-data-table
                        class="background-iron-grey fz-18 mt-2"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :headers="headers"
                        :items="arrivals"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.product_count="{item}">
                        {{ item.product_count }} шт.
                    </template>
                    <template v-slot:item.position_count="{item}">
                        {{ item.position_count }} шт.
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn icon color="primary" @click="current_arrival = item; arrivalModal = true;">
                            <v-icon>mdi-information-outline</v-icon>
                        </v-btn>
                        <v-btn icon color="success" @click="printWaybill(item.id)">
                            <v-icon>mdi-file-excel</v-icon>
                        </v-btn>
                        <v-btn icon color="error" @click="current_arrival = item; confirmationModal = true;">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:item.total_cost="{item}">
                        {{ item.total_cost | priceFilters }}
                    </template>
                    <template v-slot:item.total_sale_cost="{item}">
                        {{ item.total_sale_cost | priceFilters }}
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
                :confirm-mode="false"
                @cancel="arrivalModal = false; current_arrival = {}"
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
    import {cancelArrival, getArrivals} from "../../../api/arrivals";
    import ArrivalInfoModal from "../../Modal/ArrivalInfoModal";
    import axios from "axios";
    import ConfirmationModal from "../../Modal/ConfirmationModal";
    import showToast from "../../../utils/toastService";

    export default {
        components: {ConfirmationModal, ArrivalInfoModal},
        data: () => ({
            overlay: true,
            loading: false,
            confirmationModal: false,
            arrivals: [],
            current_arrival: {},
            arrivalModal: false,
            headers: [
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
                    text: 'Действие',
                    value: 'actions',
                    sortable: false
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
                showToast('Поставка отменена!');
            }
        },
        computed: {},
        async mounted() {
            const { data } = await getArrivals(true);
            this.arrivals = data;
            this.overlay = false;
        }
    }
</script>

<style scoped>

</style>
