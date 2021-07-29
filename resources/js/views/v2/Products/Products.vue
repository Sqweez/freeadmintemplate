<template>
    <v-card>
        <v-card-title>Все товары</v-card-title>
        <v-card-text v-if="loading">
            <div
                class="text-center d-flex align-center justify-center"
                style="min-height: 651px">
                <v-progress-circular
                    indeterminate
                    size="65"
                    color="primary"
                ></v-progress-circular>
            </div>
        </v-card-text>
        <v-card-text v-else>
            <h5>Поиск: {{searchQuery}}</h5>
            <div>
                <v-btn color="error" @click="productModal = true" v-if="is_admin">Добавить товар
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </div>
            <v-btn color="success" @click="groupProduct" v-if="is_admin">Сгруппировать товар
                <v-icon>mdi-sync</v-icon>
            </v-btn>
            <v-row>
                <v-col>
                    <v-row>
                    <!--    v-on:input="searchInput"
                        v-model="searchValue"-->
                        <v-col cols="12" xl="4">
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
                        </v-col>
                        <v-col cols="12" xl="4" v-if="is_admin">
                            <v-select
                                :items="stores"
                                item-text="name"
                                v-model="storeFilter"
                                item-value="id"
                                label="Склад"
                            />
                        </v-col>
                        <v-col cols="12" xl="2" v-if="is_admin">
                            <v-select
                                :items="photoFilters"
                                item-text="name"
                                v-model="photoFilter"
                                item-value="id"
                                label="Фильтр фото"
                            />
                        </v-col>
                        <v-col cols="12" xl="2" v-if="is_admin">
                            <v-select
                                :items="descriptionFilters"
                                item-text="name"
                                v-model="descriptionFilter"
                                item-value="id"
                                label="Фильтр описание"
                            />
                        </v-col>
                    </v-row>
                    <v-data-table
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :search="searchQuery"
                        :headers="headers"
                        :page.sync="pagination.page"
                        :items="products"
                        @page-count="pageCount = $event"
                        :items-per-page="10"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                        <template v-slot:item.attributes="{ item }">
                            <ul>
                                <li v-for="(attr, index) of item.attributes" :key="index">
                                    {{ attr.attribute }}: {{ attr.attribute_value }}
                                </li>
                            </ul>
                        </template>
                        <template v-slot:item.quantity="{item}">
                            {{ getQuantity(item.quantity) }}
                        </template>
                        <template v-slot:item.product_price="{item}">
                            {{ getPrice(item) | priceFilters }}
                        </template>
                        <template v-slot:item.categories="{ item }">
                            <ul>
                                <li v-for="(cat, index) of item.categories" :key="index">
                                    {{ cat.category_name }}
                                </li>
                            </ul>
                        </template>
                        <template v-slot:item.tags="{item}">
                            <span>Количество тегов: {{ item.tags.length }}</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <div class="mb-2 d-flex">
                                <v-btn color="error" class="mr-2" @click="changeCount(item.id, -1)">
                                    <v-icon>mdi-minus</v-icon>
                                </v-btn>
                                <v-btn color="success" class="ml-2" @click="changeCount(item.id, 1)">
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </div>
                            <div class="product--actions">
                                <v-btn color="primary" @click="productId = item.id; productQuantityModal = true;">
                                    Количество
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn color="primary" @click="priceTag = item; priceTagModal = true;">
                                    Печать ценника
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </div>
                            <div class="product--actions">
                                <v-btn color="primary"
                                       @click="productId = item.id; rangeMode = true; productModal = true;">
                                    Ассортимент
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </div>
                            <div class="product--actions">
                                <v-btn color="warning" @click="productId = item.id; productModal = true;">
                                    Редактировать
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </div>
                            <div class="product--actions">
                                <v-btn color="error" @click="productId = item.id; deleteModal = true;">
                                    Удалить
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </div>
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
        <ProductModal
            :id="productId"
            v-on:cancel="productId = -1; productModal = false; rangeMode = false;"
            :range-mode="rangeMode"
            :state="productModal"/>
        <ProductQuantityModal
            :id="productId"
            :state="productQuantityModal"
            v-on:cancel="productId = -1; productQuantityModal = false;"
        />
        <ConfirmationModal
            :message="modalText"
            :state="deleteModal"
            :on-confirm="deleteProduct"
            v-on:cancel="productId = -1; deleteModal = false;"
        />
        <PriceTagModal
            :state="priceTagModal"
            :priceTag="priceTag"
            @cancel="priceTagModal = false"
        />
    </v-card>
</template>

<script>
    import ProductRangeModal from "@/components/Modal/ProductRangeModal";
    import ProductModal from "@/components/Modal/ProductModal";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import ProductQuantityModal from "@/components/Modal/ProductQuantityModal";
    import ACTIONS from "@/store/actions";
    import axios from 'axios';
    import PriceTagModal from "@/components/Modal/PriceTagModal";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";

    export default {
        components: {
            PriceTagModal,
            ProductModal,
            ConfirmationModal,
            ProductQuantityModal,
            ProductRangeModal
        },
        async mounted() {
            this.loading = this.products.length === 0;
            const store_id = this.is_admin ? null : this.user.store_id;
            await this.$store.dispatch(ACTIONS.GET_PRODUCT, store_id);
            this.loading = false;
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_ATTRIBUTES);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
        },
        data: () => ({
            priceTagModal: false,
            loading: true,
            options: {},
            productModal: false,
            productRangeModal: false,
            productQuantityModal: false,
            pageCount: 1,
            deleteModal: false,
            modalText: 'Вы действительно хотите удалить выбранный товар?',
            productId: -1,
            storeFilter: null,
            rangeMode: false,
            priceTag: {},
            pagination: {
                ascending: true,
                rowsPerPage: 10,
                page: 1
            },
            photoFilter: 0,
            photoFilters: [
                {
                    name: 'Все товары',
                    id: 0,
                },
                {
                    name: 'Товары без фото',
                    id: 1,
                },
                {
                    name: 'Товары с фото',
                    id: 2,
                },
            ],
            descriptionFilters: [
                {
                    name: 'Все товары',
                    id: 0,
                },
                {
                    name: 'Товары без описания',
                    id: 1,
                },
                {
                    name: 'Товары с описанием',
                    id: 2,
                },
            ],
            descriptionFilter: 0,
        }),
        computed: {
            products() {
                let products = [];
                switch (this.photoFilter) {
                    case 0:
                        products = this.$store.getters.products;
                        break;
                    case 1:
                        products = this.$store.getters.products_without_photo;
                        break;
                    case 2:
                        products = this.$store.getters.products_with_photo;
                        break;
                    default:
                        break;
                }

                if (this.descriptionFilter === 1) {
                    products = products.filter(p => {
                        return p.product_description === null || product.product_description === "";
                    })
                }

                if (this.descriptionFilter === 2) {
                    products =  products.filter(p => {
                        return !(p.product_description === null || product.product_description === "");
                    })
                }
                return products;

            },
            stores() {
                const stores = this.$store.getters.stores;
                if (stores.length > 0) {
                    this.storeFilter = stores[0].id;
                }
                return stores;
            },
            categories() {
                return this.$store.getters.categories;
            },
            totalProducts() {
                return this.$store.getters.totalProducts;
            },
            user() {
                return this.$store.getters.USER;
            },
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
            headers() {
                const headers = [
                    {
                        value: 'product_name',
                        text: 'Наименование',
                        sortable: false,
                    },
                    {
                        value: 'quantity',
                        text: 'Остаток'
                    },
                    {
                        value: 'product_price',
                        text: 'Стоимость'
                    },
                    {
                        value: 'product_barcode',
                        text: 'Штрих-код',
                        align: ' d-none'
                    },
                    {
                        value: 'attributes',
                        text: 'Атрибуты'
                    },
                    {
                        value: 'manufacturer',
                        text: 'Производитель'
                    },
                    {
                        value: 'categories',
                        text: 'Категория'
                    },
                    {
                        value: 'tags',
                        text: 'Теги'
                    }
                ];

                if (this.is_admin) {
                    headers.unshift({
                        value: 'actions',
                        text: 'Действие',
                        sortable: false
                    })
                }

                return headers;
            }
        },
        methods: {
            async deleteProduct() {
                console.log(this.productId)
                await this.$store.dispatch(ACTIONS.DELETE_PRODUCT,
                    this.productId,
                );
                this.productId = -1;
                this.deleteModal = false;
                this.$toast.success('Товар успешно удален');
            },
            getQuantity(quantity = []) {
                if (typeof quantity === 'number') {
                    return quantity;
                }
                if (!quantity.length) {
                    return 0;
                }
                return quantity
                    .filter(q => +q.store_id === +this.storeFilter)
                    .map(q => q.quantity)
                    .reduce((a, c) => {
                        return +a + +c;
                    }, 0)
            },
            async groupProduct() {
                await axios.get('/api/shop/products-group');
                this.$toast.success('Товары успешно сгруппированы!')
            },
            async changeCount(id, increment) {
                const params = {
                    product_id: id,
                    increment,
                    store_id: this.storeFilter
                };
                await this.$store.dispatch('changeCount', params);
            },
        },
        mixins: [product, product_search]
    }
</script>

<style scoped>
    .product--actions .v-btn {
        width: 250px !important;
        text-align: left !important;
        margin-bottom: 10px;
    }
</style>
