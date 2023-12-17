<template>
    <v-row>
        <v-col cols="12">
            <v-card>
                <v-card-title style="background-color: #E2B13C; color: #fff;">
                    Бар
                </v-card-title>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                    ></v-data-table>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
export default {
    data: () => ({
        headers: [
            {
                text: '#',
                value: 'id',
            },
            {
                text: 'Название',
                value: 'name',
            },
            {
                text: 'Цена',
                value: 'price'
            },
            {
                text: 'Остаток',
                value: 'quantity'
            },
            {
                text: 'Штрих-код',
                value: 'barcode'
            },
            {
                text: 'Действие',
                value: 'actions'
            }
        ],
    }),
    methods: {},
    computed: {},
};
</script>

<style scoped>

</style>
