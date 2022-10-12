<template>
    <div>
        <v-card>
            <v-card-title>
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
        }
    }
}
</script>

<style scoped lang="scss">

</style>
