<template>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
        <Header />
        <Drawer />
        <main class="mdl-layout__content">
            <v-container>
                <LoadingSpinner />
                <router-view />
            </v-container>
        </main>
    </div>
</template>

<script>
import './../scripts/d3.min';
import './../scripts/getmdl-select.min';
import './../scripts/material.min';
import './../scripts/nv.d3.min';
import './../scripts/layout/layout.min';
import './../scripts/scroll/scroll.min';

import Drawer from '../components/Navigation/Drawer';
import Header from '../components/Header/Header';
import LoadingSpinner from '../components/Loaders/LoadingSpinner';
import WorkShiftRepository from '@/repositories/WorkShiftRepository';

export default {
    components: {
        LoadingSpinner,
        Drawer, Header
    },
    data: () => ({
        workShiftRepository: WorkShiftRepository,
        pageReady: false,
    }),
    async created() {
        await this.$store.dispatch('INIT');
    },
    async mounted () {
        console.log(this.$user);

    },
};
</script>

<style scoped>

</style>
