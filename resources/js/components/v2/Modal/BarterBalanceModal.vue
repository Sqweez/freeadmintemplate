<template>
    <base-modal title="Пополнение бартерного баланса" :state="state" @cancel="$emit('cancel')">
        <template #default>
            <v-text-field
                label="Сумма пополнения"
                v-model.number="payload.amount"
                type="number"
            />
            <v-text-field
                label="Активно до"
                v-model="payload.active_until"
                type="date"
            />
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">
                Отмена
            </v-btn>
            <v-spacer />
            <v-btn text color="success" @click="$emit('submit', payload)">
                Пополнить <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>
export default {
    props: {
        state: {
            type: Boolean,
            default: false,
        }
    },
    data: () => ({
        payload: {
            amount: 0,
            active_until: null,
        },
    }),
    computed: {},
    methods: {},
    watch: {
        state () {
            this.payload = {
                amount: 0,
                active_until: null
            };
        }
    }
}
</script>

<style scoped lang="scss">

</style>
