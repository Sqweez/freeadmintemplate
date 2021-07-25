<template>
    <div>
        <v-card>
            <v-card-title>
                Настройки смен
            </v-card-title>
            <v-card-text>
                <v-simple-table v-slot:default :dense="false">
                    <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>Оплата за смену</th>
                        <th>Процент от продаж</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(tax) of shiftTaxes">
                        <td>{{ tax.store.name }}</td>
                        <td>
                            <v-text-field
                                label="Смена"
                                v-model.number="tax.shift_tax"
                                type="number"></v-text-field>
                        </td>
                        <td>
                            <v-text-field
                                v-model.number="tax.sale_percent"
                                label="% от продаж"
                                type="number"></v-text-field>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <v-btn color="success" class="mt-5" @click="save">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        data: () => ({}),
        async mounted() {
            await this.$store.dispatch('GET_SHIFT_TAXES');
        },
        methods: {
            async save() {
                const taxes = this.shiftTaxes.map(s => ({
                    shift_tax: s.shift_tax,
                    sale_percent: s.sale_percent,
                    store_id: s.store.id,
                }))

                try {
                    await this.SAVE_SHIFT_TAXES(taxes);
                    this.$toast.success('Настройки успешно обновлены');
                } catch (e) {
                    this.$toast.error('При обновлении произошла ошибка')
                }

            },
            ...mapActions(['SAVE_SHIFT_TAXES'])
        },
        computed: {
            ...mapState({
                shiftTaxes: s => s.shiftModule.shiftTaxes
            }),
        }
    }
</script>

<style scoped>

</style>
