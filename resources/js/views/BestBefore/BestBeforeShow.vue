<template>
    <div>
        <v-row>
            <v-col cols="3">
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
                <v-btn color="primary" @click="_getBestBeforeItems">
                    Получить список
                </v-btn>
            </v-col>
            <v-col cols="3">
                <v-select
                    label="Фильтр"
                    v-model="filterValue"
                    :items="filters"
                    item-value="id"
                    item-text="name"
                />
                <v-select
                    label="Склад"
                    v-model="storeFilter"
                    :items="stores"
                    item-value="id"
                    item-text="name"
                />
            </v-col>
        </v-row>
        <v-data-table
            loading-text="Идет загрузка товаров..."
            class="background-iron-grey fz-18"
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :items="filteredItems"
            :headers="headers"
            :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
        >
            <template v-slot:item.product_name="{item}">
                <v-list flat>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ item.product_name }}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                {{ item.attributes }} {{ item.manufacturer }}
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </template>
            <template v-slot:item.is_expired="{item}">
                <v-icon color="success" v-if="item.is_expired">
                    mdi-check
                </v-icon>
                <v-icon color="error" v-else>
                    mdi-close
                </v-icon>
            </template>
            <template v-slot:item.actions="{item}">
                <v-btn color="primary">
                    Списать
                </v-btn>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import axios from "axios";
import date_mixin from "@/mixins/date_mixin";

export default {
    data: () => ({
        date: null,
        items: [],
        filterValue: -1,
        storeFilter: -1,
        filters: [
            {
                id: -1,
                name: 'Все'
            },
            {
                id: 1,
                name: 'Просроченные'
            },
            {
                id: 2,
                name: 'Не просроченные'
            }
        ],
        headers: [
            {
                value: 'product_name',
                text: 'Товар'
            },
            {
                value: 'store.name',
                text: 'Склад'
            },
            {
                value: 'best_before',
                text: 'Годен до'
            },
            {
                value: 'days_to_expire',
                text: 'Осталось дней'
            },
            {
                value: 'is_expired',
                text: 'Истек срок годности'
            },
            {
                value: 'actions',
                text: 'Действие'
            }
        ],
    }),
    mixins: [
        date_mixin
    ],
    computed: {
        stores () {
            return [
                {
                    id: -1,
                    name: 'Все'
                },
                ...this.$store.getters.stores,
            ];
        },
        filteredItems () {
            return this.items.filter(s => {
                if (this.filterValue === -1) {
                    return true;
                }
                if (this.filterValue === 1) {
                    return s.is_expired === true;
                }
                return s.is_expired === false;
            }).filter(s => {
                return this.storeFilter === -1 ? true : s.store.id === this.storeFilter;
            })
        }
    },
    methods: {
        async _getBestBeforeItems () {
            try {
                this.$loading.enable();
                const dates = {
                    start: this.start,
                    finish: this.finish,
                };
                const payload = new URLSearchParams(Object.keys(dates)
                    .filter(key => !!dates[key])
                    .reduce((obj, key) => {
                        obj[key] = dates[key];
                        return obj;
                    }, {}));
                const { data } = await axios.get(`/api/v2/products/best-before/get?${payload}`);
                this.items = data.data;
            } catch (e) {
                this.$toast.error('Что-то пошло не так!');
            } finally {
                this.$loading.disable();

            }
        },
    }
}
</script>

<style scoped lang="scss">

</style>
