<template>
    <div>
        <v-card>
            <v-card-title>
                {{ title }}
            </v-card-title>
            <v-card-text>
                <slot />
            </v-card-text>
            <v-card-actions>
                <slot name="actions"></slot>
            </v-card-actions>
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
