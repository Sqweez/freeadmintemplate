<template>
    <v-dialog persistent max-width="1200" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Редактор новостей</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Заголовок новости"
                    v-model="title"
                />
                <v-btn text class="my-3" @click="$refs.fileInput.click()">
                    Загрузить фото
                    <v-icon>mdi-photo</v-icon>
                </v-btn>
                <img
                    class="d-block my-3"
                    v-if="image"
                    :src="'../storage/' + image"
                    width="400"
                    alt="Изображение">
                <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                <vue-editor
                    id="editor"
                    use-custom-image-handler
                    @image-added="handleImageAdded"
                    :editor-options="editorSettings"
                    v-model="text"> </vue-editor>
                <v-text-field
                    label="Короткое описание"
                    v-model="short_text"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import { VueEditor, Quill } from "vue2-editor";
    import ImageResize from 'quill-image-resize-vue';
    import uploadFile from "@/api/upload";
    import showToast from "@/utils/toast";
    import {TOAST_TYPE} from "@/config/consts";
    Quill.register('modules/imageResize', ImageResize);


    export default {
        data: () => ({
            text: '',
            title: '',
            short_text: '',
            image: null,
            editorSettings: {
                modules: {
                    imageResize: {},
                }
            }
        }),
        methods: {
            async onSubmit() {
                const news = {
                    text: this.text,
                    title: this.title,
                    short_text: this.short_text,
                    image: this.image,
                };
                if (Object.keys(this.currentNews).length) {
                    news.id = this.currentNews.id;
                    await this.$store.dispatch('EDIT_NEWS', news);
                } else {
                    await this.$store.dispatch('ADD_NEWS', news);
                }
                this.$emit('cancel');
            },
            async uploadPhoto(e) {
                try {
                    const file = e.target.files[0];
                    const response = await uploadFile(file, 'file', 'news');
                    this.image = response.data;
                } catch (e) {
                    showToast('Во время загрузки файла произошла ошибка, попробуйте загрузить другую фотографию', TOAST_TYPE.ERROR);
                } finally {
                    this.$refs.fileInput.value = null;
                }
            },
            async handleImageAdded(file, Editor, cursorLocation, resetUploader) {
                const response = await uploadFile(file, 'file', 'news');
                const photo = `${window.location.protocol}//${window.location.hostname}:8000/storage/${response.data}`;
                Editor.insertEmbed(cursorLocation, "image", photo);
                resetUploader();
            }
        },
        components: {
            VueEditor
        },
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: true,
            },
            currentNews: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(val) {
                if (val) {
                    if (Object.keys(this.currentNews).length) {
                        this.text =  this.currentNews.text;
                        this.title =  this.currentNews.title;
                        this.short_text =  this.currentNews.short_text;
                        this.image =  this.currentNews.image;
                    }
                }
            }
        }
    }
</script>

<style scoped>
    .quillWrapper {
        background-color: #fff;
        color: #000;
    }

</style>
