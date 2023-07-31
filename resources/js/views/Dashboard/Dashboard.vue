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
            <v-col sm="12" lg="12" md="12" v-if="CAN_SALE">
                <SellerOwnRatingWidget />
            </v-col>
            <v-col sm="12" v-if="CAN_SALE && !IS_FRANCHISE">
                <TasksWidget />
            </v-col>
            <v-col sm="12" v-if="IS_SUPERUSER">
                <WorkingScheduleWidget />
            </v-col>
            <v-col sm="12" lg="6" md="6" v-if="CAN_SALE">
                <PlanWidget/>
            </v-col>
            <v-col sm="12" lg="12" md="12" v-if="CAN_SALE && !IS_FRANCHISE && false">
                <BrandsWidget/>
            </v-col>
            <v-col sm="12" lg="6" md="6" v-if="IS_BOSS">
                <BrandsRatingWidget />
            </v-col>
            <v-col sm="12" lg="6" md="6" v-if="IS_BOSS">
                <PaymentTypeRating />
            </v-col>
            <v-col sm="12" lg="6" md="6" v-if="IS_BOSS">
                <SellerByMarginTypesRating />
            </v-col>
            <v-col sm="12" lg="6" md="6" v-if="IS_BOSS">
                <ProductSalesRating />
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
import DashboardCompanion from "@/components/Widgets/DashboardCompanion";
import TasksWidget from "@/components/Widgets/TasksWidget";
import BrandsWidget from "@/components/Widgets/BrandsWidget";
import BrandsRatingWidget from '@/components/Widgets/BrandsRatingWidget';
import PaymentTypeRating from '@/components/Widgets/PaymentTypeRating';
import SellerByMarginTypesRating from '@/components/Widgets/SellerByMarginTypesRating';
import ProductSalesRating from '@/components/Widgets/ProductSalesRating';
import SellerOwnRatingWidget from '@/components/Widgets/SellerOwnRatingWidget';
import WorkingScheduleWidget from '@/components/Widgets/WorkingScheduleWidget';

export default {
        data: () => ({
            items: ['Сегодня', 'Текущая неделя', 'Текущий месяц', 'Последние 3 месяца'],
        }),
        components: {
            WorkingScheduleWidget,
            SellerOwnRatingWidget,
            ProductSalesRating,
            SellerByMarginTypesRating,
            PaymentTypeRating,
            BrandsRatingWidget,
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
