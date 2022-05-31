<template>
    <div>
        <v-card>
            <v-card-title>
                SEO-категории
            </v-card-title>
            <v-card-text>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>Тип</th>
                        <th>Наименование</th>
                        <th>Текст</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="category of categories" :key="`${category.name}-${category.id}`">
                        <td>{{ category.type }}</td>
                        <td>{{ category.name }}</td>
                        <td>
                            <span v-if="category.seo_text" v-html="category.seo_text.content"></span>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <v-btn icon @click="showDialogModal(category)">
                                <v-icon>
                                    mdi-pencil
                                </v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <v-dialog persistent v-model="showDialog" max-width="800">
            <v-card>
                <v-card-title>
                    Редактор текста
                </v-card-title>
                <v-card-text>
                    <vue-editor
                        id="editor"
                        :editor-options="editorSettings"
                        v-model="content" />
                </v-card-text>
                <v-card-actions>
                    <v-btn text @click="closeDialog">
                        Отмена
                    </v-btn>
                    <v-spacer />
                    <v-btn color="success" text @click="onSubmit">
                        Сохранить <v-icon>mdi-check</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { VueEditor, Quill } from "vue2-editor";
import ACTIONS from "@/store/actions";
import axios from "axios";
export default {
    components: {
        VueEditor
    },
    data: () => ({
        showDialog: false,
        entity: {},
        content: '',
        editorSettings: {
            modules: {
                imageResize: {},
            },
            preserveWhiteSpace: true
        },
    }),
    async mounted() {
        await this.$loading.enable();
        await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        await this.$loading.disable();
    },
    computed: {
        categories () {
            const categories = this.$store.getters.categories;
            const output = [];
            categories.forEach(c => {
                output.push({...c, type: 'Категория', type_eng: 'category', name: c.name});
                c.subcategories.forEach(s => {
                    output.push({...s, type: `Подкатегория (${c.name})`, type_eng: 'subcategory', name: s.subcategory_name});
                })
            })
            return output;
        }
    },
    methods: {
        closeDialog () {
            this.showDialog = false;
            this.entity = {};
        },
        showDialogModal (entity) {
            this.entity = {...entity};
            this.content = entity.seo_text ? entity.seo_text.content : '';
            this.showDialog = true;
        },
        async onSubmit () {
            try {
                this.$loading.enable();
                const apiEndpoint = `/api/v2/seo/text/${this.entity.type_eng}/${this.entity.id}`;
                await axios.post(apiEndpoint, {
                    content: this.content
                });
                await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
                this.closeDialog();
            } catch (e) {
                this.$toast.error('Произошла ошибка!');
            } finally {
                this.$loading.disable();
            }
        },
    }
}
</script>

<style scoped>
.quillWrapper {
    background-color: #fff;
    color: #000;
}

</style>
