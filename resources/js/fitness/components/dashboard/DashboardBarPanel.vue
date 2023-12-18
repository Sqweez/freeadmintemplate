<template>
    <v-row>
        <v-col cols="12">
            <v-card>
                <v-card-title style="background-color: #E2B13C; color: #fff;">
                    Бар
                </v-card-title>
                <v-card-text v-if="!isReady">
                    <inline-loader />
                </v-card-text>
                <v-card-text v-else>
                    <v-text-field
                        label="Поиск"
                        v-model="search"
                    />
                    <v-data-table
                        :headers="headers"
                        :items="products"
                        :search="search"
                    >
                        <template v-slot:item.ordinal="{item, index}">
                            {{ index + 1 }}
                        </template>
                        <template v-slot:item.price="{item, index}">
                            {{ item.price | priceFilters }}
                        </template>
                        <template v-slot:item.actions="{item, index}">
                            <v-btn
                                v-if="searchedClient"
                                color="success"
                                small
                                @click="_onSale(item)"
                            >
                                Продать
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import InlineLoader from '@/components/Loaders/InlineLoader.vue';
import {__hardcoded} from '@/utils/helpers';

export default {
    components: {InlineLoader},
    data: () => ({
        isReady: false,
        search: '',
        headers: [
            {
                text: '#',
                value: 'ordinal',
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
    async mounted() {
        this.isReady = false;
        await this.$store.dispatch('getProducts');
        this.isReady = true;
    },
    methods: {
        _onSale (item) {
            if (item.price > this.searchedClient.balance) {
                return this.$toast.error('У выбранного клиента недостаточно средств на балансе')
            }
            this.$confirm('Вы действительно хотите продать выбранный товар?')
                .then(async () => {
                    const payload = {
                        id: item.id,
                        client_id: this.searchedClient.id,
                        quantity: __hardcoded(1),
                        price: item.price,
                    };

                    try {
                        this.$loading.enable();
                        await this.$store.dispatch('saleProduct', payload);
                        this.$toast.success('Товар успешно продан');
                    } catch (e) {
                        const message = e?.response?.data?.message;
                        if (message) {
                            this.$toast.error(message);
                        }
                    } finally {
                        this.$loading.disable();
                    }

            }).catch(() => {})
        },
    },
    computed: {
        products () {
            return this.$store.getters.products;
        },
        searchedClient () {
            return this.$store.getters.searchedClient;
        }
    },
};
</script>

<style scoped>

</style>
