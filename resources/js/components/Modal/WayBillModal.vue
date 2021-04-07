<template>
    <v-dialog
        persistent
        v-model="state"
        max-width="1000">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Данные по накладной</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Организация-получатель"
                    v-model="organization"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', organization)">
                    Печать накладной <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            organization: '',
        }),
        methods: {},
        computed: {},
        props: {
            state: {
                type: Boolean,
                required: true,
            }
        }
    }
</script>

<style scoped>

</style>
