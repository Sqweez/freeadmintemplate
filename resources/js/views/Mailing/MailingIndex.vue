<template>
    <div>
        <i-card-page title="Рассылка">
            <v-btn color="success">
                Создать шаблон
            </v-btn>
            <v-btn :disabled="!selectedClients.length" color="success" @click="showMailingModal = true;">
                Начать рассылку
            </v-btn>
            <v-row>
                <v-col>
                    <v-select
                        style="max-width: 270px;"
                        label="Тип лояльности"
                        :items="loyalties"
                        item-value="id"
                        item-text="name"
                        v-model="loyaltyFilter"
                    />
                    <v-select
                        style="max-width: 270px;"
                        label="Тип клиента"
                        :items="clientTypes"
                        item-value="id"
                        item-text="name"
                        v-model="clientTypeFilter"
                    />
                </v-col>
                <v-col>
                    <v-select
                        style="max-width: 270px;"
                        label="Города"
                        :items="cities"
                        item-value="id"
                        item-text="name"
                        v-model="cityFilter"
                    />
                    <v-select
                        style="max-width: 270px;"
                        label="Пол"
                        :items="genders"
                        item-value="id"
                        item-text="value"
                        v-model="genderId"
                    />
                </v-col>
                <v-col>
                    <v-checkbox
                        label="Без карт"
                        v-model="withoutBarcode"
                    />
                    <v-checkbox
                        label="Оптовик"
                        v-model="isWholesaleBuyer"
                    />
                    <v-checkbox
                        label="Каспи клиент"
                        v-model="isKaspiClient"
                    />
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-text-field
                        class="mt-2"
                        v-model="search"
                        clearable
                        label="Поиск клиента"
                        single-line
                        solo
                    />
                    <v-btn depressed small color="error" @click="selectClients">
                        Выбрать всех текущих <v-icon>mdi-check</v-icon>
                    </v-btn>
                    <v-data-table
                        v-model="selectedClients"
                        show-select
                        :loading="IS_LOADING_STATE"
                        loading-text="Идет загрузка клиентов"
                        :search="search"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :headers="headers"
                        :items="clients"
                        :items-per-page="10"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                        <template v-slot:item.client_balance="{item}">
                            {{ item.client_balance }} ₸
                        </template>
                        <template v-slot:item.client_discount="{item}">
                            {{ item.client_discount }}%
                        </template>
                        <template v-slot:item.is_partner="{item}">
                            <v-icon :color="item.is_partner ? 'success' : 'error'">
                                {{ item.is_partner ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </template>
                        <template v-slot:item.extra="{item}">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.gender_name }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Пол
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.city }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Город
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item v-if="item.is_partner">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon :color="item.is_partner ? 'success' : 'error'">
                                                {{ item.is_partner ? 'mdi-check' : 'mdi-close' }}
                                            </v-icon>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Партнер
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item v-if="item.is_kaspi">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon :color="item.is_kaspi ? 'success' : 'error'">
                                                {{ item.is_kaspi ? 'mdi-check' : 'mdi-close' }}
                                            </v-icon>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Каспи-клиент
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item v-if="item.is_wholesale_buyer">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon :color="item.is_wholesale_buyer ? 'success' : 'error'">
                                                {{ item.is_wholesale_buyer ? 'mdi-check' : 'mdi-close' }}
                                            </v-icon>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Оптовый покупатель
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.birth_date_formatted }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Дата рождения
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.loyalty.name }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Тип лояльности
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.total_sum | priceFilters }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Сумма покупок за все время
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.current_month_sum | priceFilters }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Сумма покупок за текущий месяц
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item v-if="item.loyalty_id == 2">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.until_platinum | priceFilters }} | {{ item.until_platinum_percent }}%
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Платиновый остаток
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.last_mailing_date }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Последняя рассылка
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </template>
                        <template v-slot:item.actions="{ item }" v-if="IS_MARKETOLOG">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-btn icon @click="$router.push(`/clients/${item.id}`)">
                                                <v-icon>
                                                    mdi-eye
                                                </v-icon>
                                            </v-btn>
                                        </v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-btn icon @click="() => {}" color="success">
                                                <v-icon>mdi-whatsapp</v-icon>
                                            </v-btn>
                                        </v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </template>
                        <template v-slot:item.actions="{ item }" v-else>

                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </i-card-page>
        <mailing-modal
            :state="showMailingModal"
            :clients="selectedClients"
            @close="showMailingModal = false;"
        />
    </div>
</template>

<script>
import GENDERS from '@/common/enums/genders';
import ACTIONS from '@/store/actions';
import MailingModal from '@/components/Modal/MailingModal';

export default {
    components: {MailingModal},
    data: () => ({
        showMailingModal: false,
        headers: [
            {
                value: 'client_name',
                text: 'ФИО',
                sortable: false
            },
            {
                value: 'client_phone',
                text: 'Телефон',
                sortable: false,
            },
            {
                value: 'client_balance',
                text: 'Баланс'
            },
            {
                value: 'client_card',
                text: 'Номер карты'
            },
            {
                value: 'client_discount',
                text: 'Процент скидки'
            },
            {
                value: 'extra',
                text: 'Доп. информация'
            },
           /* {
                value: 'actions',
                text: 'Действие'
            }*/
        ],
        search: '',
        clientTypeFilter: -1,
        loyaltyFilter: -1,
        cityFilter: 0,
        genderId: -1,
        genders: [
            {
                id: -1,
                value: 'Все'
            },
            ...GENDERS
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
        withoutBarcode: false,
        isWholesaleBuyer: false,
        isKaspiClient: false,
        selectedClients: [],
    }),
    async created() {
        await this.$store.dispatch(ACTIONS.GET_CLIENTS);
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
        },
        cities() {
            return [
                {id: 0, name: 'Все города'},
                {id: -1, name: 'Город не указан'},
                ...this.$store.getters.cities
            ];
        },
        clients() {
            return this.$store.getters.clients
                .filter(client => {
                    if (this.cityFilter === 0) {
                        return client;
                    }
                    return +client.client_city === this.cityFilter
                }).filter(client => {
                    if (this.loyaltyFilter === -1) {
                        return client;
                    }
                    return client.loyalty.id === this.loyaltyFilter;
                }).filter(client => {
                    if (this.clientTypeFilter === -1) {
                        return client;
                    }
                    if (this.clientTypeFilter === 1) {
                        return !client.is_partner;
                    }
                    return client.is_partner;
                }).filter(c => {
                    if (this.genderId === -1) {
                        return true;
                    } else {
                        return c.gender === this.genderId;
                    }
                }).filter(c => {
                    if (!this.withoutBarcode) {
                        return true;
                    }
                    return c.client_card.length === 0 || c.client_card.length < 5;
                }).filter(c => {
                    if (!this.isWholesaleBuyer) {
                        return true;
                    }
                    return c.is_wholesale_buyer;
                }).filter(c => {
                    return !this.isKaspiClient ? true : c.is_kaspi;
                })
        },
    },
    methods: {
        selectClients () {
            this.selectedClients = [...this.clients];
        }
    }
}
</script>

<style scoped lang="scss">

</style>
