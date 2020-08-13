<template>
    <v-card>
        <v-card-title>
            Поступления
        </v-card-title>
        <v-card-text>
            <div
                class="text-center d-flex align-center justify-center"
                style="min-height: 651px"
                v-if="loading">
                <v-progress-circular
                    indeterminate
                    size="65"
                    color="primary"
                ></v-progress-circular>
            </div>
            <div v-if="!loading">
                <v-btn
                    v-for="(segment, index) of segments"
                    :key="index"
                    :text="currentSegment !== segment.component"
                    style="width: 200px"
                    class="mr-3"
                    @click="chooseSegment(segment)"
                    color="primary">
                    {{ segment.name }}
                </v-btn>
                <component
                    class="mt-5"
                    :is="currentSegment"/>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import ArrivalHistory from "../../components/Segments/ArrivalHistory";
    import CurrentArrivals from "../../components/Segments/CurrentArrivals";
    import NewArrival from "../../components/Segments/NewArrival";

    export default {
        components: {
            ArrivalHistory, CurrentArrivals, NewArrival
        },
        data: () => ({
            loading: false,
            segments: [
                {
                    name: 'Текущие поступления',
                    component: 'CurrentArrivals'
                },
                {
                    name: 'Новое поступление',
                    component: 'NewArrival'
                },
                {
                    name: 'История поступлений',
                    component: 'ArrivalHistory'
                },
            ],
            currentSegment: 'CurrentArrivals'
        }),
        methods: {
            chooseSegment(segment) {
                this.currentSegment = segment.component;
            }
        },
        computed: {}
    }
</script>

<style scoped>

</style>
