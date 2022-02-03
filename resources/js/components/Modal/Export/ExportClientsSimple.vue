<template>
    <v-dialog persistent max-width="900" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Экспорт клиентов</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Поля"
                    multiple
                    :items="fields"
                    v-model="selectedFields"
                    item-value="title"
                    item-text="title"
                />
                <p>Количество клиентов: {{ clients.length }}</p>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <download-excel
                    :data="jsonData"
                    :fields="jsonFields"
                    :exportFields="jsonFields"
                    name="Клиенты.xls"
                    :stringifyLongNum="true"
                    type="xls"
                >
                    <v-btn text color="success">
                        Экспортировать
                    </v-btn>
                </download-excel>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>

export default {
    props: {
        state: {
            type: Boolean,
            default: false,
        },
        clients: {
            type: Array,
            default: () => ([])
        }
    },
    data: () => ({
        fields: [
            {
                value: 'client_name',
                title: 'ФИО',
            },
            {
                value: 'city',
                title: 'Город',
            },
            {
                value: 'phone',
                title: 'Телефон',
            },
            {
                value: 'balance',
                title: 'Баланс',
            },
            {
                value: 'card',
                title: 'Номер карты',
            },
            {
                value: 'discount',
                title: 'Процент скидки',
            },
            {
                value: 'loyalty',
                title: 'Тип лояльности',
            },
            {
                value: 'total_sum',
                title: 'Сумма покупок',
            },
            {
                value: 'last_sale_date',
                title: 'Дата последней покупки',
            },
            {
                value: 'gender_name',
                title: 'Пол'
            },
            {
                value: 'birth_date_formatted',
                title: 'Дата рождения'
            }
        ],
        selectedFields: [
            "ФИО", "Телефон", "Баланс", "Номер карты", "Процент скидки", "Город", "Тип лояльности", "Сумма покупок", "Дата последней покупки", "Пол", "Дата рождения"
        ],
        clientTypeFilter: -1,
        loyaltyFilter: -1,
        pageCount: 1,
        cityFilter: 0,
        jsonMeta: [
            [
                {
                    " key ": " charset ",
                    " value ": " utf- 8 "
                }
            ]
        ],
        clientTypes: [
            {
                id: -1,
                name: 'Все'
            },
            {
                id: 1,
                name: 'Клиент'
            },
            {
                id: 2,
                name: 'Тренер'
            }
        ],
    }),
    methods: {
        async onExport() {

        }
    },
    computed: {
        jsonData() {
            return this.clients.map((client, key) => {
                return {
                    key: key + 1,
                    client_name: client.client_name,
                    city: client.city,
                    phone: client.client_phone,
                    balance: `${new Intl.NumberFormat('ru-RU').format(Math.ceil(client.client_balance))} ₸`,
                    card: client.client_card,
                    discount: client.client_discount,
                    loyalty: client.loyalty.name,
                    total_sum: `${new Intl.NumberFormat('ru-RU').format(Math.ceil(client.total_sum))} ₸`,
                    last_sale_date: client.last_sale_date,
                    gender_name: client.gender_name,
                    birth_date_formatted: client.birth_date_formatted,
                };
            });
        },
        jsonFields() {
            const fields = this.selectedFields.map((field) => {
                return {
                    [field]: this.fields.find(s => s.title === field).value
                };
            })

            const object = {
                '#': 'key'
            };

            for (let i = 0; i < this.selectedFields.length; i++) {
                object[this.selectedFields[i]] = this.fields.find(s => s.title === this.selectedFields[i]).value
            }

            return object;
        },
        loyalties() {
            return [
                {
                    id: -1,
                    name: 'Все'
                },
                ...this.$store.getters.LOYALTY
            ];
        },
        shops() {
            return this.$store.getters.shops;
        },
        cities() {
            return [
                {id: 0, name: 'Все города'},
                {id: -1, name: 'Город не указан'},
                ...this.$store.getters.cities
            ];
        },
    }
}
</script>

<style scoped>

</style>
