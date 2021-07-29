<template>
    <div>
        <v-card>
            <v-card-title>
                Обучение
            </v-card-title>
            <v-card-text>
                <v-btn color="success" @click="educationModal = true" v-if="isAdmin">
                    Создать обучение <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-data-table
                    :items-per-page="10"
                    :items="educations"
                    :headers="headers"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.attachments="{ item }">
                        <ul>
                            <li v-for="i of item.attachments">
                                <a :href="i.link" target="_blank">
                                    {{ i.name }}
                                </a>
                            </li>
                        </ul>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn icon text @click="currentEducation = {...item}; educationModal = true;">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon text color="error" @click="currentEducation = {...item}; deleteModal = true;">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <EducationModal
            :state="educationModal"
            :current-education="currentEducation"
            @cancel="educationModal = false; currentEducation = {}"/>
        <ConfirmationModal
            :state="deleteModal"
            @cancel="deleteModal = false; currentEducation = {};"
            message="Вы действительно хотите удалить выбранное обучение"
            :on-confirm="deleteEducation" />
    </div>
</template>

<script>
    import EducationModal from "@/components/Modal/EducationModal";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, EducationModal},
        async created() {
            await this.$store.dispatch('GET_EDUCATIONS');
        },
        data: () => ({
            deleteModal: false,
            educationModal: false,
            currentEducation: {},
        }),
        methods: {
            async deleteEducation() {
                await this.$store.dispatch('DELETE_EDUCATION', this.currentEducation.id);
                this.$toast.success('Обучение успешно удалено!');
                this.currentEducation = {};
                this.deleteModal = false;
            },
        },
        computed: {
            educations() {
                return this.$store.getters.EDUCATIONS;
            },
            isAdmin() {
                return this.$store.getters.IS_ADMIN;
            },
            headers() {
                const headers = [
                    {
                        text: 'Наименование',
                        value: 'title'
                    },
                    {
                        text: 'Описание',
                        value: 'description'
                    },
                    {
                        value: 'author.name',
                        text: 'Автор'
                    },
                    {
                        value: 'attachments',
                        text: 'Вложения'
                    },
                ];

                if (this.isAdmin) {
                    headers.push({
                        value: 'actions',
                        text: 'Действие'
                    })
                }

                return headers;
            }
        }
    }
</script>

<style scoped lang="scss">

</style>
