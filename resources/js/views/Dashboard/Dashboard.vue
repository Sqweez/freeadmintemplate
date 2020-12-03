<template>
    <div>
        <v-row>
            <v-col sm="3" lg="3" md="3">
                <Weather />
            </v-col>
            <v-col sm="9" lg="9" md="9" v-if="CAN_SALE">
                <SalesRating />
            </v-col>
          <!--  <v-col sm="9" lg="9" md="9" v-if="CAN_SALE">
                <SalesRatingWidget />
            </v-col>-->
            <v-col sm="12" lg="12" md="12" v-if="CAN_SALE">
                <PlanWidget />
            </v-col>
        </v-row>
    </div>

</template>

<script>
    import Weather from "../../components/Widgets/Weather";
    import SalesRating from "../../components/Widgets/SalesRating";
    import PlanWidget from "../../components/Widgets/PlanWidget";
    import {mapGetters} from 'vuex';
    import SalesRatingWidget from "../../components/v2/Widgets/SalesRatingWidget";

    export default {
        data: () => ({
            items: ['Сегодня', 'Текущая неделя', 'Текущий месяц', 'Последние 3 месяца'],
        }),
        components: {
            SalesRatingWidget,
            PlanWidget,
            Weather, SalesRating
        },
        computed: {
            ...mapGetters(['CAN_SALE', 'IS_MALOY'])
        },
        mounted() {
            if (this.IS_MALOY) {
                const main = document.querySelector('.mdl-layout__content');
                main.classList.add('main-maloy')
            }
        }
    }
</script>

<style scoped>
    .text-shadow {
        text-shadow: 4px 4px 4px rgba(0, 0, 0, 0.4);
    }

    iframe {
        width: 100%;
        height: 600px;
    }
</style>
