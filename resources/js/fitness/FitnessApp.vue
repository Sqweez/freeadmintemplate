<template>
    <v-app style="background-color: #e9e9e9;">
        <div v-if="!loginChecked" class="fullscreen-overlay">
            <v-progress-circular indeterminate color="red" size="60"></v-progress-circular>
        </div>
        <div v-if="isLoggedIn">
            <AppTemplate>
                <router-view></router-view>
            </AppTemplate>
        </div>
        <div v-else>
            <EmptyTemplate>
                <router-view></router-view>
            </EmptyTemplate>
        </div>
    </v-app>
</template>

<script>
import AppTemplate from '@/fitness/templates/AppTemplate.vue';
import EmptyTemplate from '@/fitness/templates/EmptyTemplate.vue';

export default {
    components: {EmptyTemplate, AppTemplate},
    data: () => ({}),
    methods: {},
    computed: {
        isLoggedIn() {
            return this.$store.getters.LOGGED_IN;
        },
        loginChecked() {
            return this.$store.getters.LOGIN_CHECKED;
        },
    },
};
</script>

<style scoped>
.fullscreen-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #000;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
}
</style>
