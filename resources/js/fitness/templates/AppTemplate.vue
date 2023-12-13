<template>
    <div>
        <v-app-bar app elevation="4">
            <div style="width: 100%; display: flex; align-items: center;">
                <v-text-field
                    placeholder="Поднесите карту или браслет к устройству считывания"
                    prepend-icon="mdi-magnify"
                    hide-details="auto"
                />
                <v-btn icon title="Выйти из приложения" class="ml-4">
                    <v-icon>
                        mdi-exit-to-app
                    </v-icon>
                </v-btn>
            </div>
        </v-app-bar>
        <v-main>
            <v-container fluid>
                <slot></slot>
            </v-container>
        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </div>
</template>

<script>
export default {
    data: () => ({}),
    methods: {},
    computed: {},
};
</script>

<style scoped>

</style>
