<template>
    <v-overlay :value="isLoading">
        <v-progress-circular
            indeterminate
            size="64"
            color="danger" />
    </v-overlay>
</template>

<script>
    export default {
        data: () => ({}),
        methods: {},
        computed: {
            isLoading() {
                return this.$store.getters.isLoading;
            }
        }
    }
</script>

<style scoped>

</style>
