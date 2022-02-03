<template>
    <div>
        <v-card>
            <v-card-title>
                Клиенты без покупок
            </v-card-title>
            <v-card-text>
                <v-menu
                    ref="startMenu"
                    v-model="startMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="start"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="start"
                            label="Дата начала"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="start"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="startMenu = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate(startMenu, start)"
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
                <v-menu
                    ref="finishMenu"
                    v-model="finishMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="finish"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="finish"
                            label="Дата окончания"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="finish"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="finishMenu = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate(finishMenu, finish) "
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
                <v-btn color="primary" @click="loadReport">
                    Получить отчет
                </v-btn>
                <v-btn color="primary" @click="exportModal = true">
                    Экспорт клиентов
                </v-btn>
                <div class="d-flex mt-2 align-center">
                    <v-select
                        style="max-width: 270px; margin-right: 20px;"
                        label="Тип лояльности"
                        :items="loyalties"
                        item-value="id"
                        item-text="name"
                        v-model="loyaltyFilter"
                    />
                    <v-select
                        style="max-width: 270px; margin-right: 20px;"
                        label="Тип клиента"
                        :items="clientTypes"
                        item-value="id"
                        item-text="name"
                        v-model="clientTypeFilter"
                    />
                    <v-select
                        style="max-width: 270px; margin-right: 20px;"
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
                </div>
                <v-row>
                    <v-col>
                        <h6>Количество клиентов: {{ clients.length }}</h6>
                        <v-text-field
                            class="mt-2"
                            v-model="search"
                            solo
                            clearable
                            label="Поиск клиента"
                            single-line
                            hide-details
                        ></v-text-field>
                        <v-data-table
                            loading-text="Идет загрузка клиентов"
                            :search="search"
                            no-results-text="Нет результатов"
                            no-data-text="Нет данных"
                            :headers="headers"
                            :page.sync="pagination.page"
                            :items="clients"
                            @page-count="pageCount = $event"
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
                                    <v-list-item>
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
                                </v-list>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-btn icon @click="$router.push(`/clients/${item.id}`)">
                                    <v-icon>
                                        mdi-eye
                                    </v-icon>
                                </v-btn>
                                <v-btn icon @click="sendWhatsapp(item)" color="success">
                                    <v-icon>mdi-whatsapp</v-icon>
                                </v-btn>
                            </template>
                            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                            </template>
                        </v-data-table>
                        <div class="text-xs-center pt-2">
                            <v-pagination
                                v-model="pagination.page"
                                :total-visible="10"
                                :length="pageCount"></v-pagination>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <ExportClientsSimple :state="exportModal" :clients="clients" @cancel="exportModal = false"/>
    </div>
</template>

<script>
import axios from 'axios';
import MUTATIONS from '@/store/mutations';
import ExportClientsSimple from "@/components/Modal/Export/ExportClientsSimple";
import GENDERS from "@/common/enums/genders";

export default {
    components: {ExportClientsSimple},
    data: () => ({
        exportModal: false,
        start: null,
        startMenu: null,
        finish: null,
        finishMenu: null,
        search: '',
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
                value: 'last_sale_date',
                text: 'Дата последней покупки',
                sortable: true,
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
                text: 'Доп информация'
            },
/*            {
                value: 'is_partner',
                text: 'Тренер'
            },
            {
                value: 'city',
                text: 'Город'
            },
            {
                value: 'loyalty.name',
                text: 'Тип лояльности'
            },*/
            {
                value: 'actions',
                text: 'Действие'
            }
        ],
        pagination: {
            ascending: true,
            rowsPerPage: 10,
            page: 1
        },
        genderId: -1,
        genders: [
            {
                id: -1,
                value: 'Все'
            },
            ...GENDERS
        ],
        clientTypeFilter: -1,
        loyaltyFilter: -1,
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
        cityFilter: 0
    }),
    computed: {
        clients() {
            return this.$store.getters.CLIENTS_WITHOUT_SALES
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
        cities() {
            return [
                {id: 0, name: 'Все города'},
                {id: -1, name: 'Город не указан'},
                ...this.$store.getters.cities
            ];
        },
    },
    methods: {
        async changeCustomDate() {
            this.$refs.startMenu.save(this.start);
            this.$refs.finishMenu.save(this.finish);
        },
        async loadReport() {
            if (!(this.start && this.finish)) {
                return this.$toast.error('Введите обе даты');
            }
            this.$loading.enable();
            const {data} = await axios.get(`/api/clients/analytics/sales?start=${this.start}&finish=${this.finish}`);
            this.$store.commit(MUTATIONS.SET_CLIENTS_WITHOUT_SALES, data.data);
            this.$loading.disable();
        },
        sendWhatsapp(client) {
            const message = '';
            window.location.href = `https://api.whatsapp.com/send?phone=${client.client_phone}&text=${message}`;
        },
    }
}
</script>

<style scoped lang="scss">

</style>
