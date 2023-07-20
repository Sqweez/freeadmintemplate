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
                Создать <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-simple-table v-slot:default>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(entity, key) of entities" :key="key">
                    <td>{{ key + 1 }}</td>
                    <td>{{ entity.name }}</td>
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
        entities () {
            return this.$store.getters.legal_entities;
        }
    },
    methods: {},
    async mounted () {
        await this.$loading.enable();
        await this.$store.dispatch('getLegalEntities');
        await this.$loading.disable();
    },
}
</script>

<style scoped lang="scss">

</style>
