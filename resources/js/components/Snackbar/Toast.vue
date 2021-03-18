<template>
    <v-snackbar
        v-model="state"
        :bottom="true"
        :color="color"
        :right="true"
        :timeout="duration"
    >
        {{ message }}
        <v-btn
            dark
            text
            @click="state = false"
        >
            Закрыть
        </v-btn>
    </v-snackbar>
</template>

<script>
    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            message: {
                type: String,
                default: ''
            },
            duration: {
                type: Number,
                default: 3000,
            },
            type: {
                type: String,
                default: 'success'
            }
        },
        data: () => ({})
    }
</script>

<style scoped>

</style>
