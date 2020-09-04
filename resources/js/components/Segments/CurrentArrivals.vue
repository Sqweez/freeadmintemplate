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
                   <!-- <template v-slot:item.total_cost="{item}">
                        {{ item.total_cost }}₸
                    </template>
                    -->
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
                        <v-btn icon color="error" @click="current_arrival = item; confirmationModal = true;">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn icon color="success" @click="printWaybill(item.id)">
                            <v-icon>mdi-file-excel</v-icon>
                        </v-btn>
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
            @cancel="arrivalModal = false; current_arrival = {}"
            @submit="onSubmit"
        />
        <ConfirmationModal
            :state="confirmationModal"
            message="Вы действительно хотите удалить выбранную поставку?"
            :on-confirm="deleteArrival"
            v-on:cancel="current_arrival = {}; confirmationModal = false"
        />
    </div>
</template>

<script>
    import {deleteArrival, getArrivals} from "../../api/arrivals";
    import ArrivalInfoModal from "../Modal/ArrivalInfoModal";
    import ConfirmationModal from "../Modal/ConfirmationModal";
    import axios from "axios";

    export default {
        components: {ConfirmationModal, ArrivalInfoModal},
        data: () => ({
            overlay: true,
            loading: false,
            arrivals: [],
            current_arrival: {},
            arrivalModal: false,
            confirmationModal: false,
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
            async onSubmit() {
                this.arrivals = this.arrivals.filter(a => a.id !== this.current_arrival.id);
                this.arrivalModal = false;
                this.current_arrival = {}
            },
            async deleteArrival() {
                await deleteArrival(this.current_arrival.id);
                this.arrivals = this.arrivals.filter(a => a.id !== this.current_arrival.id);
                this.confirmationModal = false;
                this.current_arrival = {}
            },
            async printWaybill(id) {
                this.loading = true;
                const { data } = await axios.get(`/api/excel/transfer/waybill?arrival=${id}`)
                const link = document.createElement('a');
                link.href = data.path;
                link.click();
                this.loading = false;
            }
        },
        computed: {},
        async mounted() {
            const { data } = await getArrivals(false);
            this.arrivals = data;
            this.overlay = false;
        }
    }
</script>

<style scoped>

</style>
