<template>
    <div>
        <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
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
        <v-row>
            <v-col>
                <v-select
                    v-if="IS_SUPERUSER"
                    :items="stores"
                    item-text="name"
                    item-value="id"
                    v-model="parentCity"
                    label="Отправитель:"
                />
                <v-select
                    v-if="IS_SUPERUSER"
                    :items="stores"
                    item-text="name"
                    item-value="id"
                    v-model="childCity"
                    label="Получатель:"
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
                            @click="changeCustomDate()"
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
                            @click="changeCustomDate()"
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
            </v-col>
            <v-col>
                <label>Дата принятия</label>
                <v-menu
                    ref="startMenuSecondary"
                    v-model="startMenuSecondary"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="startSecondary"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="startSecondary"
                            label="Дата начала"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="startSecondary"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="startMenuSecondary = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate()"
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
                <v-menu
                    ref="finishMenuSecondary"
                    v-model="finishMenuSecondary"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="finishSecondary"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="finishSecondary"
                            label="Дата окончания"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="finishSecondary"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="finishMenuSecondary = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate()"
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
            </v-col>
        </v-row>
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
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import TransferModal from "@/components/Modal/TransferModal";
    import axios from 'axios';
    import TransferPhotoModal from "@/components/Modal/TransferPhotoModal";
    import moment from 'moment';
    export default {
        async mounted() {
            await this.$store.dispatch('getTransfers', {mode: 'history'});
            this.loading = false;
        },
        components: {TransferPhotoModal, ConfirmationModal, TransferModal},
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
                },
                {
                    text: 'Поиск',
                    value: 'search',
                    align: ' d-none'
                }
            ],
            parentCity: -1,
            childCity: -1,
            start: null,
            startMenu: null,
            finish: null,
            finishMenu: null,
            startSecondary: null,
            startMenuSecondary: null,
            finishSecondary: null,
            finishMenuSecondary: null,
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
            },
            changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
                this.$refs.startMenuSecondary.save(this.startSecondary);
                this.$refs.finishMenuSecondary.save(this.finishSecondary);
                /*if (this.$refs.startMenuSecondary) {
                    this.$refs.startMenuSecondary.save(this.startSecondary);
                }
                if (this.$refs.finishMenuSecondary) {
                    this.$refs.finishMenuSecondary.save(this.finishSecondary);
                }*/
            },
        },
        computed: {
            transfers() {
                return this.$store.getters.transfers.filter(s => {
                    if (this.isSeller || this.IS_FRANCHISE) {
                        return +s.child_store_id === +this.user.store_id || +s.parent_store_id === +this.user.store_id;
                    }
                    return s;
                }).filter(t => {
                    if (this.childCity === -1) {
                        return t;
                    }
                    return t.child_store_id === this.childCity;
                }).filter(t => {
                    if (this.parentCity === -1) {
                        return t;
                    }
                    return t.parent_store_id === this.parentCity;
                }).filter(t => {
                    if (!(this.start && this.finish)) {
                        return t;
                    }
                    return moment(t.created_at)
                        .startOf('day')
                        .isSameOrBefore(this.finish) && moment(t.created_at)
                        .startOf('day')
                        .isSameOrAfter(this.start);
                }).filter(t => {
                    if (!(this.startSecondary && this.finishSecondary)) {
                        return t;
                    }
                    return moment(t.updated_at)
                        .startOf('day')
                        .isSameOrBefore(this.finishSecondary) && moment(t.updated_at)
                        .startOf('day')
                        .isSameOrAfter(this.startSecondary);
                });
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
