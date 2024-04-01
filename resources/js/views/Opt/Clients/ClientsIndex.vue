<template>
    <div>
        <i-card-page title="Оптовые клиенты">
            <v-simple-table>
                <template #default>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Почта</th>
                        <th>Город</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="client of clients" :key="client.id">
                        <td>{{ client.id }}</td>
                        <td>{{ client.name }}</td>
                        <td>{{ client.phone }}</td>
                        <td>{{ client.email }}</td>
                        <td>{{ client.city.name }}</td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </i-card-page>
    </div>
</template>

<script>
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        clients: [],
    }),
    methods: {},
    computed: {},
    async mounted() {
        this.$loading.enable();
        const { data } = await axiosClient.get('/opt/admin/v1/client');
        this.clients = data.clients;
        this.$loading.disable();
    }
};
</script>

<style scoped>

</style>
