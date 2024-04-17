<template>
    <div>
        <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64" />
        </v-overlay>
        <v-card>
            <v-card-title>
                Ревизии
            </v-card-title>
            <v-card-text>
                <div class="d-flex align-center" STYLE="column-gap: 24px;">
                    <v-btn color="success" @click="onRevisionCreate">
                        Создать ревизию
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                    <v-select
                        :disabled="!IS_SUPERUSER"
                        label="Магазин"
                        :items="stores"
                        item-text="name"
                        v-model="store_id"
                        item-value="id"
                    />
                </div>

                <v-data-table :headers="headers" :items="revisions" class="elevation-1">
                    <template v-slot:item.actions="{ item }">
                        <router-link  :to="item.link">
                            <v-btn icon >
                                <v-icon>mdi-information-outline</v-icon>
                            </v-btn>
                        </router-link>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import RevisionModal from '@/components/Modal/RevisionModal';
import RevisionInfoModal from '@/components/Modal/RevisionInfoModal';
import axiosClient from '@/utils/axiosClient';
import revisionRepository from '@/repositories/RevisionRepository';

export default {
    components: { RevisionInfoModal, RevisionModal },
    data: () => ({
        revisionRepository: revisionRepository,
        loading: false,
        store_id: null,
        revisionModal: false,
        revisions: [],
        revisionId: null,
        revisionInfoModal: false,
        headers: [
            { text: '#', value: 'id' },
            { text: 'Пользователь', value: 'user.name' },
            { text: 'Статус', value: 'status' },
            { text: 'Магазин', value: 'store.name' },
            { text: 'Сгенерировано файлов', value: 'files_count' },
            { text: 'Загружено файлов', value: 'uploaded_files_count' },
            { text: 'Процент выполнения', value: 'percentage_completed' },
            { text: 'Дата создания', value: 'created_at' },
            { text: 'Действие', value: 'actions'}
        ]
    }),
    async created() {
        const { data } = await this.revisionRepository.all();
        this.revisions = data.revisions;
        this.store_id = this.user.store_id;
    },
    methods: {
        async getFiles() {
            if (this.loading) {
                return;
            }
            this.loading = true;
            const { data } = await axiosClient.get(`/api/revision/file/get?store_id=${this.store_id}`);
            const link = document.createElement('a');
            link.href = data;
            link.click();
            this.loading = false;
        },
        async onRevisionCreate() {
            try {
                this.$loading.enable();
                const payload = {
                    store_id: this.store_id,
                    user_id: this.user.id,
                };
                const { data } = await axiosClient.post('/v3/revision', payload);
                this.revisions.push(data.data);
                this.$toast.success('Ревизия успешно создана!');
            } catch (e) {
                this.$toast.error('Что-то пошло не так!');
            } finally {
                this.$loading.disable();
            }
        }
    },
    computed: {
        user() {
            return this.$store.getters.USER;
        },
        stores() {
            return this.$store.getters.stores;
        },
        is_admin() {
            return this.$store.getters.IS_ADMIN;
        }
    }
};
</script>

<style scoped>

</style>
