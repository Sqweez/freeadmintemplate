<template>
    <div>
<!--
        <matrix-loading-spinner :loading="true" />
-->
        <i-card-page title="Товарные матрицы">
            <canvas style="position: absolute; inset: 0;"></canvas>
            <v-btn @click="$router.push('/matrixes/create')" color="success">
                Создать <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-simple-table v-slot:default>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Магазин</th>
                    <th>Количество позиций</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, key) of matrixes">
                    <td>{{ key + 1 }}</td>
                    <td>{{ item.store.name }}</td>
                    <td>{{ item.position_count }}</td>
                    <td>
                        <v-btn icon @click="$router.push(`/matrixes/${item.id}`)" color="primary">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon @click="$router.push(`/matrixes/create?id=${item.id}`)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </i-card-page>
    </div>
</template>

<script>
import MatrixLoadingSpinner from '@/components/Loaders/MatrixLoadingSpinner';
import axiosClient from '@/utils/axiosClient';
export default {
    components: {MatrixLoadingSpinner},
    async mounted () {
        this.$loading.enable();
        const { data: { data } } = await axiosClient.get(`/v2/matrix`);
        this.matrixes = data;
        this.$loading.disable();
    },
    data: () => ({
        matrixes: [],
    }),
    computed: {},
    methods: {}
}
</script>

<style scoped lang="scss">

</style>
