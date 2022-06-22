<template>
    <div>
        <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
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
            class="background-tooman-grey fz-18 mt-2"
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
                {{ new Intl.NumberFormat('ru-RU').format(item.total_cost) }}₸
            </template>
            <template v-slot:item.total_purchase_cost="{item}">
                {{ new Intl.NumberFormat('ru-RU').format(item.total_purchase_cost) }}₸
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
            v-on:cancel="transferId = null; infoModal = false"
        />
        <TransferPhotoModal
            :state="photoModal"
            :photos="currentPhotos"
            @cancel="photoModal = false; currentPhotos = []"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import TransferModal from "@/components/Modal/TransferModal";
    import axios from 'axios';
    import TransferPhotoModal from "@/components/Modal/TransferPhotoModal";

    export default {
        async mounted() {
            await this.$store.dispatch('getTransfers', {mode: 'history', partners: true});
            this.loading = false;
        },
        components: {TransferPhotoModal, ConfirmationModal, TransferModal},
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
                    text: 'Дата',
                    value: 'date',
                    sortable: false
                },
                {
                    text: 'Дата принятия',
                    value: 'date_updated',
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
            cancelTransfer() {
                this.transferId = null;
                this.cancelModal = false;
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
                    this.$toast.error('Нет фотографий');
                    return false;
                }
                this.currentPhotos = photos;
                this.photoModal = true;
            }
        },
        computed: {
            transfers() {
                return this.$store.getters.transfers
                    .filter(t => {
                        return this.stores.map(s => s.id).includes(t.child_store_id);
                    });
            },
            stores() {
                return this.$store.getters.partner_stores;
            }
        }
    }
</script>

<style scoped>

</style>
