<template>
    <v-card>
        <v-card-title>
            Параметры товаров
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
                <component :is="currentSegment"/>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import CategorySegment from "@/components/Segments/CategorySegment";
    import ManufacturerSegment from "@/components/Segments/ManufacturerSegment";
    import TasteSegment from "@/components/Segments/TasteSegment";
    import AttributeSegment from "@/components/Segments/AttributeSegment";
    import SupplierSegment from "@/components/Segments/SupplierSegment";
    import ACTIONS from "../../store/actions";
    export default {
        data: () => ({
            loading: true,
            segments: [
                {
                    name: 'Категории товаров',
                    component: 'CategorySegment'
                },
                {
                    name: 'Производители',
                    component: 'ManufacturerSegment'
                },
                {
                    name: 'Атрибуты',
                    component: 'AttributeSegment'
                },
                {
                    name: 'Поставщики',
                    component: 'SupplierSegment'
                }
            ],
            currentSegment: 'CategorySegment'
        }),
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_ATTRIBUTES);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            this.loading = false;
        },
        components: {
            CategorySegment, ManufacturerSegment, TasteSegment, AttributeSegment, SupplierSegment
        },
        methods: {
            chooseSegment(segment) {
                this.currentSegment = segment.component;
            }
        }
    }
</script>

<style scoped>

</style>
