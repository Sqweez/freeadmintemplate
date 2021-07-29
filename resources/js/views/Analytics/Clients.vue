<template>
    <div>
        <v-card>
            <v-card-title>
                Статистика по клиентам
            </v-card-title>
            <v-card-text>
                <v-col>
                    <label>Произвольная дата</label>
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
                                outline
                                color="primary"
                                @click="finishMenu = false"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                text
                                outline
                                color="primary"
                                @click="changeCustomDate(finishMenu, finish) "
                            >
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-col>
                <v-btn color="error" block @click="getData">
                    Получить данные
                </v-btn>
                <div v-if="topClients">
                    <h5>Топ-3 клиента IRON-ADDICTS:</h5>
                    <v-list>
                        <v-list-item v-for="(client, idx) of topClients" :key="`top-clients-${idx}`">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">{{ client.client }} - {{ client.store }}</v-list-item-title>
                                <v-list-item-title>{{ client.total_cost | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </div>
                <div v-if="topClientsByStore">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Город</th>
                                <th>Клиенты</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="store of stores" :key="`store-row-${store.id}`">
                                <td>
                                    {{ store.name }}
                                </td>
                                <td>
                                    <v-list>
                                        <v-list-item v-for="(client, idx) of topClientsByStore[store.id]" :key="`top-clients-${idx}`">
                                            <v-list-item-content>
                                                <v-list-item-title class="font-weight-black">{{ client.client }}</v-list-item-title>
                                                <v-list-item-title>{{ client.total_cost | priceFilters }}</v-list-item-title>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import moment from 'moment';
    import {getClientAnalytics} from "@/api/clients";
    import ACTIONS from "@/store/actions";
    export default {
        data: () => ({
            start: null,
            finish: null,
            startMenu: null,
            finishMenu: null,
            topClients: null,
            topClientsByStore: null,
        }),
        async created() {
            this.$loading.enable();
            this.start = moment().startOf('month').format('yyyy-MM-DD');
            this.finish = moment().endOf('month').format('yyyy-MM-DD');
            await this.$store.dispatch(ACTIONS.GET_STORES)
            const response = await getClientAnalytics(this.start, this.finish);
            this.topClients = response.data.top_clients_all;
            this.topClientsByStore = response.data.top_clients_store;
            this.$loading.disable();
        },
        methods: {
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
            },
            async getData() {
                this.$loading.enable();
                this.topClients = null;
                this.topClientsByStore = null;
                const response = await getClientAnalytics(this.start, this.finish);
                this.topClients = response.data.top_clients_all;
                this.topClientsByStore = response.data.top_clients_store;
                this.$loading.disable();
            }
        },
        computed: {
            stores() {
                return this.$store.getters.shops;
            }
        }
    }
</script>

<style scoped>

</style>
