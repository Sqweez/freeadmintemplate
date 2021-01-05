<template>
    <v-dialog persistent max-width="600" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Новый сертификат</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field label="Штрих-код" type="text" v-model="certificate.barcode"></v-text-field>
                <v-text-field label="Номинал" type="number" v-model.number="certificate.amount"></v-text-field>
                <v-text-field label="Действителен до" type="date" v-model="certificate.expired_at"></v-text-field>
                <p>Оставьте значение пустым, если бессрочно</p>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">
                    Создать <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            certificate: {
                amount: 0,
                barcode: '',
                expired_at: null
            },
        }),
        methods: {
            onSubmit() {
                const certificate = {...this.certificate};
                if (certificate.expired_at === null) {
                    delete certificate.expired_at;
                }
                this.$emit('submit', certificate);
            },
        },
        computed: {
            user() {
                return this.$store.getters.USER;
            }
        },
        watch: {
            state() {
                if (this.state) {
                    this.certificate = {
                        amount: 0,
                        barcode: '',
                        expired_at: null,
                        user_id: this.user.id
                    };
                }
            }
        },
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
