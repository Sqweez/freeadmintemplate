<template>
    <base-modal
        :state="state"
        :max-width="700"
        persistent
        @cancel="$emit('cancel')"
        title="Списание тренировки"
    >
        <template #default>
            <v-form>
                <v-select
                    label="Тренер"
                    :items="trainers"
                    item-text="name"
                    item-value="id"
                />
            </v-form>
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">
                Отмена
            </v-btn>
            <v-spacer />
            <v-btn text color="success" @click="onSubmit">
                Списать <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>

export default {
    data: () => ({
        payload: {
            trainer_id: null,
        },
        loading: false,
    }),
    mounted() {

    },
    methods: {
        async onSubmit() {
            this.$emit('submit', this.payload)
        },
    },
    props: {
        state: {
            type: Boolean,
            default: false
        },
    },
    watch: {
        state(value) {
            this.payload = {
                trainer_id: null,
            }
        },
    },
    computed: {
        trainers () {
            return this.$store.getters.trainers;
        }
    }
}
</script>

<style>
.theme--dark input[type="date"]::-webkit-calendar-picker-indicator {
    background-color: #ccc;
}
</style>
