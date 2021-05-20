<template>
    <div>
        <v-card>
            <v-card-title>
                Список новостей
            </v-card-title>
            <v-card-text>
                <v-btn color="error" class="my-5" @click="newsModal = true;">
                    Добавить новость +
                </v-btn>
                <v-btn color="success" target="_blank" href="https://iron-addicts.kz/api/cache/update-all">
                    Сбросить кэш сайта
                </v-btn>
                <v-data-table
                    :loading="news.length === 0"
                    loading-text="Идет загрузка новостей"
                    :search="search"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :items="news"
                    @page-count="pageCount = $event"
                    :items-per-page="10"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                    <template v-slot:item.news_image="{ item }">
                        <img
                            width="300"
                            :src="'../../storage/' + item.image"
                            alt="">
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn icon @click="currentNews = item; newsModal = true;">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon @click="confirmationModal = true; newsId = item.id;">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <NewsModal
            :state="newsModal"
            :currentNews="currentNews"
            @cancel="newsModal = false;
            currentNews = {};"
        />
        <ConfirmationModal
            :state="confirmationModal"
            message="Вы действительно хотите удалить выбранную новость?"
            @cancel="confirmationModal = false; newsId = null"
            :on-confirm="deleteNews"
        />
    </div>
</template>

<script>
    import NewsModal from "@/components/Modal/NewsModal";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import axios from 'axios';
    import showToast from "@/utils/toast";
    export default {
        components: {ConfirmationModal, NewsModal},
        async created() {
            await this.$store.dispatch('GET_NEWS');
            await this.$store.dispatch('GET_PRODUCTS_v2');
        },
        data: () => ({
            newsModal: false,
            newsId: null,
            confirmationModal: false,
            currentNews: {},
            search: '',
            headers: [
                {
                    value: 'id',
                    text: 'ID',
                    sortable: false
                },
                {
                    value: 'title',
                    text: 'Заголовок',
                    sortable: true
                },
                {
                    value: 'news_image',
                    text: 'Главное изображение',
                    sortable: false
                },
                {
                    value: 'short_text',
                    text: 'Короткое содержание',
                    sortable: false,
                },
                {
                    value: 'actions',
                    text: 'Действия',
                    sortable: false,
                }
            ],
        }),
        methods: {
            async deleteNews() {
                await this.$store.dispatch('DELETE_NEWS', this.newsId);
                this.confirmationModal = false;
                this.newsId = null;
            },
            async updateSiteCache() {
                await this.$store.commit('enableLoading');
                await axios.get('https://iron-addicts.kz/api/cache/update-all')
                await this.$store.commit('disableLoading');
                showToast('Кэш сброшен')
            }
        },
        computed: {
            news() {
                return this.$store.getters.NEWS;
            }
        }
    }
</script>

<style scoped>

</style>
