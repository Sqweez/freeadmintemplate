<template>
    <v-dialog persistent max-width="300" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Пополнить баланс партнера</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field label="Сумма пополнения" type="number" v-model="sum"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', sum)">
                    Пополнить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
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
            }
        }
    }
</script>

<style scoped>

</style>
