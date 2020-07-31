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
                <v-btn color="primary" @click="getFiles">Получить список товаров</v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data: () => ({
            loading: false,
        }),
        methods: {
            async getFiles() {
                if (this.loading) {
                    return;
                }
                this.loading = true;
                const { data } = await axios.get('/api/revision/file/get');
                const link = document.createElement('a');
                link.href = data;
                link.click();
                this.loading = false;
            }
        },
        computed: {},
    }
</script>

<style scoped>

</style>
