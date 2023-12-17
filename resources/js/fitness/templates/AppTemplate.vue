<template>
    <div>
        <v-app-bar app elevation="4">
            <div style="width: 100%; display: flex; align-items: center;">
                <v-text-field
                    placeholder="ПОДНЕСИТЕ КАРТУ ИЛИ БРАСЛЕТ К УСТРОЙСТВУ СЧИТЫВАНИЯ..."
                    prepend-icon="mdi-magnify"
                    hide-details="auto"
                />
                <v-btn @click="_onLogout" icon title="Выйти из приложения" class="ml-4">
                    <v-icon>
                        mdi-exit-to-app
                    </v-icon>
                </v-btn>
            </div>
        </v-app-bar>
        <v-main>
            <v-container>
                <LoadingSpinner />
                <slot></slot>
            </v-container>
        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </div>
</template>

<script>
import LoadingSpinner from '@/components/Loaders/LoadingSpinner.vue';

export default {
    components: {LoadingSpinner},
    data: () => ({}),
    methods: {
        async _onLogout () {
            await this.$store.dispatch('LOGOUT');
            window.location = '/fit';
        }
    },
    computed: {},
};
</script>

<style scoped>

</style>
