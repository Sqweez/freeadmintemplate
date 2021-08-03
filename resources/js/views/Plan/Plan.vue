<template>
    <div>
        <v-card>
            <v-card-title>
                План продаж
            </v-card-title>
            <v-card-text>
                <v-simple-table v-slot:default :dense="false">
                    <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>План на неделю</th>
                        <th>План на месяц</th>
                        <th>Премия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(store) of plans">
                        <td>{{ store.name }}</td>
                        <td>
                            <v-text-field
                                label="План неделя"
                                v-model="store.week_plan"
                                type="number"></v-text-field>
                        </td>
                        <td>
                            <v-text-field
                                v-model="store.month_plan"
                                label="План месяц"
                                type="number"></v-text-field>
                        </td>
                        <td>
                            <v-text-field
                                v-model="store.prize"
                                label="Премия"
                                type="number" />
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <v-btn color="success" class="mt-5" @click="savePlans">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-text>
        </v-card>
        <v-card>
            <v-card-title>
                Мотивация по брендам
            </v-card-title>
            <v-card-text>
                <v-simple-table v-slot:default :dense="false">
                    <thead>
                    <tr>
                        <th>Бренды</th>
                        <th>План</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(motivation, key) of motivations">
                        <td>
                            <v-autocomplete
                                label="Бренды"
                                multiple
                                v-model="motivation.brands"
                                :items="brands"
                                item-text="manufacturer_name"
                                item-value="id"
                            />
                        </td>
                        <td>
                            <v-text-field
                                v-for="amount of motivation.amount"
                                :label="amount.user_name"
                                type="number"
                                v-model.number="amount.amount"
                            />
                        </td>
                        <td>
                            <v-btn icon text @click="motivations.splice(1, key)">
                                <v-icon>
                                    mdi-cancel
                                </v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <v-btn color="primary" class="mt-5" @click="addMotivation">
                    Добавить пункт <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-btn color="success" class="ml-5 mt-5" @click="saveBrandsMotivation">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import ACTIONS from '@/store/actions';

    export default {
        data: () => ({
            plans: [],
            motivations: [],
        }),
        methods: {
            async savePlans() {
                await this.$store.dispatch(ACTIONS.SAVE_PLANS, this.plans);
                await this.init();
                this.$toast.success('План успешно изменен!');
            },
            async saveBrandsMotivation() {
                await this.$store.dispatch(ACTIONS.CREATE_BRANDS_MOTIVATION, this.motivations);
                this.$toast.success('Информация обновлена');
            },
            addMotivation() {
                this.motivations.push({
                    brands: [],
                    amount: [...this.sellers.map(s => ({
                        user_id: s.id,
                        user_name: `${s.name} | ${s.city}`,
                        amount: 0,
                    }))],
                })
            },
            async init() {
                this.plans = this.stores.map(s => {
                    const plan = this._plans.find(p => p.store_id == s.id);
                    if (!plan) {
                        return {
                            store_id: s.id,
                            week_plan: 0,
                            month_plan: 0,
                            name: s.name,
                            prize: 0,
                        }
                    }
                    plan.name = s.name;
                    return plan;
                });
            }
        },
        computed: {
            stores() {
                return this.$store.getters.shops;
            },
            _plans() {
                return this.$store.getters.PLANS;
            },
            brands() {
                return this.$store.getters.manufacturers;
            },
            sellers() {
                return this.$store.getters.USERS_SELLERS;
            },
            BRANDS_MOTIVATION_PLAN() {
                return this.$store.getters.BRANDS_MOTIVATION_PLAN;
            }
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_PLANS);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_BRANDS_MOTIVATIONS_PLAN);
            await this.init();
        },
        watch: {
            BRANDS_MOTIVATION_PLAN(value) {
                this.motivations = [...value];
            }
        }
    }
</script>

<style scoped>

</style>
