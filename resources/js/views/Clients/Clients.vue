<template>
    <v-card>
        <v-card-title>Список клиентов</v-card-title>
        <v-card-text>
            <v-container>
                <v-btn color="error" @click="clientModal = true">Добавить клиента <v-icon>mdi-plus</v-icon></v-btn>
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
                            <template v-slot:item.actions="{ item }">
                                <v-btn icon @click="userId = item.id; clientModal = true;">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn icon @click="confirmationModal = true; userId = item.id;">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                                <v-btn icon @click="balanceModal = true; userId = item.id;">
                                    <v-icon>mdi-cash</v-icon>
                                </v-btn>
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
    </v-card>
</template>

<script>
    import ConfirmationModal from "../../components/Modal/ConfirmationModal";
    import UserModal from "../../components/Modal/UserModal";
    import ACTIONS from "../../store/actions";
    import ClientModal from "../../components/Modal/ClientModal";
    import showToast from "../../utils/toast";
    import BalanceModal from "../../components/Modal/BalanceModal";
    export default {
        components: {
            BalanceModal,
            ClientModal,
            ConfirmationModal,
            UserModal
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
        },
        data: () => ({
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
            pageCount: 1,
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
                    value: 'actions',
                    text: 'Действие'
                }
            ]
        }),
        computed: {
            clients() {
                return this.$store.getters.clients;
            }
        },
        methods: {
            async deleteUser() {
                await this.$store.dispatch(ACTIONS.DELETE_CLIENT, this.userId);
                showToast('Клиент удален');
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
                showToast('Баланс успешно пополнен!');
            }
        }
    }
</script>

<style scoped>
    th {
        font-size: 16px;
    }
</style>
