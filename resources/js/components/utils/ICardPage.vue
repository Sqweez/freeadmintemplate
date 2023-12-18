<template>
    <div>
        <v-card>
            <v-card-title :style="[
                color ? {backgroundColor: color} : {},
                color ? {color: '#fff'} : {},
            ]">
                {{ title }}
            </v-card-title>
            <v-card-text>
                <slot />
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => ({}),
    computed: {},
    methods: {},
    props: {
        title: {
            type: String,
            default: 'Страница'
        },
        color: {
            type: String,
            default: null,
        }
    }
}
</script>

<style scoped lang="scss">

</style>
