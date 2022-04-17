<template>
    <div>
        <v-row>
            <v-col sm="12" lg="12" md="12" v-if="IS_PARTNER_SELLER">
                <DashboardCompanion />
            </v-col>
            <v-col sm="3" lg="3" md="3" v-if="!IS_PARTNER_SELLER">
                <Weather/>
            </v-col>
            <v-col sm="9" lg="9" md="9" v-if="CAN_SALE || IS_OBSERVER || !IS_FRANCHISE">
                <SalesRating/>
            </v-col>
            <v-col sm="12" v-if="CAN_SALE && !IS_FRANCHISE">
                <TasksWidget />
            </v-col>
            <v-col sm="12" lg="12" md="12" v-if="CAN_SALE">
                <PlanWidget/>
            </v-col>
            <v-col sm="12" lg="12" md="12" v-if="CAN_SALE && !IS_FRANCHISE">
                <BrandsWidget/>
            </v-col>
        </v-row>
    </div>

</template>

<script>
    import Weather from "@/components/Widgets/Weather";
    import SalesRating from "@/components/Widgets/SalesRating";
    import PlanWidget from "@/components/Widgets/PlanWidget";
    import {mapGetters} from 'vuex';
    import SalesRatingWidget from "@/components/v2/Widgets/SalesRatingWidget";
    import ACTIONS from "@/store/actions";
    import DashboardCompanion from "@/components/Widgets/DashboardCompanion";
    import TasksWidget from "@/components/Widgets/TasksWidget";
    import BrandsWidget from "@/components/Widgets/BrandsWidget";

    export default {
        data: () => ({
            items: ['Сегодня', 'Текущая неделя', 'Текущий месяц', 'Последние 3 месяца'],
        }),
        components: {
            BrandsWidget,
            TasksWidget,
            DashboardCompanion,
            SalesRatingWidget,
            PlanWidget,
            Weather, SalesRating
        },
        computed: {
            ...mapGetters(['CAN_SALE', 'IS_MALOY', 'IS_PARTNER_SELLER', 'USER']),
            store() {
                return this.$store.getters.stores.find(s => s.id == this.USER.store_id);
            }
        },
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
