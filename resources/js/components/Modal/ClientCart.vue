<template>
    <v-dialog v-model="state" persistent max-width="900">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Выберите клиента:</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
<!--                <v-select
                    style="max-width: 270px;"
                    label="Тип лояльности"
                    :items="loyalties"
                    item-value="id"
                    item-text="name"
                    v-model="loyaltyFilter"
                />-->
                <v-checkbox
                    v-model="isWholesaleBuyer"
                    label="Оптовый покупатель"
                />
                <v-row justify="space-between">
                    <v-col>
                        <v-text-field
                            label="Поиск клиента"
                            solo
                            single-line
                            v-model="search"
                            @input="onSearchInput"
                        />
                    </v-col>
                    <v-col>
                        <v-btn text color="success" @click="clientModal = true;">
                            Добавить клиента
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-btn text color="primary" @click="guestSale">
                            Гость
                            <v-icon>mdi-account</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>

                <v-data-table
                    :loading="loading"
                    :headers="headers"
                    :items="clients"
                    class="background-iron-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Введите данные клиента, чтобы начать поиск"
                    :disable-pagination="true"
                    :items-per-page="-1"
                    :hide-default-footer="true"
                >
                    <template v-slot:item.actions="{item}">
                        <v-btn icon color="success" @click="chooseClient(item)">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:item.discount_percent="{item}">
                        <span class="text-center">{{ item.discountPercent }}%</span>
                    </template>
                    <template v-slot:item.is_wholesale_buyer="{item}">
                        <v-icon color="success" v-if="item.is_wholesale_buyer">
                            mdi-check
                        </v-icon>
                        <v-icon color="error" v-else>
                            mdi-close
                        </v-icon>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ClientModal
            :state="clientModal"
            v-on:cancel="clientModal = false"
            v-on:clientCreated="setClient"
        />
    </v-dialog>
</template>

<script>
import ClientModal from './ClientModal';
import ClientRepository from '@/repositories/ClientRepository';
import _ from 'lodash';

export default {
    components: { ClientModal },
    watch: {
        state () {
            this.clients = [];
        }
    },
    data: () => ({
        search: '',
        loading: false,
        loyaltyFilter: -1,
        clientModal: false,
        clients: [],
        headers: [
            {
                text: 'ФИО',
                value: 'client_name'
            },
            {
                text: 'Телефон',
                value: 'client_phone'
            },
            {
                text: 'Номер карты',
                value: 'client_card'
            },
            {
                text: 'Процент скидки',
                value: 'client_discount'
            },
            {
                text: 'Оптовый покупатель',
                value: 'is_wholesale_buyer'
            },
            {
                text: 'Выбрать',
                value: 'actions'
            }
        ],
        isWholesaleBuyer: false,
        clientRepository: ClientRepository,
        queryMap: new Map(),
        meta: {},
    }),
    methods: {
        onSearchInput: _.debounce(function(value) {
            if (value.length >= 4) {
                this.getClient({
                    search: value,
                    per_page: 100
                });
            }
        },500),
        chooseClient(client) {
            this.$emit('onClientChosen', client);
        },
        async getClient (payload) {
            this.loading = true;
            this.clients = [];
            const { data } = await this.clientRepository.get(payload);
            this.clients = data.data;
            this.loading = false;
        },
        guestSale() {
            const client = {
                id: -1,
                client_name: 'Гость',
                sale_sum: 0,
                client_balance: 0,
                client_discount: 0,
                total_sum: 0
            };

            this.$emit('onClientChosen', client);
        },
        setClient(client) {
            this.clientModal = false;
            this.$emit('onClientChosen', client);
        }
    },
    async mounted () {
      /*  const { data } = await this.clientRepository.get();
        this.clients = data.data;
        this.meta = data.meta;*/
    },
    computed: {
        loyalties() {
            return [
                {
                    id: -1,
                    name: 'Все'
                },
                ...this.$store.getters.LOYALTY
            ];
        }
    },
    props: {
        state: {
            type: Boolean,
            default: false
        }
    }
};
</script>

<style scoped>

</style>
