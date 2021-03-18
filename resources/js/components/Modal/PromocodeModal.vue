<template>
    <v-dialog
        persistent
        max-width="600"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Промокод</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field label="Промокод" type="text" v-model="promocode.promocode"></v-text-field>
                <v-autocomplete
                    label="Партнер"
                    :items="partners"
                    item-value="id"
                    item-text="client_name"
                    v-model="promocode.client_id"
                ></v-autocomplete>
                <v-text-field label="Скидка" type="number" v-model="promocode.discount"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', promocode)">
                    {{ editMode ? `Отредактировать` : `Создать` }} <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            promocode: {
                promocode: '',
                client_id: null,
                discount: 0,
            },
        }),
        methods: {},
        computed: {
            partners() {
                return this.$store.getters.PARTNERS;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            editMode: {
                type: Boolean,
                default: false
            },
            _promocode: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(value){
                if (!value) {
                    this.promocode = {
                        promocode: '',
                        client_id: null,
                        discount: null,
                    };
                } else {
                    if (this.editMode) {
                        this.promocode = JSON.parse(JSON.stringify(this._promocode));
                    } else {
                        this.promocode = {
                            promocode: '',
                            client_id: null,
                            discount: null,
                        };
                    }
                }
            },
            promocode: {
                deep: true,
                handler(value) {

                }
            }
        }
    }
</script>

<style scoped>

</style>
