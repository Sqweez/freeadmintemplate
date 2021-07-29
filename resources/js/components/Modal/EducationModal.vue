<template>
    <v-dialog persistent v-model="state" max-width="800">
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">Обучение</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Название обучения"
                    v-model="education.title"
                />
                <v-textarea
                    label="Описание обучения"
                    v-model="education.description"
                />
                <ul v-if="attachments.length">
                    <li v-for="(item, key) of attachments">
                        <a :href="item.link" target="_blank">
                            {{ item.name }}
                        </a>
                        <v-btn icon color="error" @click="deleteAttachment(key)">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </li>
                </ul>
                <input type="file" class="d-none" ref="fileInput" @change="onUpload">
                <v-btn color="primary" @click="$refs.fileInput.click()">
                    Загрузить вложение <v-icon>mdi-attachment</v-icon>
                </v-btn>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">Сохранить <v-icon>mdi-check</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import uploadFile, {deleteFile} from "@/api/upload";

    export default {
        data: () => ({
            education: {
                title: '',
                description: '',
            },
            attachments: [],
        }),
        methods: {
            async onSubmit() {
                if (!this.editing) {
                    await this.createEducation();
                } else {
                    await this.editEducation();
                }
            },
            async editEducation() {
                if (!this.validate()) { return; }
                const education = {
                    title: this.education.title,
                    description: this.education.description,
                    attachments: this.attachments,
                };
                try {
                    console.log(education);
                    await this.$store.dispatch('EDIT_EDUCATION', {
                        education,
                        id: this.education.id,
                    });
                    this.$toast.success('Обучение успешно отредактировано!');
                    this.$emit('cancel');
                } catch (e) {
                    console.error(e);
                    this.$toast.error('При редактировании обучения произошла ошибка!');
                }
            },
            async createEducation() {
                if (!this.validate()) { return; }
                try {
                    await this.$store.dispatch('CREATE_EDUCATION', {...this.education, attachments: this.attachments});
                    this.$toast.success('Обучение успешно создано!');
                    this.$emit('cancel');
                } catch (e) {
                    this.$toast.error('При создании обучения произошла ошибка!');
                }
            },
            async onUpload(e) {
                try {
                    const response = await uploadFile(e.target.files[0], 'file', 'education');
                    this.attachments.push({
                        url: response.data,
                        name: e.target.files[0].name,
                        link: `${window.location.origin}/storage/${response.data}`
                    })
                    this.$refs.fileInput.value = '';
                } catch (e) {
                    this.$toast.error('При загрузке файла произошла ошибка!');
                }
            },
            async deleteAttachment(key) {
                await deleteFile(this.attachments[key]);
                this.attachments.splice(key, 1);
            },
            validate() {
                if (!this.education.title.length) {
                    this.$toast.error('Заполните наименование обучения!');
                    return false;
                }
                return true;
            }
        },
        computed: {
            editing() {
                return Object.keys(this.currentEducation).length;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
                required: true
            },
            currentEducation: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(val) {
                if (val) {
                    if (this.editing) {
                        this.education = {...this.currentEducation};
                        this.attachments = [...this.education.attachments];
                    } else {
                        this.education = {
                            title: '',
                            description: '',
                        };
                        this.attachments = [];
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
