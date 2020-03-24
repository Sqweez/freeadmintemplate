<template>
    <div>
        <v-row>
            <v-col sm="12" lg="6" md="6">
                <Weather />
            </v-col>
            <v-col sm="12" lg="6" md="6">
                <SalesRating />
            </v-col>
        </v-row>


    </div>

</template>

<script>
    import Weather from "../../components/Widgets/Weather";
    import SalesRating from "../../components/Widgets/SalesRating";
    export default {
        data: () => ({
            items: ['Сегодня', 'Текущая неделя', 'Текущий месяц', 'Последние 3 месяца'],
        }),
        components: {
            Weather, SalesRating
        }
    }
</script>

<style scoped>
    .text-shadow {
        text-shadow: 4px 4px 4px rgba(0, 0, 0, 0.4);
    }
</style>
