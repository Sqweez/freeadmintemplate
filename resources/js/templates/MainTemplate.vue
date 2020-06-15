<template>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
        <Header/>
        <Drawer/>
        <main class="mdl-layout__content mdl-color--grey-100">
            <v-container>
                <router-view></router-view>
            </v-container>
        </main>
    </div>
</template>

<script>
    import "./../scripts/d3.min"
    import "./../scripts/getmdl-select.min"
    import "./../scripts/material.min"
    import "./../scripts/nv.d3.min"
    import "./../scripts/layout/layout.min"
    import "./../scripts/scroll/scroll.min"
    //import "./../scripts/widgets/charts/discreteBarChart.min"
    //import "./../scripts/widgets/charts/linePlusBarChart.min";
    //import "./../scripts/widgets/charts/stackedBarChart.min";
    //import "./../scripts/widgets/employer-form/employer-form.min";
   // import "./../scripts/widgets/line-chart/line-charts-nvd3.min";
    //import "./../scripts/widgets/map/maps.min";
    //import "./../scripts/widgets/pie-chart/pie-charts-nvd3.min";
    //import "./../scripts/widgets/table/table.min";
    //import "./../scripts/widgets/todo/todo.min";
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
