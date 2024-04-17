<template>
    <div>
        <i-card-page title="Ревизия" v-if="revision">
            <div class="d-flex justify-start">
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>{{ revision.user.name }}</v-list-item-title>
                        <v-list-item-subtitle>Пользователь</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>{{ revision.store.name }}</v-list-item-title>
                        <v-list-item-subtitle>Склад</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>{{ revision.created_at }}</v-list-item-title>
                        <v-list-item-subtitle>Дата создания</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item two-line>
                    <v-list-item-content>
                        <v-list-item-title>{{ revision.status }}</v-list-item-title>
                        <v-list-item-subtitle>Статус</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item v-if="IS_SUPERUSER">
                    <v-list-item-content>
                        <v-list-item-title>
                            <router-link :to="`/revision/${revision.id}/table`">
                                <v-btn text color="primary" icon>
                                    <v-icon>mdi-information-outline</v-icon>
                                </v-btn>
                            </router-link>
                        </v-list-item-title>
                        <v-list-item-subtitle>Сводная таблица</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </div>
            <v-simple-table v-slot:default>
                <thead>
                <tr>
                    <th>Категория</th>
                    <th>Оригинальный файл</th>
                    <th>Загруженный файл</th>
                    <th>Когда загружено</th>
                    <th>Сводная таблица</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) of revision.files">
                    <td>{{ item.category.category_name }}</td>
                    <td>
                        <a :href="item.original_file" v-if="item.original_file">
                            <v-btn color="primary" text small>
                                Скачать
                                <v-icon>mdi-download</v-icon>
                            </v-btn>
                        </a>

                    </td>
                    <td>
                        <a :href="item.uploaded_file" v-if="item.uploaded_file">
                            <v-btn color="primary" text small>
                                Скачать
                                <v-icon>mdi-download</v-icon>
                            </v-btn>
                        </a>
                        <input
                            type="file"
                            @change="onFileChange($event, item.id)"
                            class="hidden"
                            :ref="`fileInput${item.id}`"
                            accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                        >
                        <v-btn
                            v-if="IS_SUPERUSER || !item.uploaded_file"
                            color="success"
                            text
                            small
                            @click="onUploadClick(item.id)"
                        >
                            Загрузить <span v-if="item.uploaded_file">повторно</span>
                            <v-icon>mdi-upload</v-icon>
                        </v-btn>
                    </td>
                    <td>
                        {{ item.uploaded_at || 'Не загружено' }}
                    </td>
                    <td v-if="IS_SUPERUSER">
                        <router-link :to="`/revision/${revision.id}/table?file=${item.id}`">
                            <v-btn text icon>
                                <v-icon>mdi-information-outline</v-icon>
                            </v-btn>
                        </router-link>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>

        </i-card-page>
    </div>

</template>

<script>
import revisionRepository from '@/repositories/RevisionRepository';

export default {
    data: () => ({
        revisionRepository: revisionRepository,
        revision: null
    }),
    methods: {
        onUploadClick(id) {
            const input = this.$refs[this.getInputRef(id)];
            input[0].click();
        },
        async onFileChange(event, id) {
            const file = event.target.files[0];
            this.$refs[this.getInputRef(id)][0].value = null;
            if (!file) {
                return undefined;
            }
            try {
                this.$loading.enable();
                const formData = new FormData;
                formData.append('file', file);
                await this.revisionRepository.upload(this.revision.id, id, formData);
                const { data } = await this.revisionRepository.get(this.$route.params.id);
                this.revision = data.revision;
            } catch (e) {

            } finally {
                this.$loading.disable();
            }
        },
        getInputRef(id) {
            return `fileInput${id}`;
        }
    },
    async mounted() {
        this.$loading.enable();
        const { data } = await this.revisionRepository.get(this.$route.params.id);
        this.revision = data.revision;
        this.$loading.disable();
    },
    computed: {}
};
</script>

<style scoped>

</style>
