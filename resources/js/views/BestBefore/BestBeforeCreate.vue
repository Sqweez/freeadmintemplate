<template>
    <v-card>
        <v-card-text>
            <v-simple-table v-slot:default>
                <template>
                    <thead class="fz-18">
                    <tr>
                        <th>#</th>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Срок годности</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody class="background-iron-grey">
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
                            {{ item.quantity }} шт
                        </td>
                        <td>
                            <v-text-field
                                label="Срок годности"
                                v-model="item.best_before"
                                type="date"
                            />
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
            <v-btn block color="primary" class="my-2" @click="onSubmit" :disabled="!cart.length">
                Сохранить
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
                <v-col cols="12" xl="4">
                    <v-checkbox
                        label="Скрыть отсутствующие"
                        v-model="hideNotInStock"
                    />
                </v-col>
                <v-col cols="12" xl="4">
                    <v-select
                        :items="stores"
                        item-text="name"
                        v-model="storeFilter"
                        item-value="id"
                        label="Склад"
                        :disabled="!(is_admin || IS_BOSS)"
                    />
                </v-col>
            </v-row>
            <v-data-table
                class="background-iron-grey fz-18"
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
                <template v-slot:item.quantity="{item}">
                    {{ item.quantity - getCartCount(item.id) }}
                </template>
                <template v-slot:item.actions="{item}">
                    <v-btn depressed icon color="success" @click="addToCart({...item, best_before: null})">
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                    <v-btn depressed icon @click="addToCart({...item, best_before: null}, true)" color="success"  v-if="cart.find(c => c.id === item.id)">
                        <span>+1</span>
                    </v-btn>
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
import ReportCancelModal from "@/components/Modal/ReportCancelModal";
import ACTIONS from '@/store/actions/index';
import product_search from "@/mixins/product_search";
import axios from 'axios';
import cart from "@/mixins/cart";
import product from "@/mixins/product";

export default {
    components: {ReportCancelModal, ConfirmationModal},
    mixins: [product_search, cart, product],
    data: () => ({
        moneyRate: 0,
        ratio: 0,
        cart: [],
        overlay: false,
        loading: false,
        productLoaded: false,
        cancelModal: false,
        purchaseId: null,
        currentProducts: [],
        currentStoreType: -1,
        currentCity: -1,
        currentSeller: -1,
        currentType: -1,
        hideNotInStock: false,
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
        const store_id = (this.IS_SUPERUSER) ? null : this.user.store_id;
        await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
        await this.init();
    },
    methods: {
        async chooseAllProduct() {
            this.products.forEach(item => {
                this.addToList(item);
            })
        },
        async onSubmit() {
            const isValid = this.cart.map(c => c.best_before).every(c => !!c);
            if (!isValid) {
                return this.$toast.error('Заполните все поля!');
            }
            try {
                this.$loading.enable();
                const payload = this.cart.map(c => ({
                    product_sku_id: c.id,
                    best_before: c.best_before,
                    store_id: this.storeFilter,
                    quantity: c.count,
                }));
                this.cart = [];
                await axios.post('/api/v2/products/best-before', {
                    products: payload
                })
                this.$toast.success('Успешно!');
            } catch (e) {
                console.log(e);
                this.$toast.error('Произошла ошибка')
            } finally {
                this.$loading.disable();
            }
        },
        calculateFinalPrice () {
            this.cart = this.cart.map(item => {
                const price = item.purchase_price / this.moneyRate;
                item.final_price = Math.floor(price * (this.ratio / 100));
                return item;
            });
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
        async onConfirm() {
            this.closeModal();
        },
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
            if (this.hideNotInStock) {
                products = products.filter(p => p.quantity > 0);
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
