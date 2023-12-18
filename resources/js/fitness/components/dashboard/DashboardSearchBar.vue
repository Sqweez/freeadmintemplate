<template>
    <div style="width: 100%; display: flex; align-items: center;">
        <v-btn v-if="canGoBack" @click="$router.back()" icon title="Назад" class="mr-4">
            <v-icon>
                mdi-arrow-left
            </v-icon>
        </v-btn>
        <v-text-field
            placeholder="ПОДНЕСИТЕ КАРТУ ИЛИ БРАСЛЕТ К УСТРОЙСТВУ СЧИТЫВАНИЯ..."
            prepend-icon="mdi-magnify"
            hide-details="auto"
            v-model.trim="pass"
            @keydown.enter="_onSearch"
        />
        <div v-if="client" class="mx-4">
            <b>Текущий клиент: {{ client.name }}</b>
        </div>
        <v-btn @click="_onLogout" icon title="Выйти из приложения" class="ml-4">
            <v-icon>
                mdi-exit-to-app
            </v-icon>
        </v-btn>
    </div>
</template>

<script>
export default {
    data: () => ({
        pass: '',
    }),
    methods: {
        canGoBack() {
            return this.$router.history.length > 1;
        },
        async _onLogout () {
            await this.$store.dispatch('LOGOUT');
            window.location = '/fit';
        },
        async _onSearch () {
            if (!this.pass) {
                return this.$toast.warning('Введите данные карты клиента');
            }
            try {
                this.$loading.enable('Идет поиск клиента...');
                const payload = {
                    type: 'pass',
                    value: this.pass
                };
                await this.$store.dispatch('searchClient', payload);
                this.pass = '';
                if (this.$route.path !== '/') {
                    await this.$router.push('/');
                }
            } catch (e) {
                console.log(e);
                //this.$toast.error(e.response.data.message)
            } finally {
                this.$loading.disable();
            }
        },
    },
    computed: {
        client () {
            return this.$store.getters.searchedClient;
        },
    },
};
</script>

<style scoped>

</style>
