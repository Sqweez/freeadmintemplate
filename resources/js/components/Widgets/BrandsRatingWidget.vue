<template>
    <div>
        <i-card-page title="Рейтинг брендов">
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
                <v-select label="Магазин" v-model="store" :items="$storeFilters" item-value="id" item-text="name"></v-select>
                <v-btn @click="onSubmit" color="success" :disabled="!(Array.isArray(dates) && dates.length === 2)" block>
                    Получить рейтинг
                </v-btn>
                <v-simple-table v-slot:default v-if="brands">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Наименование</th>
                        <th>Сумма</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(brand, idx) of brands" :key="brand.manufacturer_id">
                        <td>{{ idx + 1 }}</td>
                        <td>{{ brand.manufacturer }}</td>
                        <td>{{ brand.total | priceFilters }}</td>
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
        store: -1,
        loading: false,
        brands: null,
    }),
    computed: {},
    methods: {
        async onSubmit () {
            try {
                this.loading = true;
                this.brands = null;
                const payload = new URLSearchParams({
                    start: this.dates[0],
                    finish: this.dates[1],
                    store_id: this.store,
                }).toString();
                const { data } = await axiosClient.get(`/v2/reports/brands?${payload}`);
                this.brands = data;
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
