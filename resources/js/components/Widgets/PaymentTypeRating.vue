<template>
    <div>
        <i-card-page title="Рейтинг по способам оплаты">
            <v-responsive
                :min-height="200"
                v-if="loading"
                class="text-center d-flex justify-center align-center">
                <v-progress-circular
                    :size="50"
                    color="primary"
                    indeterminate
                ></v-progress-circular>
            </v-responsive>
            <div v-else>
                <i-date-picker v-model="dates"/>
                <v-select
                    :items="payment_types"
                    label="Способ оплаты:"
                    v-model="paymentType"
                    item-value="id"
                    item-text="name">
                </v-select>
                <v-btn @click="onSubmit" color="success" :disabled="!(Array.isArray(dates) && dates.length === 2)" block>
                    Получить рейтинг
                </v-btn>
                <v-simple-table v-slot:default v-if="sellers">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Продавец</th>
                        <th>Сумма</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(seller, idx) of sellers" :key="seller.user_id">
                        <td>{{ idx + 1 }}</td>
                        <td>{{ seller.user }} ({{ seller.store }})</td>
                        <td>{{ seller.total | priceFilters }}</td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </div>
        </i-card-page>
    </div>

</template>

<script>
import IDatePicker from '@/components/DatePicker/DatePicker';
import axiosClient from '@/utils/axiosClient';
export default {
    components: {
        IDatePicker
    },
    data: () => ({
        dates: [],
        loading: false,
        sellers: null,
        paymentType: -1,
    }),
    computed: {
        payment_types() {
            return [{id: -1, name: 'Все'}, ...this.$store.getters.payment_types];
        },
    },
    methods: {
        async onSubmit () {
            try {
                this.loading = true;
                this.brands = null;
                const payload = new URLSearchParams({
                    start: this.dates[0],
                    finish: this.dates[1],
                    payment_type: this.paymentType,
                }).toString();
                const { data } = await axiosClient.get(`/v2/reports/payment-types?${payload}`);
                this.sellers = data;
            } catch (e) {
                this.$toast.error('Произошла ошибка!')
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped lang="scss">

</style>
