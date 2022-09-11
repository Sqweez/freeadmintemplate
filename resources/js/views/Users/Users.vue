<template>
    <v-card>
        <v-card-title>Список продавцов</v-card-title>
        <v-card-text>
            <v-container>
                <v-btn color="error" @click="userModal = true">Добавить продавца <v-icon>mdi-plus</v-icon></v-btn>
                <v-row>
                    <v-col>
                        <v-text-field
                            class="mt-2"
                            v-model="search"
                            solo
                            clearable
                            label="Поиск пользователя"
                            single-line
                            hide-details
                        />
                        <v-data-table
                            :items="users"
                            :search="search"
                            :headers="headers"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-btn icon @click="userId = item.id; userModal = true;">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn icon @click="confirmationModal = true; userId = item.id;">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <UserModal
            :state="userModal"
            :id="userId"
            v-on:cancel="userId = null; userModal = false;"
            v-on:onSubmit="userId = null; userModal = false;"
        />
        <ConfirmationModal
            :state="confirmationModal"
            :on-confirm="deleteUser"
            v-on:cancel="userId = null; confirmationModal = false"
            message="Вы действительно хотите удалить выбранного пользователя?" />
    </v-card>
</template>

<script>
import ConfirmationModal from "@/components/Modal/ConfirmationModal";
import UserModal from "@/components/Modal/UserModal";
import ACTIONS from "@/store/actions";

export default {
    components: {
        ConfirmationModal,
        UserModal
    },
    data: () => ({
        confirmationModal: false,
        userModal: false,
        userId: null,
        search: '',
        headers: [
            {
                value: 'name',
                text: 'Имя'
            },
            {
                value: 'login',
                text: 'Логин'
            },
            {
                value: 'role',
                text: 'Роль'
            },
            {
                value: 'city',
                name: 'Город'
            },
            {
                value: 'actions',
                text: 'Действие'
            }
        ],
    }),
    computed: {
        users() {
            return this.$store.getters.users;
        }
    },
    methods: {
        async deleteUser() {
            await this.$store.dispatch(ACTIONS.DELETE_USER, this.userId);
            this.$toast.success('Пользователь удален');
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
