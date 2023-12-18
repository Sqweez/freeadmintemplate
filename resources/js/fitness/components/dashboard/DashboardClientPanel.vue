<template>
    <v-row v-bind="$attrs" dense>
        <FItClientModal
            :state="showClientModal"
            @cancel="showClientModal = false; clientId = null;"
            :selected-client="client"
        />
        <FitWriteOffModal
            :state="showWriteOffModal"
            @cancel="showWriteOffModal = false; currentSale = null;"
            @submit="_onWriteOff"
        />
        <v-col cols="12" sm="12" md="9">
            <v-card v-if="!client">
                <v-card-title style="background-color: #5C6BC0; color: white;">
                    Клиент
                </v-card-title>
                <v-card-text>
                    <div class="pt-5 pb-0">
                        <p class="body-1">
                            Для начала работы поднесите карту к устройству
                        </p>
                    </div>
                </v-card-text>
            </v-card>
            <v-card v-if="client">
                <v-card-title style="display: flex; align-items: center; justify-content: space-between; background-color: #5C6BC0; color: white;">
                    <span>
                        Данные о клиенте
                    </span>
                    <div>
                        <v-btn icon color="white" @click="showClientModal = true;">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                        </v-btn>
                        <v-btn icon color="white" @click="$store.commit('setSearchedClient', null)">
                            <v-icon>
                                mdi-close
                            </v-icon>
                        </v-btn>
                    </div>
                </v-card-title>
                <v-card-text>
                    <v-row class="pt-5">
                        <v-col cols="12" md="12" lg="3">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ client.name }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            ФИО клиента
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ client.balance | priceFilters }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Баланс карты
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ client.phone }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Телефон
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ client.birth_date_format }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Дата рождения
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-col>
                        <v-col cols="12" md="12" lg="3">
                            <v-text-field
                                label="Сумма пополнения баланса"
                                type="number"
                                v-model.number="topUp.amount"
                            />
                            <v-radio-group v-model="topUp.type">
                                <v-radio
                                    label="Наличные"
                                    :value="1"
                                />
                                <v-radio
                                    label="Безналичные"
                                    :value="2"
                                />
                            </v-radio-group>
                            <v-textarea
                                rows="1"
                                label="Комментарий"
                                v-model="topUp.description"
                            />
                            <v-btn color="success" depressed @click="_onTopUp">
                                Пополнить <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </v-col>
                        <v-col cols="12" md="12" lg="6">
                            <v-expansion-panels>
                                <v-expansion-panel v-for="sale of client.services" :key="sale.id">
                                    <v-expansion-panel-header color="darken-3" :color="sale.can_be_used ? 'green' : 'red'" style="color: #fff;">
                                        {{ sale.service.name }}
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <div class="d-flex">
                                            <v-list>
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{ sale.valid_until }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>
                                                            Активен до
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{ sale.visits_remaining }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>
                                                            Осталось посещений
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{ sale.purchase_date }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>
                                                            Дата покупки
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{ sale.activation_date }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>
                                                            Дата активации
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                            <div class="ml-12 pt-4">
                                                <ul style="list-style: none; display: flex; flex-direction: column; row-gap: 15px;">
                                                    <li>
                                                        <v-btn
                                                            v-if="sale.can_be_used"
                                                            color="success"
                                                            @click="currentSale = {...sale}; showWriteOffModal = true;"
                                                        >
                                                            Списать <v-icon>mdi-check</v-icon>
                                                        </v-btn>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" sm="12" md="3">
            <dashboard-create-client />
        </v-col>
    </v-row>
</template>

<script>
import FItClientModal from '@/fitness/components/modals/FItClientModal.vue';
import FitWriteOffModal from '@/fitness/components/modals/FitWriteOffModal.vue';
import DashboardCreateClient from '@/fitness/components/dashboard/DashboardCreateClient.vue';

export default {
    components: {DashboardCreateClient, FitWriteOffModal, FItClientModal},
    data: () => ({
        showClientModal: false,
        currentSale: null,
        showWriteOffModal: false,
        clientId: null,
        topUp: {
            amount: 0,
            description: '',
            type: 1,
        }
    }),
    methods: {
        async _onTopUp () {
            if (!this.topUp.amount) {
                return this.$toast.warning('Заполните поле сумма')
            }
            if (!this.topUp.type) {
                return this.$toast.warning('Выберите тип пополнения')
            }
            try {
                this.$loading.enable();
                await this.$store.dispatch('topUp', {
                    ...this.topUp,
                    client_id: this.client.id,
                });
                this.$toast.success('Баланс клиента успешно пополнен');
                this.topUp = {
                    amount: 0,
                    description: '',
                    type: 1,
                };
            } catch (e) {
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        },
        async _onWriteOff (data) {
            const payload = {
                ...data,
                sale_id: this.currentSale.id,
                user_id: this.$store.getters.USER.id,
                client_id: this.currentSale.client_id,
            };
            try {
                this.showWriteOffModal = false;
                this.currentSale = null;
                this.$loading.enable();
                await this.$store.dispatch('createVisit', payload);
                this.$toast.success('Тренировка успешно списана!')
            } catch (e) {
                const message = e?.response?.data?.message;
                if (message) {
                    this.$toast.error(message);
                }
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        },
    },
    beforeDestroy() {
        this.$store.commit('setSearchedClient', null)
    },
    computed: {
        client() {
            return this.$store.getters.searchedClient;
        },
    },
};
</script>

<style scoped>

</style>
