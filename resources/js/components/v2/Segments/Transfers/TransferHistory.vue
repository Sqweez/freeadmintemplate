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
        <v-text-field
            label="Поиск по перемещениям"
            v-model="search"
            clearable
            append-icon="search"
            @input="onSearchChange"
        />
        <v-row>
            <v-col>
                <v-select
                    v-if="IS_SUPERUSER"
                    :items="stores"
                    item-text="name"
                    item-value="id"
                    v-model="parentCity"
                    label="Отправитель:"
                    @change="setFilter('parent_store_id', parentCity)"
                />
                <v-select
                    v-if="IS_SUPERUSER"
                    :items="stores"
                    item-text="name"
                    item-value="id"
                    v-model="childCity"
                    label="Получатель:"
                    @change="setFilter('child_store_id', childCity)"
                />
            </v-col>
            <v-col>
                <label>Дата создания</label>
                <v-menu
                    ref="startMenu"
                    v-model="startMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="start"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="start"
                            label="Дата начала"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="start"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="startMenu = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate(startMenu, start)"
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
                <v-menu
                    ref="finishMenu"
                    v-model="finishMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="finish"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="finish"
                            label="Дата окончания"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="finish"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="finishMenu = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate(finishMenu, finish) "
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
            </v-col>
        </v-row>
        <v-data-table
            :hide-default-footer="loading"
            :server-items-length="meta.total"
            v-if="!loading"
            :items-per-page="10"
            class="background-iron-grey fz-18 mt-2"
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :headers="headers"
            :items="transfers"
            @pagination="onPaginate"
            :footer-props="{
                'showFirstLastPage': true,
                            'items-per-page-options': [10, 15],
                        }"
        >
            <template v-slot:item.child_store="{item}">
                <span v-if="!editMode">
                    {{ item.child_store }}
                </span>
                <v-select
                    v-else
                    :items="stores"
                    item-text="name"
                    item-value="id"
                    label="Получатель"
                    v-model="storeId"
                />
            </template>
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
                <v-flex v-if="!editMode">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                color="primary"
                                @click="transferId = item.id; infoModal = true"
                                title="Подробная информация"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-information-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>Подробная информация</span>
                    </v-tooltip>
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn v-bind="attrs" v-on="on" icon color="success" @click="printWaybill(item.id)">
                                <v-icon>mdi-file-excel</v-icon>
                            </v-btn>
                        </template>
                        <span>Печать накладной</span>
                    </v-tooltip>
                    <v-btn icon color="primary" @click="showPhotoModal(item.photos)">
                        <v-icon>mdi-camera</v-icon>
                    </v-btn>
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn v-bind="attrs" v-on="on" color="orange darken-2" icon
                                   @click="$router.push(`/matrixes/create?transfer=${item.id}`)">
                                <v-icon>
                                    mdi-collage
                                </v-icon>
                            </v-btn>
                        </template>
                        <span>Сформировать матрицу на основании текущего перемещения</span>
                    </v-tooltip>
                </v-flex>
                <v-flex v-else>
                    <v-btn icon color="danger" @click="transferId = null; editMode = false;">
                        <v-icon>mdi-cancel</v-icon>
                    </v-btn>
                    <v-btn icon color="success" @click="editTransfer">
                        <v-icon>mdi-check</v-icon>
                    </v-btn>
                </v-flex>
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
            :search="search"
        />
        <TransferPhotoModal
            :state="photoModal"
            :photos="currentPhotos"
            @cancel="photoModal = false; currentPhotos = []"
        />
    </div>
</template>

<script>
import ConfirmationModal from '@/components/Modal/ConfirmationModal';
import TransferPhotoModal from '@/components/Modal/TransferPhotoModal';
import TransferModal from '@/components/Modal/TransferModal';
import { declineTransfer } from '@/api/transfers';
import axios from 'axios';
import { __hardcoded } from '@/utils/helpers';
import TransferRepository from '@/repositories/TransferRepository';
import _ from 'lodash';

export default {
    async mounted() {
        this.filterMapQuery.set('mode', __hardcoded('history'));
        this.filterMapQuery.set('per_page', __hardcoded(5));
        await this.getTransfers();
        this.loading = false;
    },
    components: {ConfirmationModal, TransferModal, TransferPhotoModal},
    data: () => ({
        initialLoad: true,
        filterMapQuery: new Map(),
        transferRepository: TransferRepository,
        parentCity: -1,
        childCity: -1,
        startMenu: null,
        start: null,
        finishMenu: null,
        finish: null,
        search: '',
        editMode: false,
        loading: true,
        cancelModal: false,
        infoModal: false,
        transferId: null,
        photoModal: false,
        currentPhotos: [],
        storeId: null,
        meta: [],
        transfers: [],
    }),
    methods: {
        onSearchChange: _.debounce(function(value) {
            if (value) {
                this.filterMapQuery.set('search', value)
            } else {
                this.filterMapQuery.delete('search')
            }
            this.getTransfers();
        }, 500),
        setFilter (key, value) {
            if ([null, undefined, -1].includes(value)) {
                this.filterMapQuery.delete(key);
            } else {
                this.filterMapQuery.set(key, value);
            }
            this.getTransfers();
        },
        onPaginate (pagination) {
            if (this.initialLoad) {
                return ;
            }
            this.filterMapQuery.set('page', pagination.page);
            this.filterMapQuery.set('per_page', pagination.itemsPerPage);
            this.getTransfers();
        },
        async getTransfers() {
            this.initialLoad = true;
            this.$loading.enable();
            this.transfers = [];
            const { data } = await this.transferRepository.get(Object.fromEntries(this.filterMapQuery));
            this.transfers = data.data;
            this.meta = data.meta;
            this.$loading.disable();
            this.$nextTick(() => {
                this.initialLoad = false;
            })
        },
        async changeCustomDate() {
            this.$refs.startMenu.save(this.start);
            this.$refs.finishMenu.save(this.finish);
            if (this.start) {
                this.filterMapQuery.set('created_at_min', this.start)
            } else {
                this.filterMapQuery.delete('created_at_min');
            }
            if (this.finish) {
                this.filterMapQuery.set('created_at_max', this.finish)
            } else {
                this.filterMapQuery.delete('created_at_max')
            }
            await this.getTransfers();
        },
        async cancelTransfer() {
            this.loading = true;
            this.cancelModal = false;
            await declineTransfer(this.transferId);
            this.transferId = null;
            await this.getTransfers();
            this.loading = false;
        },
        async onConfirm() {
            this.transferId = null;
            this.infoModal = false;
            this.loading = true;
            await this.getTransfers();
            this.loading = false;
        },
        async printWaybill(id) {
            this.loading = true;
            const {data} = await axios.get(`/api/excel/transfer/waybill?transfer=${id}`)
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
        },
        toggleEditTransfer(item) {
            this.transferId = item.id;
            this.storeId = item.child_store_id;
            this.editMode = !this.editMode;
        },
        async editTransfer() {
            try {
                await this.$store.dispatch('editTransfer', {
                    id: this.transferId,
                    child_store_id: this.storeId
                });
                this.editMode = false;
                this.transferId = null;
                await this.getTransfers();
                this.$toast.success('Перемещение отредактировано!')
            } catch (e) {
                this.$toast.error('Произошла ошибка')
            }
        }
    },
    computed: {
        headers() {
            let defaultHeaders = [
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
                },
                {
                    text: 'Поиск',
                    value: 'search',
                    align: ' d-none'
                }
            ];

            let selfCost = {
                text: 'Общая закуп. стоимость',
                value: 'total_purchase_cost',
                sortable: false,
            };

            if (this.IS_BOSS) {
                defaultHeaders.splice(__hardcoded(2), 0, selfCost);
            }

            return defaultHeaders;
        },
        isSeller() {
            return this.$store.getters.IS_SELLER;
        },
        user() {
            return this.$store.getters.USER;
        },
        stores() {
            return [
                {
                    id: -1,
                    name: 'Все'
                },
                ...this.$store.getters.stores
            ];
        }
    }
}
</script>

<style scoped>

</style>
