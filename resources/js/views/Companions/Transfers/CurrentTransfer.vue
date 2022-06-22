<template>
    <div>
        <v-text-field
            label="Поиск по перемещениям"
            v-model="search"
            clearable
            append-icon="search"
        />
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
            :search="search"
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
                {{ item.total_cost | priceFilters }}
            </template>
            <template v-slot:item.total_purchase_cost="{item}">
                {{ item.total_purchase_cost | priceFilters }}
            </template>
            <template v-slot:item.is_consignment="{item}">
                <v-icon v-if="item.is_consignment" color="success">
                    mdi-check
                </v-icon>
                <v-icon v-else color="error">
                    mdi-close
                </v-icon>
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
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import TransferPhotoModal from "@/components/Modal/TransferPhotoModal";
    import TransferModal from "@/components/Modal/TransferModal";
    import {declineTransfer} from "@/api/transfers";
    import axios from "axios";

    export default {
        async mounted() {
            await this.$store.dispatch('getTransfers', {mode: 'current', partners: true});
            this.loading = false;
        },
        components: {ConfirmationModal, TransferModal, TransferPhotoModal},
        data: () => ({
            search: '',
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
                    text: 'Под консигнацию',
                    value: 'is_consignment',
                    sortable: false
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
            async cancelTransfer() {
                this.loading = true;
                this.cancelModal = false;
                await declineTransfer(this.transferId);
                this.transferId = null;
                await this.$store.dispatch('getTransfers', {mode: 'current', partners: true});
                this.loading = false;
            },
            async onConfirm() {
                this.infoModal = false;
                this.loading = true;
                await this.$store.dispatch('getTransfers', {mode: 'current', partners: true});
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
