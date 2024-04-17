<template>
    <i-card-page title="Сводная таблица">
        <div v-if="revision">
            <div class="d-flex justify-start" >
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>{{ revision.count_expected }} шт</v-list-item-title>
                        <v-list-item-subtitle>Количество, ожидаемое</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ revision.count_actual }} шт.
                            <b :class="[revision.count_delta < 0  ?'red--text' : 'green--text']">({{ revision.count_delta }})</b>
                        </v-list-item-title>
                        <v-list-item-subtitle>Количество, фактическое</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title> {{ revision.price_expected_total  }}</v-list-item-title>
                        <v-list-item-subtitle>Стоимость, ожидаемое</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ revision.price_actual_total  }}
                            <b :class="[revision.price_delta < 0  ?'red--text' : 'green--text']">({{ revision.price_delta | priceFilters }})</b>
                        </v-list-item-title>
                        <v-list-item-subtitle>Стоимость, фактическое</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </div>
            <v-text-field label="Поиск" v-model="search"/>
            <v-data-table :headers="headers" :items="products" :search="search">
                <template v-slot:item.delta="{ item }">
                    <b :class="[item.delta < 0  ?'red--text' : 'green--text']">{{ item.delta }}</b>
                </template>
            </v-data-table>
        </div>

    </i-card-page>
</template>

<script>
import revisionRepository from '@/repositories/RevisionRepository';

export default {
    data: () => ({
        revisionRepository: revisionRepository,
        revision: null,
        products: null,
        headers: [
            {
                text: 'Наименование',
                value: 'name'
            },
            {
                text: 'Стоимость',
                value: 'price'
            },
            {
                text: 'Ожидаемое количество',
                value: 'count_expected'
            },
            {
                text: 'Фактическое количество',
                value: 'count_actual'
            },
            {
                text: 'Разница',
                value: 'delta'
            }
        ],
        search: '',
    }),
    methods: {},
    computed: {},
    async mounted() {
        try {
            this.$loading.enable();
            const { data } = await this.revisionRepository.table(this.$route.params.id, this.$route.query.file);
            this.revision = {
                ...data.table,
                products: null
            };
            this.products = data.table.products;
        } catch (e) {
            console.log(e);
        } finally {
            this.$loading.disable();
        }
    }
};
</script>

<style scoped>

</style>
