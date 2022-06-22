<template>
    <div>
        <v-card>
            <v-card-title>
                Аналитика продаж выбранных товаров
            </v-card-title>
            <v-card-text>
                <v-col>
                    <v-menu
                        ref="startMenu"
                        v-model="startMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="start"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y
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
                            type="month"
                            :max="maxDate"
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
                            type="month"
                            :max="maxDate"
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
                </v-col>
                <SaleChart
                    v-if="saleAnalyticsLoaded"
                    :chart-data="chartData"
                    :options="options"
                />
                <v-card v-if="cart.length > 0">
                    <v-card-title class="justify-space-between">
                        <span>Корзина</span>
                    </v-card-title>
                    <v-card-text style="padding: 0;">
                        <v-divider></v-divider>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Товары ({{ cart.length }})
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-virtual-scroll :items="cart" height="400" item-height="60">
                                        <template v-slot:default="{ item, index }">
                                            <v-list-item style="border-bottom: 1px solid mintcream">
                                                <v-list-item-content>
                                                    <v-list-item-title>
                                                        {{ item.product_name }}
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle>
                                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                                            item.manufacturer.manufacturer_name }}
                                                    </v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-btn color="error" icon @click="deleteFromCart(index)">
                                                        <v-icon>
                                                            mdi-close
                                                        </v-icon>
                                                    </v-btn>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </template>
                                    </v-virtual-scroll>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
                <v-btn block color="primary" class="my-2" @click="loadReports()" :disabled="!cart.length">
                    Загрузить отчет
                </v-btn>
                <v-text-field
                    class="mt-2"
                    v-on:input="searchInput"
                    v-model="searchValue"
                    solo
                    clearable
                    label="Поиск товара"
                    single-line
                    hide-details
                ></v-text-field>
                <v-row class="d-flex align-center">
                    <v-col cols="12" xl="4">
                        <v-autocomplete
                            :items="categories"
                            item-text="name"
                            v-model="categoryId"
                            item-value="id"
                            label="Категория"
                        />
                    </v-col>
                    <v-col cols="12" xl="4">
                        <v-autocomplete
                            :items="manufacturers"
                            item-text="manufacturer_name"
                            v-model="manufacturerId"
                            item-value="id"
                            label="Бренд"
                        />
                    </v-col>
                    <v-col cols="12" xl="4">
                        <v-btn color="success" @click="chooseAllProduct">
                            Выбрать все товары <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
                <v-data-table
                    v-show="!addingToCart"
                    class="background-tooman-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="product_headers"
                    :search="searchQuery"
                    loading-text="Идет загрузка товаров..."
                    :items="products"
                    :items-per-page.sync="itemsPerPage"
                    @current-items="getFiltered"
                >
                    <template v-slot:item.product_name="{item}">
                        <v-list flat>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.product_name }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }} | {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn depressed icon color="success" @click="addToList(item)">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import SaleChart from "@/components/Charts/SaleChart";
import months from '@/common/enums/months.ru';
import moment from "moment";
import ACTIONS from "@/store/actions";
import { mapGetters } from 'vuex';
import product_search from "@/mixins/product_search";

export default {
    data: () => ({
        itemsPerPage: 10,
        addingToCart: false,
        saleAnalyticsLoaded: false,
        startMenu: null,
        start: null,
        finishMenu: null,
        finish: null,
        maxDate: moment().format('YYYY-MM-DD'),
        chartData: {
            labels: [
                ...months
            ],
            datasets: [
                {
                    label: 'Продажи',
                    data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11],
                    backgroundColor: '#e53935',
                    borderColor: '#000'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        return `Продажи: ${new Intl.NumberFormat('ru-RU').format(Math.ceil(tooltipItem.yLabel))}₸`
                    }
                }
            },
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        precision: 4,
                        maxTicksLimit: 10,
                        callback: function (value, index, values) {
                            return `${new Intl.NumberFormat('ru-RU').format(Math.ceil(value))}₸`                     }
                    }
                }],
                xAxes: [{
                    display: true,
                }],
            }
        },
        product_headers: [
            {
                text: 'Наименование',
                value: 'product_name',
                sortable: false,
                align: ' fz-18'
            },
            {
                value: 'manufacturer.manufacturer_name',
                text: 'Производитель',
                align: ' d-none'
            },
            {
                text: 'Стоимость',
                value: 'product_price'
            },
            {
                text: 'Добавить',
                value: 'actions'
            },
            {
                text: 'Штрих-код',
                value: 'product_barcode',
                align: ' d-none'
            }
        ],
        categoryId: -1,
        manufacturerId: -1,
        currentManufacturerId: -1,
        cart: [],
        filtered: [],
    }),
    async mounted() {
        this.$loading.enable();
        await this.$store.dispatch('GET_PRODUCTS_v2');
        await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
        await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        this.$loading.disable();
    },
    methods: {
        async changeCustomDate() {
            this.$refs.startMenu.save(this.start);
            this.$refs.finishMenu.save(this.finish);
        },
        getFiltered (e) {
            return this.filtered = [...e];
        },
        async loadReports() {
            if (!(this.start && this.finish)) {
                return this.$toast.error('Выберите обе даты!');
            }
            const products = this.cart.map(c => c.id);

            this.saleAnalyticsLoaded = false;

            await this.$store.dispatch(ACTIONS.GET_SALE_ANALYTICS, {
                start: `${this.start}-01`,
                finish: `${this.finish}-01`,
                products
            });

            this.saleAnalyticsLoaded = true;
        },
        addToList(item) {
            const findIndex = this.cart.findIndex(p => p.id === item.id);
            if (findIndex === -1) {
                this.cart.push(item);
            }
        },
        deleteList(key) {
            this.cart.splice(key, 1);
        },
        async chooseAllProduct() {
            this.addingToCart = true;
            this.$loading.enable();
            this.itemsPerPage = -1;
            setTimeout(() => {
                this.filtered.forEach(item => {
                    this.addToList(item);
                });
                this.itemsPerPage = 10;
                this.$loading.disable();
                this.addingToCart = false;
            }, 3000);
        },
    },
    computed: {
        ...mapGetters(['SALE_ANALYTICS', 'SALE_ANALYTIC_LABELS']),
        products() {
            let products = this.$store.getters.PRODUCTS_v2;
            if (this.manufacturerId !== -1) {
                products = products.filter(product => product.manufacturer.id === this.manufacturerId);
            }
            if (this.categoryId !== -1) {
                products = products.filter(product => product.category.id === this.categoryId);
            }
            return products;
        },
        manufacturers() {
            return [
                {
                    id: -1,
                    manufacturer_name: 'Все'
                }, ...this.$store.getters.manufacturers];
        },
        categories() {
            return [
                {
                    id: -1,
                    name: 'Все'
                }, ...this.$store.getters.categories
            ];
        },

    },
    components: {
        SaleChart
    },
    watch: {
        SALE_ANALYTICS(val) {
            this.chartData = {
                labels: [
                    ...this.SALE_ANALYTIC_LABELS
                ],
                datasets: [
                    {
                        label: 'Продажи',
                        data: [...val],
                        backgroundColor: '#e53935',
                        borderColor: '#000'
                    }
                ]
            };
        }
    },
    mixins: [
        product_search
    ]
}
</script>

<style scoped>

</style>
