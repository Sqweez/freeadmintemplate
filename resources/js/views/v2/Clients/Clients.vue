<template>
    <v-card>
        <v-card-title>Список клиентов</v-card-title>
        <v-card-text>
            <v-container>
                <div class="d-flex justify-space-between align-center">
                    <v-row>
                        <v-col>
                            <v-btn color="error" @click="clientModal = true" v-if="!IS_MARKETOLOG">
                                Добавить клиента
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                            <v-btn class="mt-2" color="success" @click="exportModal = true;" v-if="!IS_MARKETOLOG">
                                Экспорт клиентов
                                <v-icon>mdi-file-excel-box</v-icon>
                            </v-btn>
                            <v-btn
                                color="success"
                                :disabled="selectedClients.length === 0"
                                class="mt-2"
                                @click="isMassBalanceEnabled = true; balanceModal = true;"
                            >
                                Массово добавить баланс
                                <v-icon>mdi-cash</v-icon>
                            </v-btn>
                        </v-col>
                        <v-col>
                            <v-select
                                style="max-width: 270px;"
                                label="Тип лояльности"
                                :items="loyalties"
                                item-value="id"
                                item-text="name"
                                @change="applyFilter('loyalty_id', $event)"
                                v-model="loyaltyFilter"
                            />
                            <v-select
                                style="max-width: 270px;"
                                label="Тип клиента"
                                :items="clientTypes"
                                item-value="id"
                                item-text="name"
                                @change="applyFilter('is_partner', $event)"
                                v-model="clientTypeFilter"
                            />
                        </v-col>
                        <v-col>
                            <v-select
                                style="max-width: 270px;"
                                label="Города"
                                :items="cities"
                                item-value="id"
                                item-text="name"
                                @change="applyFilter('client_city', $event)"
                                v-model="cityFilter"
                            />
                            <v-select
                                style="max-width: 270px;"
                                label="Пол"
                                :items="genders"
                                item-value="id"
                                item-text="value"
                                @change="applyFilter('gender', $event)"
                                v-model="genderId"
                            />
                        </v-col>
                        <v-col>
                            <v-checkbox
                                label="Без карт"
                                v-model="withoutBarcode"
                                @change="applyFilter('without_code', withoutBarcode)"
                            />
                            <v-checkbox
                                label="Оптовик"
                                v-model="isWholesaleBuyer"
                                @change="applyFilter('is_wholesale_buyer', isWholesaleBuyer)"
                            />
                            <v-checkbox
                                label="Каспи клиент"
                                v-model="isKaspiClient"
                                @change="applyFilter('is_kaspi', isKaspiClient)"
                            />
                        </v-col>
                    </v-row>
                </div>
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
                            @input="onSearchChange"
                        />
                        <v-btn depressed small color="error" @click="selectClients" class="my-2">
                            Выбрать всех текущих
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                        <v-data-table
                            v-model="selectedClients"
                            show-select
                            :loading="loading"
                            loading-text="Идет загрузка клиентов"
                            no-results-text="Нет результатов"
                            no-data-text="Нет данных"
                            :headers="headers"
                            :page.sync="meta.current_page"
                            :server-items-length="meta.total"
                            :items="clients"
                            @pagination="onPageChange"
                            @page-count="pageCount = $event"
                            :disable-pagination="loading"
                            :items-per-page="10"
                            :footer-props="{
                            'items-per-page-options': [10, 20, 50],
                        }">
                            <template v-slot:item.client_balance="{item}">
                                {{ item.client_balance | priceFilters }}
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
                                                {{ item.barter_balance_amount | priceFilters }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Бартерный баланс
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
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
                                    <v-list-item v-if="item.is_partner">
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
                                    <v-list-item v-if="item.is_kaspi">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-icon :color="item.is_kaspi ? 'success' : 'error'">
                                                    {{ item.is_kaspi ? 'mdi-check' : 'mdi-close' }}
                                                </v-icon>
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Каспи-клиент
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-list-item v-if="item.is_wholesale_buyer">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <v-icon :color="item.is_wholesale_buyer ? 'success' : 'error'">
                                                    {{ item.is_wholesale_buyer ? 'mdi-check' : 'mdi-close' }}
                                                </v-icon>
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                Оптовый покупатель
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
                                                {{ item.until_platinum | priceFilters }} |
                                                {{ item.until_platinum_percent }}%
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
                                                <v-btn
                                                    icon
                                                    @click="currentClient = {...item}; userId = item.id; clientModal = true;"
                                                >
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
                                                <v-btn title="Пополнить бартерный баланс" icon
                                                       @click="barterBalanceModal = true; userId = item.id;">
                                                    <v-icon>mdi-account-cash</v-icon>
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
                                v-if="false"
                                @input="onPageChange"
                                v-model="meta.current_page"
                                :total-visible="10"
                                :length="meta.last_page"
                            />
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <ClientModal
            :current-client="currentClient"
            :state="clientModal"
            :id="userId"
            v-on:cancel="userId = null; clientModal = false; retrieveClients()"
            v-on:clientCreated="userId = null; clientModal = false;"
        />
        <ConfirmationModal
            :state="confirmationModal"
            :on-confirm="deleteUser"
            v-on:cancel="userId = null; confirmationModal = false"
            message="Вы действительно хотите удалить выбранного клиента?" />
        <BalanceModal
            :state="balanceModal"
            @cancel="userId = null; balanceModal = false; isMassBalanceEnabled = false;"
            @submit="addBalance"
        />
        <ExportClientsModal
            @cancel="exportModal = false;"
            :state="exportModal"
        />
        <BarterBalanceModal
            :state="barterBalanceModal"
            @cancel="barterBalanceModal = false"
            @submit="_handleBarterBalanceSubmit"
        />
    </v-card>
</template>

<script>
import ConfirmationModal from '@/components/Modal/ConfirmationModal.vue';
import UserModal from '@/components/Modal/UserModal.vue';
import ACTIONS from '@/store/actions';
import ClientModal from '@/components/Modal/ClientModal.vue';
import BalanceModal from '@/components/Modal/BalanceModal.vue';
import ExportClientsModal from '@/components/Modal/Export/ExportClientsModal.vue';
import GENDERS from '@/common/enums/genders';
import axiosClient from '@/utils/axiosClient';
import BarterBalanceModal from '@/components/v2/Modal/BarterBalanceModal.vue';
import ClientRepository from '@/repositories/ClientRepository';
import _ from 'lodash';

export default {
    components: {
        BarterBalanceModal,
        ExportClientsModal,
        BalanceModal,
        ClientModal,
        ConfirmationModal,
        UserModal
    },
    async created() {
        this.queryMap.set('per_page', 10);
        this.queryMap.set('page', 1);
        await this.retrieveClients();
    },
    data: () => ({
        currentClient: {},
        clientFilters: {},
        queryMap: new Map(),
        loading: true,
        clients: [],
        links: {},
        meta: {},
        clientRepository: ClientRepository,
        isMassBalanceEnabled: false,
        selectedClients: [],
        isKaspiClient: false,
        isWholesaleBuyer: false,
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
                sortable: false
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
        ],
        barterBalanceModal: false
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
        clients2() {
            return this.$store.getters.clients.filter(client => {
                if (this.cityFilter === 0) {
                    return client;
                }
                return +client.client_city === this.cityFilter;
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
            }).filter(c => {
                if (!this.isWholesaleBuyer) {
                    return true;
                }
                return c.is_wholesale_buyer;
            }).filter(c => {
                return !this.isKaspiClient ? true : c.is_kaspi;
            });
        },
        shops() {
            return this.$store.getters.shops;
        },
        cities() {
            return [
                { id: 0, name: 'Все города' },
                { id: -1, name: 'Город не указан' },
                ...this.$store.getters.cities
            ];
        }
    },
    methods: {
        applyFilter(key, value) {
            if (value === -1) {
                this.queryMap.delete(key);
            } else {
                this.queryMap.set(key, value);
            }
            if (key === 'is_partner') {
                this.queryMap.set(key, value === 2);
            }
            if (['is_kaspi', 'is_wholesale_buyer'].includes(key) && !value) {
                this.queryMap.delete(key);
            }
            this.retrieveClients();
        },
        async retrieveClients() {
            this.loading = true;
            this.clients = [];
            window.scrollTo(0, 0);
            const { data } = await this.clientRepository.get(Object.fromEntries(this.queryMap));
            this.clients = data.data;
            this.meta = data.meta;
            this.links = data.links;
            this.loading = false;
        },
        async refreshClients() {
            await this.retrieveClients();
        },
        onPageChange(page) {
            if (this.loading) {
                return undefined;
            }
            let touched = false;
            if (page.itemsPerPage !== this.queryMap.get('per_page')) {
                this.queryMap.set('per_page', page.itemsPerPage);
                touched = true;
            }
            if (page.page !== this.queryMap.get('page')) {
                this.queryMap.set('page', page.page);
                touched = true;
            }
            if (touched) {
                this.retrieveClients();
            }
            //this.queryMap.set('page', page);
            //this.retrieveClients();
        },
        onSearchChange: _.debounce(function(value) {
            if (value && value.length > 4) {
                this.queryMap.set('search', value);
                this.retrieveClients();
            }
            if (value === '') {
                this.queryMap.delete('search');
                this.retrieveClients();
            }
        }, 500),
        selectClients() {
            this.selectedClients = [...this.clients];
        },
        async deleteUser() {
            await this.$store.dispatch(ACTIONS.DELETE_CLIENT, this.userId);
            await this.retrieveClients();
            this.$toast.success('Клиент удален');
            this.userId = null;
            this.confirmationModal = false;
        },
        async addBalance(e) {
            this.$loading.enable();
            this.balanceModal = false;
            if (!this.isMassBalanceEnabled) {
                await this.$store.dispatch(ACTIONS.ADD_BALANCE, {
                    client_id: this.userId,
                    sum: e
                });
            } else {
                await axiosClient.post(`clients/balance`, {
                    clients: this.selectedClients.map(c => c.id),
                    sum: e
                });
                this.selectedClients = [];
            }
            await this.retrieveClients();
            this.userId = null;
            this.$loading.disable();
            this.$toast.success('Баланс успешно пополнен!');
        },
        sendWhatsapp(client) {
            let phone = client.client_phone;
            phone = phone.replace(/^\+/, '');
            const link = document.createElement('a');
            link.target = '_blank';
            link.href = `https://api.whatsapp.com/send?phone=${phone}`;
            link.click();
        },
        async _handleBarterBalanceSubmit(payload) {
            try {
                this.$loading.enable();
                await this.$store.dispatch(ACTIONS.ADD_BARTER_BALANCE, { ...payload, client_id: this.userId });
                this.barterBalanceModal = false;
                this.$toast.success('Бартерный баланс успешно пополнен');
                await this.retrieveClients();
            } catch (e) {
                this.$toast.error('Произошла ошибка...');
            } finally {
                this.$loading.disable();
            }
        }
    }
};
</script>

<style scoped>
th {
    font-size: 16px;
}
</style>
