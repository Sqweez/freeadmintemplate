<template>
    <v-card>
        <v-card-title>Список клиентов</v-card-title>
        <v-card-text>
            <v-container>
                <div class="d-flex justify-space-between align-center">
                    <v-btn color="error" @click="clientModal = true" v-if="!IS_MARKETOLOG">Добавить клиента <v-icon>mdi-plus</v-icon></v-btn>
                    <v-select
                        style="max-width: 270px;"
                        label="Тип лояльности"
                        :items="loyalties"
                        item-value="id"
                        item-text="name"
                        v-model="loyaltyFilter"
                    />
                    <v-select
                        style="max-width: 270px;"
                        label="Тип клиента"
                        :items="clientTypes"
                        item-value="id"
                        item-text="name"
                        v-model="clientTypeFilter"
                    />
                    <v-select
                        style="max-width: 270px;"
                        label="Города"
                        :items="cities"
                        item-value="id"
                        item-text="name"
                        v-model="cityFilter"
                    />
                    <v-select
                        style="max-width: 270px;"
                        label="Пол"
                        :items="genders"
                        item-value="id"
                        item-text="value"
                        v-model="genderId"
                    />
                    <v-checkbox
                        label="Без карт"
                        v-model="withoutBarcode"
                    />
                </div>
                <v-btn color="success" @click="exportModal = true;" v-if="!IS_MARKETOLOG">
                    Экспорт клиентов <v-icon>mdi-file-excel-box</v-icon>
                </v-btn>
                <v-row>
                    <v-col>
                        <v-text-field
                            class="mt-2"
                            v-model="search"
                            solo
                            clearable
                            label="Поиск клиента"
                            single-line
                            hide-details
                        ></v-text-field>
                        <v-data-table
                            :loading="clients.length === 0"
                            loading-text="Идет загрузка клиентов"
                            :search="search"
                            no-results-text="Нет результатов"
                            no-data-text="Нет данных"
                            :headers="headers"
                            :page.sync="pagination.page"
                            :items="clients"
                            @page-count="pageCount = $event"
                            :items-per-page="10"
                            :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                            <template v-slot:item.client_balance="{item}">
                                {{ item.client_balance }} ₸
                            </template>
                            <template v-slot:item.client_discount="{item}">
                                {{ item.client_discount }}%
                            </template>
                            <template v-slot:item.is_partner="{item}">
                                <v-icon :color="item.is_partner ? 'success' : 'error'">
                                    {{ item.is_partner ? 'mdi-check' : 'mdi-close' }}
                                </v-icon>
                            </template>
                            <template v-slot:item.extra="{item}">
                                <v-list>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.gender_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Пол
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.city }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Город
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-icon :color="item.is_partner ? 'success' : 'error'">
                                                    {{ item.is_partner ? 'mdi-check' : 'mdi-close' }}
                                                </v-icon>
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Партнер
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                               {{ item.birth_date_formatted }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Дата рождения
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.loyalty.name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Тип лояльности
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.total_sum | priceFilters }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Сумма покупок за все время
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.current_month_sum | priceFilters }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Сумма покупок за текущий месяц
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item v-if="item.loyalty_id == 2">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.until_platinum | priceFilters }} | {{ item.until_platinum_percent }}%
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Платиновый остаток
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </template>
                            <template v-slot:item.actions="{ item }" v-if="IS_MARKETOLOG">
                                <v-list>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="$router.push(`/clients/${item.id}`)">
                                                    <v-icon>
                                                        mdi-eye
                                                    </v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="sendWhatsapp(item)" color="success">
                                                    <v-icon>mdi-whatsapp</v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </template>
                            <template v-slot:item.actions="{ item }" v-else>
                                <v-list>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="userId = item.id; clientModal = true;">
                                                    <v-icon>mdi-pencil</v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="confirmationModal = true; userId = item.id;">
                                                    <v-icon>mdi-delete</v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="balanceModal = true; userId = item.id;">
                                                    <v-icon>mdi-cash</v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="$router.push(`/clients/${item.id}`)">
                                                    <v-icon>
                                                        mdi-eye
                                                    </v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-btn icon @click="sendWhatsapp(item)" color="success">
                                                    <v-icon>mdi-whatsapp</v-icon>
                                                </v-btn>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </template>
                            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                            </template>
                        </v-data-table>
                        <div class="text-xs-center pt-2">
                            <v-pagination
                                v-model="pagination.page"
                                :total-visible="10"
                                :length="pageCount"></v-pagination>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <ClientModal
            :state="clientModal"
            :id="userId"
            v-on:cancel="userId = null; clientModal = false;"
            v-on:clientCreated="userId = null; clientModal = false;"
        />
        <ConfirmationModal
            :state="confirmationModal"
            :on-confirm="deleteUser"
            v-on:cancel="userId = null; confirmationModal = false"
            message="Вы действительно хотите удалить выбранного клиента?" />
        <BalanceModal
            :state="balanceModal"
            @cancel="userId = null; balanceModal = false"
            @submit="addBalance"
        />
        <ExportClientsModal
            @cancel="exportModal = false;"
            :state="exportModal"
        />
    </v-card>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import UserModal from "@/components/Modal/UserModal";
    import ACTIONS from "@/store/actions";
    import ClientModal from "@/components/Modal/ClientModal";
    import BalanceModal from "@/components/Modal/BalanceModal";
    import ExportClientsModal from "@/components/Modal/Export/ExportClientsModal";
    import GENDERS from "@/common/enums/genders";

    export default {
        components: {
            ExportClientsModal,
            BalanceModal,
            ClientModal,
            ConfirmationModal,
            UserModal
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
        },
        data: () => ({
            withoutBarcode: false,
            exportModal: false,
            confirmationModal: false,
            clientModal: false,
            userId: null,
            balanceModal: false,
            search: '',
            pagination: {
                ascending: true,
                rowsPerPage: 10,
                page: 1
            },
            clientTypes: [
                {
                    id: -1,
                    name: 'Все'
                },
                {
                    id: 1,
                    name: 'Клиент'
                },
                {
                    id: 2,
                    name: 'Тренер'
                }
            ],
            genderId: -1,
            genders: [
                {
                    id: -1,
                    value: 'Все'
                },
                ...GENDERS
            ],
            clientTypeFilter: -1,
            loyaltyFilter: -1,
            pageCount: 1,
            cityFilter: 0,
            headers: [
                {
                    value: 'client_name',
                    text: 'ФИО',
                    sortable: false
                },
                {
                    value: 'client_phone',
                    text: 'Телефон',
                    sortable: false,
                },
                {
                    value: 'client_balance',
                    text: 'Баланс'
                },
                {
                    value: 'client_card',
                    text: 'Номер карты'
                },
                {
                    value: 'client_discount',
                    text: 'Процент скидки'
                },
                {
                    value: 'extra',
                    text: 'Доп. информация'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ]
        }),
        computed: {
            loyalties() {
                return [
                    {
                        id: -1,
                        name: 'Все'
                    },
                    ...this.$store.getters.LOYALTY
                ];
            },
            clients() {
                return this.$store.getters.clients
                    .filter(client => {
                        if (this.cityFilter === 0) {
                            return client;
                        }
                        return +client.client_city === this.cityFilter
                    }).filter(client => {
                        if (this.loyaltyFilter === -1) {
                            return client;
                        }
                        return client.loyalty.id === this.loyaltyFilter;
                    }).filter(client => {
                        if (this.clientTypeFilter === -1) {
                            return client;
                        }
                        if (this.clientTypeFilter === 1) {
                            return !client.is_partner;
                        }
                        return client.is_partner;
                    }).filter(c => {
                        if (this.genderId === -1) {
                            return true;
                        } else {
                            return c.gender === this.genderId;
                        }
                    }).filter(c => {
                        if (!this.withoutBarcode) {
                            return true;
                        }
                        return c.client_card.length === 0 || c.client_card.length < 5;
                    })
            },
            shops() {
                return this.$store.getters.shops;
            },
            cities() {
                return [
                    {id: 0, name: 'Все города'},
                    {id: -1, name: 'Город не указан'},
                    ...this.$store.getters.cities
                ];
            },
        },
        methods: {
            async deleteUser() {
                await this.$store.dispatch(ACTIONS.DELETE_CLIENT, this.userId);
                this.$toast.success('Клиент удален');
                this.userId = null;
                this.confirmationModal = false;
            },
            async addBalance(e) {
                await this.$store.dispatch(ACTIONS.ADD_BALANCE, {
                    client_id: this.userId,
                    sum: e,
                });
                this.balanceModal = false;
                this.userId = null;
                this.$toast.success('Баланс успешно пополнен!');
            },
            sendWhatsapp(client) {
                const message = '';
                window.location.href = `https://api.whatsapp.com/send?phone=${client.client_phone}&text=${message}`;
            },
        }
    }
</script>

<style scoped>
    th {
        font-size: 16px;
    }
</style>
