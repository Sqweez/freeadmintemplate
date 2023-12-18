<template>
    <v-row>
        <FitUserModal
            @cancel="showUserModal = false; userId = null;"
            :state="showUserModal"
            :id="userId"
        />
        <v-col cols="12">
            <v-card>
                <v-card-title style="background-color: #F9690E; color: #fff;">
                    Список сотрудников
                </v-card-title>
                <v-card-text v-if="!isReady">
                    <inline-loader />
                </v-card-text>
                <v-card-text v-else>
                    <v-btn color="success" depressed class="my-4" @click="showUserModal = true;">
                        Добавить сотрудника
                    </v-btn>
                    <v-data-table
                        :headers="headers"
                        :items="users"
                    >
                        <template v-slot:item.actions="{ item }">
                            <v-btn icon @click="userId = item.id; showUserModal = true;">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon>
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import InlineLoader from '@/components/Loaders/InlineLoader.vue';
import FitUserModal from '@/fitness/components/modals/FitUserModal.vue';

export default {
    components: {FitUserModal, InlineLoader},
    data: () => ({
        isReady: true,
        showUserModal: false,
        userId: null,
        headers: [
            {
                text: '#',
                value: 'id',
            },
            {
                text: 'Имя',
                value: 'name',
            },
            {
                text: 'Логин',
                value: 'login'
            },
            {
                text: 'Должность',
                value: 'role.name'
            },
            {
                text: 'Действие',
                value: 'actions'
            }
        ],
    }),
    methods: {
        async _getUsers () {
            await this.$store.dispatch('getUsers');
        }
    },
    async mounted() {

    },
    computed: {
        users () {
            return this.$store.getters.users;
        }
    },
};
</script>

<style scoped>

</style>
