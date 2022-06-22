<template>
    <v-card>
        <v-card-title>
            Создание акции
        </v-card-title>
        <v-card-text>
            <v-text-field v-model="title" label="Название акции"/>
            <v-text-field v-model.number="discount" type="number" label="Процент скидки"/>
            <v-row>
                <v-col>
                    <v-text-field v-model="startedAt" type="datetime-local" label="Время начала"/>
                </v-col>
                <v-col>
                    <v-text-field v-model="finishedAt" type="datetime-local" label="Время окончания"/>
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
            <v-btn block color="primary" class="my-2" @click="createStock" :disabled="!cart.length">
                Создать акцию
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
                        <v-list-item v-if="is_admin || IS_BOSS">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">Общая сумма прибыли:</v-list-item-title>
                                <v-list-item-title>{{ totalMargin | priceFilters }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-col>
            </v-row>
            <v-data-table
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
                :loading="loading"
                loading-text="Отчеты обновляются"
                :items="report"
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
            title: '',
            discount: 0,
            startedAt: null,
            finishedAt: null,
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
                    text: 'Количество',
                    value: 'quantity'
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
        }),
        async mounted() {
            await this.$store.dispatch('GET_PRODUCTS_v2');
            await this.$store.dispatch('GET_MAIN_STORE_QUANTITIES');
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.init();
        },
        methods: {
            async createStock() {
                if (this.title.length === 0) {
                    return this.$toast.error('Заполните поле название')
                }
                if (!this.startedAt) {
                    return this.$toast.error('Заполните поле дата начала')
                }
                if (!this.finishedAt) {
                    return this.$toast.error('Заполните поле дата окончания')
                }

                try {
                    this.$loading.enable();
                    const stock = {
                        title: this.title,
                        discount: this.discount,
                        products: this.cart.map(p => p.product_id),
                        started_at: this.startedAt,
                        finished_at: this.finishedAt
                    };

                    const response = await axios.post(`/api/v2/stocks`, stock);
                    this.$toast.success('Акция успешно создана')
                } catch (e) {
                    this.$toast.error('При создании акции произошла ошибка!')
                } finally {
                    this.$loading.disable();
                }



            },
            async chooseAllProduct() {
                this.products.forEach(item => {
                    this.addToList(item);
                })
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
            async init() {
                this.loading = true;
                this.overlay = this.loading = false;
            },
        },
        computed: {
            is_admin() {
                return this.$store.getters.CURRENT_ROLE === 'admin';
            },
            products() {
                let products = this.$store.getters.MAIN_PRODUCTS_v2;
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
