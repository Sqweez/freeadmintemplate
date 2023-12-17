<template>
    <div>
        <i-card-page title="Список клиентов">
            <v-data-table
                :headers="headers"
                :items="clients"
            ></v-data-table>
        </i-card-page>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    data: () => ({
        headers: [
            {
                text: '#',
                value: 'id'
            },
            {
                text: 'Имя',
                value: 'name'
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
        ...mapGetters([
            'clients'
        ])
    },
};
</script>

<style scoped>

</style>
