<template>
    <div>
        <v-card>
            <v-card-title>Сроки годности</v-card-title>
            <v-card-text>
                <v-tabs v-model="tab">
                    <v-tab v-for="(tab, idx) of tabs" :key="idx">
                        {{ tab.value }}
                    </v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <best-before-create />
                    </v-tab-item>
                    <v-tab-item>
                       <best-before-show />
                    </v-tab-item>
                </v-tabs-items>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import BestBeforeCreate from "@/views/BestBefore/BestBeforeCreate";
import BestBeforeShow from "@/views/BestBefore/BestBeforeShow";
export default {
    components: {BestBeforeCreate, BestBeforeShow},
    data: () => ({
        tab: 0,
        tabs: [
            {
                value: 'Создание',
                component: 'BestBeforeCreate'
            },
            {
                value: 'Истекающие',
                component: 'BestBeforeShow'
            }
        ]
    }),
    computed: {
        component () {
            return this.tabs[this.tab].component;
        }
    },
    methods: {}
}
</script>

<style scoped lang="scss">

</style>
