<template>
    <v-row>
        <FitPriceModal
            @cancel="showServiceModal = false; serviceId = null;"
            :state="showServiceModal"
            :id="serviceId"
        />
        <FitServiceSaleModal
            :state="showServiceSaleModal"
            @cancel="showServiceSaleModal = false; currentService = null;"
            @submit="_onServiceSale"
        />
        <v-col cols="12">
            <v-card>
                <v-card-title style="background-color: #407A52; color: #fff;">
                    Прайс-лист
                </v-card-title>
                <v-card-text v-if="!isReady">
                    <inline-loader />
                </v-card-text>
                <v-card-text v-else>
                    <v-btn class="my-4" color="success" depressed @click="showServiceModal = true;">
                        Добавить прайс
                    </v-btn>
                    <v-data-table
                        :headers="headers"
                        :items="services"
                    >
                        <template v-slot:item.price="{item}">
                            <span>
                                {{ item.price | priceFilters }}
                            </span>
                        </template>
                        <template v-slot:item.action_pay="{ item }">
                            <div class="py-2" v-if="searchedClient">
                                <div>
                                    <v-btn color="success" small block @click="_toggleSale(item)">
                                        Продать
                                    </v-btn>
                                </div>
                            </div>
                            <div class="py-2" v-else>
                                <span>
                                    Выберите клиента...
                                </span>
                            </div>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn icon @click="serviceId = item.id; showServiceModal = true;">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon>
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import FitPriceModal from '@/fitness/components/modals/FitPriceModal.vue';
import InlineLoader from '@/components/Loaders/InlineLoader.vue';
import FitServiceSaleModal from '@/fitness/components/modals/FitServiceSaleModal.vue';

export default {
    components: {FitServiceSaleModal, InlineLoader, FitPriceModal},
    data: () => ({
        showServiceSaleModal: false,
        isReady: false,
        serviceId: null,
        currentService: null,
        showServiceModal: false,
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
                value: 'validity_in_days_text'
            },
            {
                text: 'Количество посещений',
                value: 'visits_count_text'
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
    methods: {
        async _getServices () {
            await this.$store.dispatch('getServices');
        },
        _toggleSale (item) {
            if (this.searchedClient.balance < item.price) {
                return this.$toast.error('У выбранного клиента недостаточно средств на счету')
            }
            this.currentService = {...item}; this.showServiceSaleModal = true;
        },
        async _onServiceSale (event) {
            this.showServiceSaleModal = false;
            const payload = {
                ...event,
                service_id: this.currentService.id,
                client_id: this.searchedClient.id,
                price: this.currentService.price,
                visits_count: this.currentService.visits_count,
                gym_id: this.$store.getters.USER.gym_id,
                user_id: this.$store.getters.USER.id,
                name: this.currentService.name,
                validity_in_days: this.currentService.validity_in_days,
            };

            try {
                this.$loading.enable('Продаем услугу...');
                await this.$store.dispatch('createServiceSale', payload);
            } catch (e) {
                this.$toast.error('Во время продажи что-то пошло не так')
            } finally {
                this.$loading.disable();
            }
        },
    },
    async mounted() {
        this.isReady = false;
        await this._getServices();
        this.isReady = true;
    },
    computed: {
        services () {
            return this.$store.getters.services;
        },
        searchedClient () {
            return this.$store.getters.searchedClient;
        }
    },
};
</script>

<style scoped>

</style>
