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
                <v-select
                    style="max-width: 270px;"
                    label="Тип лояльности"
                    :items="loyalties"
                    item-value="id"
                    item-text="name"
                    v-model="loyaltyFilter"
                />
                <v-row justify="space-between">
                    <v-col>
                        <v-text-field
                            label="Поиск клиента"
                            solo
                            single-line
                            v-model="search"
                        />
                    </v-col>
                    <v-col>
                        <v-btn text color="success" @click="clientModal = true;">
                            Добавить клиента <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-btn text color="primary" @click="guestSale">
                            Гость <v-icon>mdi-account</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>

                <v-data-table
                    :headers="headers"
                    :items="clients"
                    class="background-tooman-grey fz-18"
                    :search="search"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                    }"
                >
                    <template v-slot:item.actions="{item}">
                        <v-btn icon color="success" @click="chooseClient(item)">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </template>
                    <template v-slot:item.discount_percent="{item}">
                        <span class="text-center">{{ item.discountPercent }}%</span>
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
    import ClientModal from "./ClientModal";
    export default {
        components: {ClientModal},
        data: () => ({
            search: '',
            loyaltyFilter: -1,
            clientModal: false,
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
                    text: 'Выбрать',
                    value: 'actions'
                }
            ]
        }),
        methods: {
            chooseClient(client) {
                this.$emit('onClientChosen', client)
            },
            guestSale() {
                const client = {
                    id: -1,
                    client_name: 'Гость',
                    sale_sum: 0,
                    client_balance: 0,
                    client_discount: 0,
                    total_sum: 0,
                };

                this.$emit('onClientChosen', client)
            },
            setClient(client) {
                this.clientModal = false;
                this.$emit('onClientChosen', client)
            }
        },
        computed: {
            clients() {
                return this.$store.getters.clients.filter(client => {
                    if (this.loyaltyFilter === -1) {
                        return client;
                    }
                    return client.loyalty_id === this.loyaltyFilter;
                });
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
        },
        props: {
            state: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<style scoped>

</style>
