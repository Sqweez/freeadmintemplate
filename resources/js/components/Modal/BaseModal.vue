<template>
    <v-dialog
        v-bind="$attrs"
        persistent
        v-model="state"
    >
        <v-card v-if="IS_LOADING_STATE">
            <v-card-text>
                <div
                    class="text-center d-flex align-center justify-center"
                    style="min-height: 651px">
                    <v-progress-circular
                        indeterminate
                        size="65"
                        color="primary"
                    ></v-progress-circular>
                </div>
            </v-card-text>
        </v-card>
        <v-card v-if="!IS_LOADING_STATE">
            <v-card-title class="headline d-flex justify-space-between">
                <span
                    class="white--text">
                    {{ title }}
                </span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit(closeEvent)">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <slot></slot>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <slot name="actions"></slot>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            default: 'Модальное окно'
        },
        closeEvent: {
            type: String,
            default: 'cancel'
        },
        state: {
            type: Boolean,
            default: false,
        }
    },
    data: () => ({}),
    computed: {
        IS_LOADING () {
            return this.$store.getters.isLoading;
        }
    },
    methods: {},
}
</script>

<style scoped lang="scss">

</style>
