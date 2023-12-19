<template>
    <div>
        <v-card>
            <v-card-title style="background-color: #044F67; color: #fff">
                Клуб "{{ user.gym.name }}" 💪
            </v-card-title>
            <v-card-text class="pt-4 pb-4">
                <p class="text-h5">
                    Приветствуем вас, <b>{{ user.name }}</b> 👋
                </p>
                <v-row>
                    <v-col cols="12" lg="3" md="3" sm="12">
                        <v-btn block color="pink lighten-1" depressed dark>
                            Отчеты
                        </v-btn>
                    </v-col>
                    <v-col cols="12" lg="3" md="3" sm="12">
                        <v-btn block color="pink lighten-1" depressed dark>
                            Статистика
                        </v-btn>
                    </v-col>
                    <v-col cols="12" lg="3" md="3" sm="12">
                        <v-btn block color="pink lighten-1" depressed dark>
                            Учет посещения
                        </v-btn>
                    </v-col>
                    <v-col cols="12" lg="3" md="3" sm="12">
                        <v-btn block color="pink lighten-1" depressed dark to="/clients">
                            Клиенты
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
<!--        <v-row dense>
            <v-col cols="12" lg="2" md="2" sm="12">
                <h1>
                    Клуб "{{ $store.getters.USER.gym.name }}"
                </h1>
            </v-col>
        </v-row>-->
    </div>
</template>

<script>
export default {
    data: () => ({}),
    methods: {},
    computed: {
        user () {
            return this.$store.getters.USER;
        }
    },
};
</script>

<style scoped>

</style>
