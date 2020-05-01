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
                    label="Организация-грузоотправитель"
                    v-model="wayBill.organization"
                />
                <v-text-field
                    label="Грузополучатель"
                    v-model="wayBill.consignee"
                />
                <v-text-field
                    label="Поставщик"
                    v-model="wayBill.supplier"
                />
                <v-text-field
                    label="Плательщик"
                    v-model="wayBill.payer"
                />
                <v-text-field
                    label="Основание"
                    v-model="wayBill.base"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('cancel')">
                    Печать накладной <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            wayBill: {
                organization: 'ИП "Соловьев" БИН 950420350949',
                consignee: '',
                supplier: 'Соловьев Андрей Игоревич, тел. 8705300022',
                payer: '',
                base: ''
            },
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
