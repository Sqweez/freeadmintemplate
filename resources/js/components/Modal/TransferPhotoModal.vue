<template>
    <v-dialog persistent max-width="1000" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Фотографии</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div v-for="(photo, key) of photos">
                    <v-img :src="'../../storage/' + photo"></v-img>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            sum: 0,
        }),
        methods: {},
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            photos: {
                type: Array,
                default: () => ([]),
            }
        }
    }
</script>

<style scoped>

</style>
