<template>
    <base-modal
        :state="state"
        :max-width="700"
        persistent
        @cancel="$emit('cancel')"
        title="Продажа услуги"
    >
        <template #default>
            <v-form>
                <v-radio-group v-model="payload.payment_type">
                    <v-radio
                        label="Наличные"
                        :value="1"
                    />
                    <v-radio
                        label="Безналичные"
                        :value="2"
                    />
                </v-radio-group>
                <v-textarea
                    label="Комментарий"
                    v-model="payload.description"
                />
            </v-form>
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">
                Отмена
            </v-btn>
            <v-spacer />
            <v-btn text color="success" @click="onSubmit">
                Сохранить <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>

export default {
    data: () => ({
        payload: {
            description: '',
            payment_type: 1,
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
                description: '',
                payment_type: 1,
            }
        },
    },
}
</script>

<style>
.theme--dark input[type="date"]::-webkit-calendar-picker-indicator {
    background-color: #ccc;
}
</style>
