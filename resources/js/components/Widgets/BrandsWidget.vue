<template>
    <v-card>
        <v-responsive
            :min-height="200"
            v-if="loading"
            class="text-center d-flex justify-center align-center">
            <v-progress-circular
                :size="50"
                color="primary"
                indeterminate
            ></v-progress-circular>
        </v-responsive>
        <v-responsive v-else>
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">Мотивация по брендам</span>
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col v-for="(store) of BRANDS_MOTIVATION" v-if="(IS_ADMIN || IS_OBSERVER || IS_BOSS || IS_MARKETOLOG) || USER.id == store.id" cols="12" md="6" lg="auto">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title class="font-weight-bold">
                                        {{ store.name }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <div v-for="(item, index) of store.motivations">
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title class="font-weight-bold">
                                        {{ item.name }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.plan | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        План
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.sum | priceFilters }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Текущий показатель
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-title>
                                    <v-progress-linear
                                        color="light-green darken-2"
                                        height="20"
                                        :value="item.percent"
                                        striped
                                    >
                                        <template v-slot:default="{ value }">
                                            {{ item.percent }}%
                                        </template>
                                    </v-progress-linear>
                                </v-list-item-title>
                            </v-list-item>
                            </div>
                        </v-list>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import {mapGetters} from 'vuex';

    export default {
        data: () => ({
            plans: [],
            loading: true,
        }),
        computed: {
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
            shops() {
                return this.$store.getters.shops;
            },
            ...mapGetters([
                'IS_ADMIN',
                'USER',
                'IS_OBSERVER',
                'BRANDS_MOTIVATION'
            ]),
        },
        async created() {
            this.loading = true;
            await this.$store.dispatch('getBrandsMotivation');
            this.loading = false;
        },
        methods: {

        }
    }
</script>

<style lang="scss">
    .total td{
        border-top: 1px solid #ffffff;
    }

    .plan__data {
        color: #fff;
        td {
            font-size: 14px!important;
        }
    }
</style>
