<template>
    <v-card>
        <v-card-title>
            Отчеты по товарам
        </v-card-title>
        <v-card-text>
            <v-row>
                <v-col cols="12" xl="3" justify="center">
                    <v-btn color="success" v-if="productLoaded" @click="getExcelReports">
                        Выгрузить в excel
                    </v-btn>
                    <!--<v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма продаж:</v-list-item-title>
                                <v-list-item-title>{{ totalSales | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="is_admin">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма прибыли:</v-list-item-title>
                                <v-list-item-title>{{ totalMargin | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>-->
                </v-col>
                <v-col cols="12" xl="3">
                    <v-select
                        :items="dateFilters"
                        item-text="name"
                        item-value="value"
                        v-model="currentDate"
                        v-if="is_admin"
                        label="Время:"
                    />
                </v-col>
                <v-col v-if="currentDate === 4">
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
                <v-col>
                    <label>Прочие фильтры:</label>
                    <v-select
                        v-if="is_admin"
                        :items="shops"
                        item-text="name"
                        item-value="id"
                        v-model="currentCity"
                        label="Город:"
                    >
                    </v-select>
                    <v-select
                        v-if="is_admin"
                        :items="sellers"
                        label="Продавец:"
                        v-model="currentSeller"
                        item-value="id"
                        item-text="name">
                    </v-select>
                </v-col>
            </v-row>
            <v-simple-table v-slot:default>
                <template>
                    <thead class="fz-18">
                    <tr>
                        <th>#</th>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody class="background-tooman-grey">
                    <tr v-for="(item, index) of cart">
                        <td>{{ index + 1 }}</td>
                        <td>
                            <v-list class="product__list" flat>
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
                        </td>
                        <td>
                            {{ item.product_price | priceFilters }}
                        </td>
                        <td>
                            <v-btn icon color="error" @click="deleteList(index)">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
            <v-btn block color="primary" class="my-2" @click="loadReports(false)" :disabled="!cart.length">
                Загрузить отчет
            </v-btn>
            <v-btn block color="primary" class="my-2" @click="loadReports(true)">
                Загрузить отчет по всем товарам
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
                class="background-tooman-grey fz-18"
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="product_headers"
                :search="searchQuery"
                loading-text="Идет загрузка товаров..."
                :items="products"
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
        <v-card-text v-if="productLoaded">
            <v-row>
                <v-col cols="12" xl="3" justify="center">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма продаж:</v-list-item-title>
                                <v-list-item-title>{{ totalSales | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item v-if="is_admin">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма прибыли:</v-list-item-title>
                                <v-list-item-title>{{ totalMargin | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-col>
                <v-col cols="12" xl="3" justify="center">
                    <v-autocomplete
                        :items="currentManufacturers"
                        item-text="manufacturer_name"
                        v-model="currentManufacturerId"
                        item-value="id"
                        label="Бренд"
                    />
                </v-col>
            </v-row>
            <v-data-table
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
                :loading="loading"
                loading-text="Отчеты обновляются"
                :items="_reports"
                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
            >
                <template v-slot:item.product_name="{item}">
                    <v-list>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{ item.product_name }}</v-list-item-title>
                                <v-list-item-subtitle>{{ item.attributes }}<span v-if="item.manufacturer">,</span> {{ item.manufacturer }}</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.total_purchase_price="{item}">
                    {{ item.total_purchase_price | priceFilters }}
                </template>
                <template v-slot:item.total_product_price="{item}">
                    {{ item.total_product_price | priceFilters }}
                </template>
                <template v-slot:item.margin="{item}">
                    {{ item.margin | priceFilters }}
                </template>
                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                </template>
            </v-data-table>
        </v-card-text>
        <ReportCancelModal
            :state="cancelModal"
            :products="currentProducts"
            :id="purchaseId"
            v-on:cancel="closeModal"
            v-on:confirm="onConfirm"
        />
    </v-card>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import moment from 'moment';
    import ReportCancelModal from "@/components/Modal/ReportCancelModal";
    import ACTIONS from '@/store/actions/index';
    import product_search from "@/mixins/product_search";
    import axios from 'axios';

    const DATE_FILTERS = {
        ALL_TIME: 1,
        CURRENT_MONTH: 2,
        TODAY: 3,
        CUSTOM_FILTER: 4,
        LAST_3_DAYS: 5,
    };

    const DATE_FORMAT = 'YYYY-MM-DD';

    export default {
        components: {ReportCancelModal, ConfirmationModal},
        mixins: [product_search],
        data: () => ({
            cart: [],
            overlay: false,
            loading: false,
            productLoaded: false,
            cancelModal: false,
            purchaseId: null,
            currentProducts: [],
            currentStoreType: -1,
            startMenu: null,
            report: [],
            editMode: false,
            start: null,
            finishMenu: null,
            today: moment(),
            finish: null,
            currentDate:  [
                moment().startOf('month').format(DATE_FORMAT),
                moment().format(DATE_FORMAT),
            ],
            currentCity: -1,
            currentSeller: -1,
            currentType: -1,
            reports: [],
            dateFilters: [
                {
                    name: 'Сегодня',
                    value: [
                        moment().format(DATE_FORMAT),
                        moment().format(DATE_FORMAT),
                    ],
                },
                {
                    name: 'Последние 3 дня',
                    value: [
                        moment().subtract(3, 'days').format(DATE_FORMAT),
                        moment().format(DATE_FORMAT),
                    ],
                },
                {
                    name: 'За текущий месяц',
                    value: [
                        moment().startOf('month').format(DATE_FORMAT),
                        moment().format(DATE_FORMAT),
                    ],
                },
                {
                    name: 'За все время',
                    value: [
                        moment.unix(1).format(DATE_FORMAT),
                        moment().format(DATE_FORMAT)
                    ],
                },
                {
                    name: 'Произвольно',
                    value: DATE_FILTERS.CUSTOM_FILTER
                },
            ],
            headers: [
                {text: 'Товар', value: 'product_name', align: ' min-w-250 w-30'},
                {text: 'Количество', value: 'count', align: ' font-weight-black'},
                {text: 'Общая закупочная', value: 'total_purchase_price'},
                {text: 'Общая продажная', value: 'total_product_price'},
                {text: 'Прибыль', value: 'margin'},
            ],
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
        }),
        async mounted() {
            await this.$store.dispatch('GET_PRODUCTS_v2');
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.init();
        },
        methods: {
            async chooseAllProduct() {
                this.products.forEach(item => {
                    this.addToList(item);
                })
            },
            async loadReports(all = false) {
                this.loading = true;
                this.productLoaded = false;
                const products = all === false ? JSON.stringify(this.cart.map(c => c.id)) : JSON.stringify(this.products.map(c => c.id));
                const queryObject = {
                    date_start: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.start : this.currentDate[0],
                    date_finish: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.finish : this.currentDate[1]
                };

                if (this.currentSeller !== -1) {
                    queryObject.user_id = this.currentSeller;
                }

                if (this.currentCity !== -1) {
                    queryObject.store_id = this.currentCity;
                }

                const response = await axios.post(`/api/reports/products?${new URLSearchParams(queryObject)}`, {
                    products
                });

                this.report = response.data;
                const reports = [];
                Object.values(this.report).forEach(item => {
                    reports.push(item)
                });
                this.report = reports;
                this.loading = false;
                this.productLoaded = true;
            },
            async getExcelReports() {
                this.loading = true;
                const products = JSON.stringify(this.cart.map(c => c.id));

                const queryObject = {
                    date_start: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.start : this.currentDate[0],
                    date_finish: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.finish : this.currentDate[1]
                };

                if (this.currentSeller !== -1) {
                    queryObject.user_id = this.currentSeller;
                }

                if (this.currentCity !== -1) {
                    queryObject.store_id = this.currentCity;
                }

                const { data } = await axios.get(`/api/v2/documents/report/products?products=${products}&${new URLSearchParams(queryObject)}`);
                const link = document.createElement('a');
                link.href = `${window.location.origin}/${data.path}`;
                link.click();
                this.loading = false;
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
            async changeSale() {
                this.overlay = true;
                await this.$store.dispatch('updateSale', {
                    id: this.report.id,
                    payment_type: this.report.payment_type
                });
                this.report = {};
                this.overlay = false;
                this.editMode = false;
            },
            closeModal() {
                this.currentProducts = [];
                this.purchaseId = null;
                this.cancelModal = false;
            },
            async init() {
                this.loading = true;
                this.overlay = this.loading = false;
            },
            async onConfirm() {
                this.closeModal();
            },
            printCheck(id) {
                window.open(`/check/${id}`, '_blank');
            },
            async loadReport() {

                if (this.currentDate === DATE_FILTERS.CUSTOM_FILTER) {
                    if (!(this.start || this.finish)) {
                        return;
                    }
                }
                this.overlay = true;
                this.loading = true;
                const dateObject = {
                    start: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.start : this.currentDate[0],
                    finish: this.currentDate === DATE_FILTERS.CUSTOM_FILTER ? this.finish : this.currentDate[1]
                };
                await this.$store.dispatch(ACTIONS.GET_REPORTS, dateObject);
                this.overlay = false;
                this.loading = false;

            },
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
            }
        },
        computed: {
            is_admin() {
                return this.$store.getters.CURRENT_ROLE === 'admin';
            },
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
            is_seller() {
                return this.$store.getters.CURRENT_ROLE === 'seller';
            },
            sellers() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.users];
            },
            shops() {
                return [{id: -1, name: 'Все'}, ...this.$store.getters.shops];
            },
            store_types() {
                return [{id: -1, type: 'Все'}, ...this.$store.getters.store_types];
            },
            totalSales() {
                return this._reports.reduce((a, c) => {
                    return a + c.total_product_price;
                }, 0)
            },
            totalMargin() {
                return this._reports.reduce((a, c) => {
                    return a + c.margin;
                }, 0)
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
            _reports() {
                return this.report.filter(r => {
                    return this.currentManufacturerId !== -1 ? r.manufacturer_id === this.currentManufacturerId : true;
                })
            },
            currentManufacturers() {
                const manufacturers = this.$store.getters.manufacturers;
                const currentIds = this.report
                    .map(r => r.manufacturer_id)
                    .filter((value, index, self) => {
                        return self.indexOf(value) === index;
                    });
                return [
                    {
                        id: -1,
                        manufacturer_name: 'Все'
                    },
                    ...manufacturers.filter(m => {
                        return currentIds.indexOf(m.id) !== -1;
                    })
                ]
            }
            /*totalSales() {
                return this.salesReport
                    .reduce((a, c) => {
                        if (c.split_payment === null || this.currentType === -1) {
                            return a + c.final_price
                        } else {
                            return a + +c.split_payment.find(s => s.payment_type == this.currentType).amount;
                        }
                    }, 0);
            },
            totalMargin() {
                return this.salesReport
                    .reduce((a, c) => {
                        return a + c.margin
                    }, 0);
            },*/
        }
    }
</script>

<style>
    h5 {
        font-size: 18px;
    }

    .min-w-250 {
        width: 300px;
    }

    .v-data-table>.v-data-table__wrapper>table>tbody>tr>td, .v-data-table>.v-data-table__wrapper>table>tfoot>tr>td, .v-data-table>.v-data-table__wrapper>table>thead>tr>td {
        height: auto!important;
    }

    @media (max-width: 550px) {
        .v-data-table__mobile-row__cell {
            text-align: left!important;
        }
    }

    .w-30 {
        width: 30%;
    }


</style>
