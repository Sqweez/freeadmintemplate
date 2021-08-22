<template>
    <div>
        <v-card>
            <v-card-title>
                Список документов
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Тип документа"
                    :items="docTypes"
                    item-text="value"
                    item-value="id"
                    v-model="docType"
                />
                <v-text-field
                    label="Поиск"
                    v-model="searchQuery"
                />
                <v-data-table
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :loading="loading"
                    :search="searchQuery"
                    loading-text="Идет загрузка документов..."
                    :items="docs"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.document_number="{ item }">
                        № {{ item.document_number }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn icon text>
                            <v-icon @click="downloadDocument(item)">
                                mdi-download
                            </v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>

    import axios from 'axios';

    export default {
        data: () => ({
            headers: [
                {
                    value: 'document_number',
                    text: 'Номер документа'
                },
                {
                    value: 'type',
                    text: 'Тип документа'
                },
                {
                    value: 'date',
                    text: 'Дата создания'
                },
                {
                    value: 'actions',
                    text: 'Скачать'
                }

            ],
            searchQuery: '',
            loading: false,
            documents: [],
            docType: -1,
            docTypes: [
                {
                    id: -1,
                    value: 'Все'
                },
                {
                    id: 1,
                    value: 'Накладная'
                },
                {
                    id: 2,
                    value: 'Счет-фактура'
                },
                {
                    id: 3,
                    value: 'Счет на оплату'
                },
                {
                    id: 4,
                    value: 'Товарный чек'
                }
            ],
        }),
        methods: {
            downloadDocument(item) {
                const link = document.createElement('a');
                link.href = `${window.location.origin}/${item.document}`;
                link.click();
            }
        },
        computed: {
            docs() {
                return this.documents.filter(doc => {
                    if (this.docType === -1) {
                        return doc;
                    }
                    return doc.document_type === this.docType;
                })
            }
        },
        async mounted() {
            const response = await axios.get(`/api/v2/documents/index`);
            this.documents = response.data;
        }
    }
</script>

<style scoped>

</style>
