<template>
        <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
            <Header />
            <Drawer />
            <main class="mdl-layout__content mdl-color--grey-100">
                <v-container>
                    <router-view></router-view>
                </v-container>
            </main>
        </div>
</template>

<script>

    import Drawer from "../components/Navigation/Drawer";
    import Header from "../components/Header/Header";
    import ACTIONS from "../store/actions";
    export default {
        components: {
            Drawer, Header
        },
        async mounted() {
            await this.$store.dispatch(ACTIONS.INIT)
        }
    }
</script>

<style scoped>

</style>
