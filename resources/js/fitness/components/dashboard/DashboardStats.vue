<template>
    <v-row dense v-bind="$attrs">
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-card
                size="x-large"
                color="#D81B60"
            >
                <v-card-text class="d-flex">
                    <v-icon color="#fff">
                        mdi-trending-up
                    </v-icon>
                    <div class="ml-4" style="color: #fff;">
                        <h3>Выручка на сегодня</h3>
                        <h2>
                            {{ 0 | priceFilters }}
                        </h2>
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-card
            >
                <v-card-text class="d-flex">
                    <v-icon color="#000" size="x-large">
                        mdi-trending-up
                    </v-icon>
                    <div class="ml-4">
                        <h3>Касса</h3>
                        <h2>
                            {{ 0 | priceFilters }}
                        </h2>
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-card
            >
                <v-card-text class="d-flex">
                    <v-icon color="#000" size="x-large">
                        mdi-trending-up
                    </v-icon>
                    <div class="ml-4">
                        <h3>Безналичные</h3>
                        <h2>
                            {{ 0 | priceFilters }}
                        </h2>
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-card
                size="x-large"
                color="#42A5F5"
            >
                <v-card-text class="d-flex">
                    <v-icon color="#fff">
                        mdi-backup-restore
                    </v-icon>
                    <div class="ml-4" style="color: #fff;">
                        <h3>Списания по картам</h3>
                        <h2>
                            {{ 0 | priceFilters }}
                        </h2>
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-card
                size="x-large"
                color="#4DB6AC"
            >
                <v-card-text class="d-flex">
                    <v-icon color="#fff">
                        mdi-clipboard-account
                    </v-icon>
                    <div class="ml-4" style="color: #fff;">
                        <h3>Активных клиентов</h3>
                        <h2>
                            {{ 0 | priceFilters }}
                        </h2>
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" lg="2" md="2" sm="12">
            <v-card
                size="x-large"
                color="#FFB74D"
            >
                <v-card-text class="d-flex">
                    <v-icon color="#fff">
                        mdi-account-multiple
                    </v-icon>
                    <div class="ml-4" style="color: #fff;">
                        <h3>Всего клиентов</h3>
                        <h2>
                            {{ 0 | priceFilters }}
                        </h2>
                    </div>
                </v-card-text>
            </v-card>
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
