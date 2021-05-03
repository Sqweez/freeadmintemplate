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
    </div>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import showToast from "../../utils/toast";

    export default {
        data: () => ({
            plans: [],
        }),
        methods: {
            async savePlans() {
                await this.$store.dispatch(ACTIONS.SAVE_PLANS, this.plans);
                await this.init();
                await showToast('План успешно изменен!')
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
            }
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_STORES);
            await this.$store.dispatch(ACTIONS.GET_PLANS);
            await this.init();
        }
    }
</script>

<style scoped>

</style>
