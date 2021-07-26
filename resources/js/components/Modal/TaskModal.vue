<template>
    <v-dialog persistent v-model="state" max-width="800">
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">Задание</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Название задания"
                    v-model="task.title"
                />
                <v-textarea
                    label="Описание задания"
                    v-model="task.text"
                />
                <v-text-field
                    label="Дата начала"
                    type="date"
                    v-model="task.date_start"
                />
                <v-text-field
                    label="Дата окончания"
                    type="date"
                    v-model="task.date_finish"
                />
                <v-select
                    v-if="!editing"
                    class="my-2"
                    label="Магазины"
                    :items="stores"
                    item-value="id"
                    item-text="name"
                    v-model="task.store_ids"
                    multiple
                />
                <v-checkbox
                    label="Требует завершения"
                    v-model="task.is_completion_required"
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
    import {TOAST_TYPE} from "@/config/consts";
    import uploadFile, {deleteFile} from "@/api/upload";

    export default {
        data: () => ({
            task: {
                title: '',
                text: '',
                date_start: null,
                date_finish: null,
                is_completion_required: false,
                store_ids: [],
            },
            attachments: [],
        }),
        methods: {
            async onSubmit() {
                if (!this.editing) {
                    await this.createTask();
                } else {
                    await this.editTask();
                }
            },
            async editTask() {
                if (!this.validateTask()) { return; }
                const task = {
                    title: this.task.title,
                    text: this.task.text,
                    date_start: this.task.date_start,
                    date_finish: this.task.date_finish,
                    attachments: this.attachments,
                    is_completion_required: this.task.is_completion_required
                };
                try {
                    await this.$store.dispatch('EDIT_TASK', {
                        task,
                        id: this.task.id,
                    });
                    this.$toast.success('Задание успешно отредактировано!');
                    this.$emit('cancel');
                } catch (e) {
                    this.$toast.error('При редактировании задания произошла ошибка!');
                }
            },
            async createTask() {
                if (!this.validateTask()) { return; }
                try {
                    await this.$store.dispatch('CREATE_TASK', {...this.task, attachments: this.attachments});
                    this.$toast.success('Задание успешно создано!');
                    this.$emit('cancel');
                } catch (e) {
                    this.$toast.error('При создании задания произошла ошибка!');
                }
            },
            async onUpload(e) {
                try {
                    const response = await uploadFile(e.target.files[0], 'file', 'tasks');
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
            validateTask() {
                if (!this.task.title.length) {
                    this.$toast.error('Заполните наименование задания!');
                    return false;
                }
                if (!this.task.date_start || !this.task.date_finish) {
                    this.$toast.error('Заполните даты!');
                    return false;
                }
                if (!this.task.store_ids.length) {
                    this.$toast.error('Выберите необходимые магазины');
                    return false;
                }
                return true;
            }
        },
        computed: {
            stores() {
                return this.$store.getters.stores;
            },
            editing() {
                return Object.keys(this.currentTask).length;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
                required: true
            },
            currentTask: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(val) {
                if (val) {
                    if (this.editing) {
                        this.task = {...this.currentTask};
                        this.task.store_ids = [1];
                        this.attachments = [...this.task.attachments];
                    } else {
                        this.task = {
                            title: '',
                            text: '',
                            date_start: null,
                            date_finish: null,
                            is_completion_required: false,
                            store_ids: [],
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
