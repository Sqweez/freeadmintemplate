<template>
    <div>
        <i-card-page title="Ваш рейтинг продаж">
            <div class="my-8 d-flex justify-center" v-if="!loaded">
                <v-progress-circular
                    size="48"
                    color="indigo darken-2"
                    indeterminate
                />
            </div>
            <v-simple-table v-slot:default v-if="loaded">
                <thead>
                    <tr>
                        <th>Категория маржинальности</th>
                        <th>Процент от общих продаж</th>
                        <th>Сумма продаж</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="item of items" :key="item.id">
                    <td>
                        {{ item.title }}
                    </td>
                    <td>
                        {{ item.percent }}%
                    </td>
                    <td>
                        {{ item.total | priceFilters }}
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </i-card-page>
    </div>
</template>

<script>
import axiosClient from '@/utils/axiosClient';
import LoadingSpinner from '@/components/Loaders/LoadingSpinner';
import MatrixLoadingSpinner from '@/components/Loaders/MatrixLoadingSpinner';

export default {
    components: {MatrixLoadingSpinner, LoadingSpinner},
    data: () => ({
        loaded: false,
        items: [],
    }),
    async mounted () {
        try {
            this.loaded = false;
            const { data } = await axiosClient.get('v2/dashboard/my/margins');
            this.items = data.items;
            this.loaded = true;
        } catch (e) {
            console.log(e);
        }
    },
    computed: {},
    methods: {}
}
</script>

<style scoped lang="scss">

</style>
