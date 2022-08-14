<template>
    <div>
        <v-card>
            <v-card-title>Изъятия из кассы</v-card-title>
            <v-card-text>
                <v-tabs v-model="tab">
                    <v-tab v-for="(tab, idx) of tabs" :key="idx">
                        {{ tab.value }}
                    </v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <with-drawal-create />
                    </v-tab-item>
                    <v-tab-item>
                        <with-drawal-show />
                    </v-tab-item>
                </v-tabs-items>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import WithDrawalCreate from "@/views/WithDrawal/WithDrawalCreate";
import WithDrawalShow from "@/views/WithDrawal/WithDrawalShow";
export default {
    components: {WithDrawalShow, WithDrawalCreate},
    data: () => ({
        tab: 0,
        tabs: [
            {
                value: 'Создание',
                component: 'WithDrawalCreate'
            },
            {
                value: 'Список',
                component: 'WithDrawalShow'
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
