<template>
    <v-app>
        <component :is="layout"></component>
    </v-app>
</template>

<script>
    import MainTemplate from './templates/MainTemplate';
    import SecondaryTemplate from './templates/SecondaryTemplate';
    import "./scripts/d3.min"
    import "./scripts/getmdl-select.min"
    import "./scripts/material.min"
    import "./scripts/nv.d3.min"
    import "./scripts/layout/layout.min"
    import "./scripts/scroll/scroll.min"
    import "./scripts/widgets/charts/discreteBarChart.min"
    import "./scripts/widgets/charts/linePlusBarChart.min";
    import "./scripts/widgets/charts/stackedBarChart.min";
    import "./scripts/widgets/employer-form/employer-form.min";
    import "./scripts/widgets/line-chart/line-charts-nvd3.min";
    import "./scripts/widgets/map/maps.min";
    import "./scripts/widgets/pie-chart/pie-charts-nvd3.min";
    import "./scripts/widgets/table/table.min";
    import "./scripts/widgets/todo/todo.min";
    export default {
        components: {
            MainTemplate, SecondaryTemplate
        },
        data: () => ({
            layout: 'MainTemplate'
        }),
    }
</script>

<style scoped>
    @import "./css/application.min.css";
</style>
