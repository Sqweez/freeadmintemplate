<template>
    <div>
        <i-card-page title="Юридические лица">
            <v-btn
                depressed
                to="/legal-entity/create"
                tag="v-btn"
                color="success"
                tile
                class="mb-2"
            >
                Создать
                <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-simple-table v-slot:default>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование</th>
                    <th>Банковские счета</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(entity, key) of entities" :key="key">
                    <td>{{ key + 1 }}</td>
                    <td>{{ entity.name }}</td>
                    <td>
                        <div class="d-flex" style="align-items: center;">
                            <span v-if="!entity.bank_accounts.length">
                                Нет банковских счетов
                            </span>
                            <div v-else>
                                <ul>
                                    <li
                                        v-for="(acc, idx) of entity.bank_accounts"
                                        :key="`bank-${idx}`"
                                        class="d-flex mb-3"
                                        style="align-items: center;"
                                    >
                                        <span>{{ acc.name }}</span>
                                        <v-btn class="ml-3" x-small icon
                                               @click="$router.push(`/bank-account/${entity.id}/update/${acc.id}`)">
                                            <v-icon>
                                                mdi-pencil
                                            </v-icon>
                                        </v-btn>
                                    </li>
                                </ul>
                            </div>
                            <v-btn icon color="success" class="ml-3"
                                   @click="$router.push(`/bank-account/${entity.id}/create`)">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </div>
                    </td>
                    <td>
                        <v-btn icon @click="$router.push(`/legal-entity/${entity.id}`)">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </i-card-page>
    </div>
</template>

<script>
export default {
    data: () => ({}),
    computed: {
        entities() {
            return this.$store.getters.legal_entities;
        }
    },
    methods: {},
    async mounted() {
        await this.$loading.enable();
        await this.$store.dispatch('getLegalEntities');
        await this.$loading.disable();
    },
}
</script>

<style scoped lang="scss">

</style>
