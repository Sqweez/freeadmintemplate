<template>
    <v-row>
        <v-col cols="12">
            <v-card>
                <v-card-title style="background-color: #407A52; color: #fff;">
                    Прайс-лист
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
                text: 'Срок',
                value: 'validity'
            },
            {
                text: 'Количество посещений',
                value: '1233'
            },
            {
                text: 'Оплата',
                value: 'action_pay',
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
