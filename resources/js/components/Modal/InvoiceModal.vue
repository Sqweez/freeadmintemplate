<template>
    <v-dialog
        persistent
        v-model="state"
        max-width="1000">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Данные по счет-фактуре</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Договор (контракт) на поставку товаров (работ, услуг):"
                    v-model="contract"
                />
                <v-text-field
                    label="Пункт назначения поставляемых товаров (работ, услуг): "
                    v-model="location"
                />
                <v-text-field
                    label="Товарно-транспортная накладная:"
                    v-model="waybill"
                />
                <v-text-field
                    label="Грузополучатель:"
                    v-model="consignee"
                />
                <v-text-field
                    label="Получатель:"
                    v-model="recipient"
                />
                <v-text-field
                    label="БИН и адрес места нахождения получателя:"
                    v-model="BINLocation"
                />
                <v-text-field
                    label="ИИК получателя:"
                    v-model="IIK"
                />
                <v-text-field
                    label="Наименование товаров, работ услуг:"
                    v-model="product"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', {
                    contract, location, waybill, consignee, recipient, BINLocation, IIK, product
                })">
                    Печать счет-фактуры <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            contract: 'Договор на поставку  "Спортивных витаминов"  ',
            location: '-',
            waybill: '-',
            consignee: '-',
            recipient: '-',
            BINLocation: '-',
            IIK: '-',
            product: 'Спортивные Витамины в ассортименте',
        }),
        methods: {},
        computed: {},
        props: {
            state: {
                type: Boolean,
                required: true,
            }
        },
        watch: {
            state(value) {
                if(!value) {
                    this.contract =  'Договор на поставку  "Спортивных витаминов"';
                    this.location =  '-';
                    this.waybill =  '-';
                    this.consignee =  '-';
                    this.recipient =  '-';
                    this.BINLocation =  '-';
                    this.IIK =  '-';
                    this.product = 'Спортивные Витамины в ассортименте';
                }
            }
        }
    }
</script>

<style scoped>

</style>
