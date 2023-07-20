<template>
    <v-dialog
        persistent
        v-model="state"
        max-width="1000">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Данные по счету на оплату</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Юридическое лицо"
                    v-model="entityId"
                    :items="entities"
                    item-text="name"
                    item-value="id"
                />
                <v-text-field
                    label="Покупатель"
                    v-model="customer"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', {customer, entityId})">
                    Печать накладной <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            customer: '',
            entityId: null,
        }),
        methods: {},
        computed: {
            entities () {
                return this.$store.getters.legal_entities;
            },
        },
        props: {
            state: {
                type: Boolean,
                required: true,
            }
        },
        watch: {
            state(val) {
                if (!val) {
                    this.customer = '';
                }
            }
        }
    }
</script>

<style scoped>

</style>
