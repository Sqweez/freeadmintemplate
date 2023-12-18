<template>
    <div>
        <v-app-bar app elevation="4">
            <dashboard-search-bar />
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
import DashboardSearchBar from '@/fitness/components/dashboard/DashboardSearchBar.vue';

export default {
    components: {DashboardSearchBar, LoadingSpinner},
    async mounted() {
        await this._getUsers();
    },
    methods: {
        async _getUsers () {
            await this.$store.dispatch('getUsers');
        }
    }
};
</script>

<style scoped>

</style>
