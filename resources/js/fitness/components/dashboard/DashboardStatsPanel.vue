<template>
    <v-row dense>
        <v-col cols="12" lg="2" md="2" sm="12">
            <h1>
                Клуб "{{ $store.getters.USER.gym.name }}"
            </h1>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-btn block color="error" depressed>
                Отчеты
            </v-btn>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-btn block color="error" depressed>
                Статистика
            </v-btn>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-btn block color="error" depressed>
                Учет посещения
            </v-btn>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-btn tag="v-btn" to="/clients" block color="error" depressed>
                Клиенты
            </v-btn>
        </v-col>
    </v-row>
</template>

<script>
export default {
    data: () => ({}),
    methods: {},
    computed: {},
};
</script>

<style scoped>

</style>
