<template>
    <div>
        <v-card>
            <v-card-title>
                SEO-категории
            </v-card-title>
            <v-card-text>
                <v-btn color="success" target="_blank" href="https://iron-addicts.kz/api/cache/update-all">
                    Сбросить кэш сайта
                </v-btn>
                <v-checkbox
                    label="Категории без SEO-текста"
                    v-model="withoutText"
                />
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
                    item-key="key"
                    :items="categories"
                    :headers="headers"
                    :search="search"
                >
                    <template v-slot:item.description="{ item }">
                        <v-expansion-panels v-if="item.seo_text">
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Описание
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <span v-html="item.seo_text.content"></span>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <span v-else>-</span>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn icon @click="showDialogModal(item)">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <v-dialog persistent v-model="showDialog" max-width="800">
            <v-card>
                <v-card-title>
                    SEO Категории
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        label="Meta H1"
                        v-model="seoContent.meta_h1"
                    />
                    <v-text-field
                        label="Meta Title"
                        v-model="seoContent.meta_title"
                    />
                    <v-text-field
                        label="Meta Description"
                        v-model="seoContent.meta_description"
                    />
                    <vue-editor
                        id="editor"
                        :editor-options="editorSettings"
                        v-model="seoContent.content"/>
                </v-card-text>
                <v-card-actions>
                    <v-btn text @click="closeDialog">
                        Отмена
                    </v-btn>
                    <v-spacer/>
                    <v-btn color="success" text @click="onSubmit">
                        Сохранить
                        <v-icon>mdi-check</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import {VueEditor, Quill} from "vue2-editor";
import ACTIONS from "@/store/actions";
import axios from "axios";

export default {
    components: {
        VueEditor
    },
    data: () => ({
        search: '',
        withoutText: false,
        headers: [
            {
                value: 'type',
                text: 'Тип'
            },
            {
                value: 'name',
                text: 'Наименование'
            },
            {
                value: 'description',
                text: 'Текст'
            },
            {
                value: 'meta_h1',
                text: 'Meta H1',
            },
            {
                value: 'meta_title',
                text: 'Meta Title'
            },
            {
                value: 'meta_description',
                text: 'Meta Description'
            },
            {
                value: 'actions',
                text: 'Действие'
            }
        ],
        showDialog: false,
        entity: {},
        seoContent: {
            meta_h1: '',
            meta_title: '',
            meta_description: '',
            content: ''
        },
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
        categories() {
            const categories = this.$store.getters.categories;
            const output = [];
            categories.forEach(c => {
                output.push({...c, type: 'Категория', type_eng: 'category', name: c.name});
                c.subcategories.forEach(s => {
                    output.push({
                        ...s,
                        type: `Подкатегория (${c.name})`,
                        type_eng: 'subcategory',
                        name: s.subcategory_name,
                    });
                })
            })
            return output.map(o => ({
                ...o,
                key: `${o.type_eng}_${o.id}`
            })).filter(o => {
                return this.withoutText === false ? true : !o.seo_text;
            });
        }
    },
    methods: {
        closeDialog() {
            this.showDialog = false;
            this.seoContent = {
                meta_h1: '',
                meta_title: '',
                meta_description: '',
                content: ''
            };
        },
        showDialogModal(entity) {
            this.entity = {...entity};
            this.seoContent = {
                content: entity.seo_text ? entity.seo_text.content : '',
                meta_h1: entity.meta_h1,
                meta_title: entity.meta_title,
                meta_description: entity.meta_description,
            };
            this.showDialog = true;
        },
        async onSubmit() {
            try {
                this.$loading.enable();
                const apiEndpoint = `/api/v2/seo/text/${this.entity.type_eng}/${this.entity.id}`;
                const content = this.seoContent;
                this.closeDialog();
                await axios.post(apiEndpoint, content);
                await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
                this.$toast.success('Текст обновлен!');
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
