<template>
    <div>
        <v-container>
            <dashboard-stats-panel />
            <dashboard-stats class="mt-8" />
            <dashboard-client-panel class="mt-8" />
            <dashboard-price-panel class="mt-8" />
            <dashboard-bar-panel class="mt-8" />
            <dashboard-user-panel
                v-if="$store.getters.IS_BOSS"
                class="mt-8"
            />
        </v-container>
    </div>
</template>

<script>
import DashboardStats from '@/fitness/components/dashboard/DashboardStats.vue';
import DashboardClientPanel from '@/fitness/components/dashboard/DashboardClientPanel.vue';
import DashboardPricePanel from '@/fitness/components/dashboard/DashboardPricePanel.vue';
import DashboardBarPanel from '@/fitness/components/dashboard/DashboardBarPanel.vue';
import DashboardUserPanel from '@/fitness/components/dashboard/DashboardUserPanel.vue';
import DashboardStatsPanel from '@/fitness/components/dashboard/DashboardStatsPanel.vue';

export default {
    components: {
        DashboardStatsPanel,
        DashboardUserPanel, DashboardBarPanel, DashboardPricePanel, DashboardClientPanel, DashboardStats},
    data: () => ({}),
    methods: {},
    computed: {},
};
</script>

<style scoped>

</style>
