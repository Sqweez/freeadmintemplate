<template>
    <div>
        <div
            class="text-center d-flex align-center justify-center"
            style="min-height: 651px"
            v-if="loading">
            <v-progress-circular
                indeterminate
                size="65"
                color="primary"
            ></v-progress-circular>
        </div>
        <v-data-table
            v-if="!loading"
            class="background-iron-grey fz-18 mt-2"
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :headers="headers"
            :items="transfers"
            :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
        >
            <template v-slot:item.total_cost="{item}">
                {{ item.total_cost | priceFilters }}
            </template>
            <template v-slot:item.total_purchase_cost="{item}">
                {{ item.total_purchase_cost | priceFilters }}
            </template>
            <template v-slot:item.product_count="{item}">
                {{ item.product_count }} шт.
            </template>
            <template v-slot:item.position_count="{item}">
                {{ item.position_count }} шт.
            </template>
            <template v-slot:item.actions="{item}">
                <v-btn icon color="primary" @click="transferId = item.id; infoModal = true">
                    <v-icon>mdi-information-outline</v-icon>
                </v-btn>
                <v-btn icon color="error" @click="transferId = item.id; cancelModal = true">
                    <v-icon>mdi-cancel</v-icon>
                </v-btn>
                <v-btn icon color="success" @click="printWaybill(item.id)">
                    <v-icon>mdi-file-excel</v-icon>
                </v-btn>
                <v-btn icon color="primary" @click="showPhotoModal(item.photos)">
                    <v-icon>mdi-camera</v-icon>
                </v-btn>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <ConfirmationModal
            :on-confirm="cancelTransfer"
            v-on:cancel="transferId = null; cancelModal = false;"
            message="Вы действительно хотите отменить выбранное перемещение?"
            :state="cancelModal"
        />
        <TransferModal
            :state="infoModal"
            :id="transferId"
            :confirm-mode="true"
            v-on:cancel="transferId = null; infoModal = false"
            v-on:confirmed="onConfirm"
        />
        <TransferPhotoModal
            :state="photoModal"
            :photos="currentPhotos"
            @cancel="photoModal = false; currentPhotos = []"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "../../Modal/ConfirmationModal";
    import TransferPhotoModal from "../../Modal/TransferPhotoModal";
    import TransferModal from "../../Modal/TransferModal";
    import {declineTransfer} from "@/api/transfers";
    import axios from "axios";
    import showToast from "../../../utils/toast";
    import {TOAST_TYPE} from "@/config/consts";
    export default {
        async mounted() {
            await this.$store.dispatch('getTransfers', {mode: 'current'});
            this.loading = false;
        },
        components: {ConfirmationModal, TransferModal, TransferPhotoModal},
        data: () => ({
            loading: true,
            cancelModal: false,
            infoModal: false,
            transferId: null,
            photoModal: false,
            currentPhotos: [],
            headers: [
                {
                    text: 'Количество позиций',
                    value: 'position_count',
                    sortable: false,
                },
                {
                    text: 'Количество товаров',
                    value: 'product_count',
                    sortable: false,
                },
                {
                    text: 'Общая закуп. стоимость',
                    value: 'total_purchase_cost',
                    sortable: false,
                },
                {
                    text: 'Общая стоимость',
                    value: 'total_cost',
                    sortable: false,
                },
                {
                    text: 'Пользователь',
                    value: 'user',
                    sortable: false
                },
                {
                    text: 'Дата создания',
                    value: 'date',
                    sortable: false
                },
                {
                    text: 'Отправитель',
                    value: 'parent_store',
                    sortable: false
                },
                {
                    text: 'Получатель',
                    value: 'child_store',
                    sortable: false
                },
                {
                    text: 'Действие',
                    value: 'actions',
                    sortable: false
                }
            ],
        }),
        methods: {
            async cancelTransfer() {
                this.loading = true;
                this.cancelModal = false;
                await declineTransfer(this.transferId);
                this.transferId = null;
                await this.$store.dispatch('getTransfers', {mode: 'current'});
                this.loading = false;
            },
            async onConfirm() {
                this.infoModal = false;
                this.loading = true;
                await this.$store.dispatch('getTransfers', {mode: 'current'});
                this.loading = false;
            },
            async printWaybill(id) {
                this.loading = true;
                const { data } = await axios.get(`/api/excel/transfer/waybill?transfer=${id}`)
                const link = document.createElement('a');
                link.href = data.path;
                link.click();
                this.loading = false;
            },
            showPhotoModal(photos) {
                if (!photos || !photos.length) {
                    showToast('Нет фотографий', TOAST_TYPE.ERROR);
                    return false;
                }
                this.currentPhotos = photos;
                this.photoModal = true;
            }
        },
        computed: {
            transfers() {
                return this.$store.getters.transfers.filter(s => {
                    if (this.isSeller) {
                        return +s.child_store_id === +this.user.store_id || +s.parent_store_id === +this.user.store_id;
                    }
                    return s;
                });
            },
            isSeller() {
                return this.$store.getters.IS_SELLER;
            },
            user() {
                return this.$store.getters.USER;
            }
        }
    }
</script>

<style scoped>

</style>
