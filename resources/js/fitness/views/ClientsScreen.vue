<template>
    <div>
        <FItClientModal
            :state="showClientModal"
            :id="clientId"
            @cancel="showClientModal = false; clientId = null;"
        />
        <i-card-page title="Список клиентов" color="tomato">
            <v-data-table
                :headers="headers"
                :items="clients"
            >
                <template v-slot:item.balance="{ item }">
                    {{ item.balance | priceFilters }}
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-btn icon @click="clientId = item.id; showClientModal = true;">
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn icon>
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </i-card-page>
    </div>
</template>

<script>

import FItClientModal from '@/fitness/components/modals/FItClientModal.vue';

export default {
    components: {FItClientModal},
    data: () => ({
        showClientModal: false,
        clientId: null,
        headers: [
            {
                text: '#',
                value: 'id'
            },
            {
                text: 'Имя',
                value: 'name'
            },
            {
                text: 'Карта',
                value: 'pass'
            },
            {
                text: 'Телефон',
                value: 'phone'
            },
            {
                text: 'Баланс',
                value: 'balance'
            },
            {
                text: 'Дата регистрации',
                value: 'date'
            },
            {
                text: 'Действие',
                value: 'actions'
            }
        ],
    }),
    async mounted() {
        this.$loading.enable();
        try {
            await Promise.all([
                this._getClients(),
            ])
        } catch (e) {

        } finally {
            this.$loading.disable();
        }
    },
    methods: {
        async _getClients () {
            await this.$store.dispatch('getClients')
        },
    },
    computed: {
        clients () {
            return this.$store.getters.clients;
        }
    },
};
</script>

<style scoped>

</style>
