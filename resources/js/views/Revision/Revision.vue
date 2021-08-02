<template>
    <div>
        <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card>
            <v-card-title>
                Ревизии
            </v-card-title>
            <v-card-text>
                <div class="d-flex align-center">
                    <v-btn color="primary" @click="getFiles">Получить список товаров</v-btn>
                    <v-select
                        class="ml-10"
                        v-if="is_admin"
                        label="Магазин"
                        :items="stores"
                        item-text="name"
                        v-model="store_id"
                        item-value="id"></v-select>
                </div>


                <v-btn color="success" @click="revisionModal = true">
                    Создать ревизию <v-icon>mdi-plus</v-icon>
                </v-btn>

                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>Ответственный</th>
                        <th>Дата</th>
                        <th>Подробнее</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(revision, idx) of revisions" :key="idx">
                        <td>{{ revision.store.name }}</td>
                        <td>{{ revision.user.name }}</td>
                        <td>{{ revision.created_at }}</td>
                        <td>
                            <v-btn color="primary" @click="revisionId = revision.id; revisionInfoModal = true;">
                                Подробнее <v-icon>mdi-info</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <RevisionModal
            :state="revisionModal"
            v-on:cancel="revisionModal = false"
            v-on:submit="onRevisionCreate"/>
        <RevisionInfoModal
            :id="revisionId"
            :state="revisionInfoModal"
            v-on:cancel="revisionId = null; revisionInfoModal = false;"
        />
    </div>
</template>

<script>
    import axios from 'axios';
    import ACTIONS from "../../store/actions";
    import RevisionModal from "../../components/Modal/RevisionModal";
    import RevisionInfoModal from "../../components/Modal/RevisionInfoModal";

    export default {
        components: {RevisionInfoModal, RevisionModal},
        data: () => ({
            loading: false,
            store_id: null,
            revisionModal: false,
            revisions: [],
            revisionId: null,
            revisionInfoModal: false
        }),
        async created() {
            const { data } = await axios.get('/api/revision');
            this.revisions = data.data;
            this.store_id = this.user.store_id;
        },
        methods: {
            async getFiles() {
                if (this.loading) {
                    return;
                }
                this.loading = true;
                const { data } = await axios.get(`/api/revision/file/get?store_id=${this.store_id}`);
                const link = document.createElement('a');
                link.href = data;
                link.click();
                this.loading = false;
            },
            async onRevisionCreate(e) {
                this.loading = true;
                const data = {
                    user_id: this.user.id,
                    store_id: this.store_id,
                    file: e,
                    is_finished: true
                };

                try {
                    const response = await axios.post('/api/revision', data);
                    this.$toast.success('Ревизия успешно загружена!');
                } catch (e) {
                    this.$toast.error('Что-то пошло не так!');
                }
                this.loading = false;
                this.revisionModal = false;
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
        },
    }
</script>

<style scoped>

</style>
