<template>
    <div>
        <v-card>
            <v-card-title>
                Предзаказы
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
                        style="width: 240px"
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
    </div>
</template>

<script>
    import NewPreorders from "@/views/v3/Preorders/NewPreorders";
    import PreordersList from "@/views/v3/Preorders/PreordersList";

    export default {
        data: () => ({
            loading: false,
            currentSegment: 'NewPreorders',
            hideNotInStock: false,
            segments: [
                {
                    name: 'Новый предзаказ',
                    component: 'NewPreorders'
                },
                {
                    name: 'Список предзаказов',
                    component: 'PreordersList'
                }
            ],
        }),
        components: {
            NewPreorders,
            PreordersList
        },
        methods: {
            chooseSegment(segment) {
                this.currentSegment = segment.component
            }
        },
        computed: {

        }
    }
</script>

<style scoped>

</style>
