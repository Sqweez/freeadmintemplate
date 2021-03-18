<template>
    <div>
        <v-card>
            <v-card-title>
                Товары
            </v-card-title>
            <v-card-text>
                <v-text-field
                    class="mt-2"
                    v-model="search"
                    solo
                    clearable
                    label="Поиск товара"
                    single-line
                    hide-details
                ></v-text-field>
                <v-data-table
                    :search="search"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :page.sync="pagination.page"
                    :items="products"
                    @page-count="pageCount = $event"
                    :items-per-page="10"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
                <div class="text-xs-center pt-2">
                    <v-pagination
                        v-model="pagination.page"
                        :total-visible="10"
                        :length="pageCount"></v-pagination>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    export default {
        data: () => ({
            search: '',
            headers: [
                {
                    value: 'products',
                    text: 'Товары',
                    sortable: false,
                },
                {
                    value: 'is_hit',
                    text: 'Хит продаж',
                    sortable: false,
                },
            ],
            pagination: {
                ascending: true,
                rowsPerPage: 10,
                page: 1
            },
            products: [],
            pageCount: 1,
        }),
        methods: {},
        computed: {}
    }
</script>

<style scoped>

</style>
