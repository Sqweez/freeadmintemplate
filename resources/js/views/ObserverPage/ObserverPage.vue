<template>
    <div>
        <v-row>
            <v-col sm="3" lg="3" md="3">
                <Weather />
            </v-col>
            <v-col sm="9" lg="9" md="9">
                <SalesRating />
            </v-col>
            <v-col sm="12" lg="12" md="12">
                <PlanWidget />
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import SalesRating from "../../components/Widgets/SalesRating";
    import PlanWidget from "../../components/Widgets/PlanWidget";
    import Weather from "../../components/Widgets/Weather";
    export default {
        data: () => ({}),
        methods: {},
        computed: {},
        components: {
            SalesRating, PlanWidget, Weather
        }
    }
</script>

<style scoped>

</style>
