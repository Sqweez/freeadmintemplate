<template>
    <v-card>
        <v-card-title>Список клиентов</v-card-title>
        <v-card-text>
            <v-container>
                <v-btn color="error" @click="clientModal = true">Добавить клиента <v-icon>mdi-plus</v-icon></v-btn>
                <v-row>
                    <v-col>
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th>ФИО</th>
                                    <th>Телефон</th>
                                    <th>Баланс</th>
                                    <th>Номер карты</th>
                                    <th>Процент скидки</th>
                                    <th>Действие</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user, idx) of clients" :key="idx">
                                    <td>{{ user.client_name }}</td>
                                    <td>{{ user.client_phone }}</td>
                                    <td>{{ user.client_balance }} ₸</td>
                                    <td>{{ user.client_card }} </td>
                                    <td>{{ user.client_discount }}%</td>
                                    <td>
                                        <v-btn icon @click="userId = user.id; clientModal = true;">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                        <v-btn icon @click="confirmationModal = true; userId = user.id;">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
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
    </v-card>
</template>

<script>
    import ConfirmationModal from "../../components/Modal/ConfirmationModal";
    import UserModal from "../../components/Modal/UserModal";
    import ACTIONS from "../../store/actions";
    import ClientModal from "../../components/Modal/ClientModal";
    import showToast from "../../utils/toast";
    export default {
        components: {
            ClientModal,
            ConfirmationModal,
            UserModal
        },
        data: () => ({
            confirmationModal: false,
            clientModal: false,
            userId: null,
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
        }
    }
</script>

<style scoped>
    th {
        font-size: 16px;
    }
</style>
