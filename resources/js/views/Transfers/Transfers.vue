<template>
    <v-card>
        <v-card-title>
            Перемещения
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
</template>

<script>
    import TransferHistory from "@/components/Segments/Transfers/TransferHistory";
    import NewTransferSegment from "@/components/Segments/Transfers/NewTransferSegment";
    import CurrentTransfers from "@/components/Segments/Transfers/CurrentTransfers";
    import OnAcceptionTransfers from "@/components/Segments/Transfers/OnAcceptionTransfers";

    export default {
        components: {TransferHistory, NewTransferSegment, CurrentTransfers, OnAcceptionTransfers},
        data: () => ({
            loading: false,
            currentSegment: 'CurrentTransfers'
        }),
        methods: {
            chooseSegment(segment) {
                this.currentSegment = segment.component;
            }
        },
        computed: {
            segments() {
                let segments = [
                    {
                        name: 'Текущие перемещения',
                        component: 'CurrentTransfers'
                    },
                    {
                        name: 'Новое перемещение',
                        component: 'NewTransferSegment'
                    },
                    {
                        name: 'История перемещений',
                        component: 'TransferHistory'
                    },
                ];

                if (this.IS_SUPERUSER) {
                    segments.push({
                        name: 'Требуют подтверждения',
                        component: 'OnAcceptionTransfers'
                    })
                }

                return segments;
            },
            IS_ADMIN() {
                return this.$store.getters.IS_ADMIN;
            }
        }
    }
</script>

<style scoped>

</style>
