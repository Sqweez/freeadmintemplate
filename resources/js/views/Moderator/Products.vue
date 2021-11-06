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
            <div class="mb-5">
                <v-btn color="error" @click="showProductModal()">Добавить товар <v-icon>mdi-plus</v-icon></v-btn>
            </div>
            <v-row>
                <v-col>
                    <v-row>
                        <v-col cols="12" xl="6">
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
                        <v-col cols="12" xl="6">
                            <v-select
                                :items="stores"
                                item-text="name"
                                v-model="storeFilter"
                                item-value="id"
                                label="Склад"
                            />
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" xl="6">
                            <v-select
                                :items="photoFilters"
                                item-text="name"
                                v-model="photoFilter"
                                item-value="id"
                                label="Фотографии"
                            />
                        </v-col>
                        <v-col cols="12" xl="6">
                            <v-select
                                :items="descriptionFilters"
                                item-text="name"
                                v-model="descriptionFilter"
                                item-value="id"
                                label="Описание"
                            />
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" xl="6">
                            <v-checkbox
                                label="Скрывать отсутствующие"
                                v-model="hideNotInStock"
                            />
                        </v-col>
                        <v-col cols="12" xl="6">
                            <v-checkbox
                                label="Группировать товары"
                                v-model="groupProducts"
                            />
                        </v-col>
                    </v-row>
                    <v-data-table
                        :search="searchQuery"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :headers="headers"
                        :page.sync="pagination.page"
                        :items="products"
                        @page-count="pageCount = $event"
                        @update:page="updatePage"
                        :items-per-page="10"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                        <template v-slot:item.attributes="{ item }">
                            <ul>
                                <li v-for="(attr, index) of item.attributes" :key="index">
                                    {{ attr.attribute_name }}: {{ attr.attribute_value }}
                                </li>
                            </ul>
                        </template>
                        <template v-slot:item.product_price="{ item }">
                            <span>{{ item.product_price | priceFilters }}</span>
                        </template>
                        <template v-slot:item.category="{ item }">
                            <span>{{ item.category.category_name }}</span>
                        </template>
                        <template v-slot:item.manufacturer="{ item }">
                            <span>{{ item.manufacturer.manufacturer_name }}</span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <div class="actions-products__container">
                                <v-btn color="success" @click="showProductSkuModal(item.id)" v-if="item.sku_can_be_created">
                                    Ассортимент
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn color="warning" @click="showProductSkuModal(item.id, true)" v-if="item.sku_can_be_created && !groupProducts">
                                    Ассортимент
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn color="success" @click="showProductModal(item.id, 'addProductRange')">
                                    Новый товар
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn color="primary" @click="productId = item.id; productQuantityModal = true;">
                                    Количество
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn color="warning" @click="showProductModal(item.id, 'editProduct')">
                                    Товар
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn color="error" @click="productId = item.id; deleteModal = true;">
                                    Удалить
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                                <div class="mb-2 d-flex justify-space-between" v-if="!groupProducts">
                                    <v-btn color="error" class="mr-2" @click="changeCount(item.id, -1)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <v-btn color="success" class="ml-2" @click="changeCount(item.id, 1)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
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
            v-on:cancel="onCloseProductModal" />
        <ConfirmationModal
            :message="modalText"
            :state="deleteModal"
            :on-confirm="deleteProduct"
            v-on:cancel="productId = -1; deleteModal = false;"
        />
        <ProductQuantityModal
            :state="productQuantityModal"
            @cancel="productQuantityModal = false; productId = -1;"
            @submit="addProductQuantity"
        />
        <SkuModal
            @cancel="$store.commit('modals/closeProductSkuModal')"
        />
    </v-card>
</template>

<script>
    import ProductRangeModal from "@/components/Modal/ProductRangeModal";
    import ProductModal from "@/components/v2/Modal/ProductModal";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import ProductQuantityModal from "@/components/Modal/ProductQuantityModal";
    import ACTIONS from "@/store/actions";
    import axios from 'axios';
    import PriceTagModal from "@/components/Modal/PriceTagModal";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import {PRODUCT_MODAL_EVENTS, TOAST_TYPE} from "@/config/consts";
    import SkuModal from "@/components/v2/Modal/SkuModal";
    import { uniqBy } from 'lodash';

    export default {
        components: {
            SkuModal,
            PriceTagModal,
            ProductModal,
            ConfirmationModal,
            ProductQuantityModal,
            ProductRangeModal
        },
        async created() {
            try {
                await this.$store.dispatch('GET_MODERATOR_PRODUCTS');
            } catch (e) {
                console.log(e.response);
            }
            await this.$store.dispatch(ACTIONS.GET_STORES, null);
            this.storeFilter = this.stores[0].id;
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_ATTRIBUTES);
        },
        data: () => ({
            hideNotInStock: false,
            groupProducts: false,
            priceTagModal: false,
            waitingQuantities: false,
            loading: false,
            options: {},
            productModal: false,
            productRangeModal: false,
            productQuantityModal: false,
            pageCount: 1,
            deleteModal: false,
            modalText: 'Вы действительно хотите удалить выбранный товар?',
            productId: null,
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
            descriptionFilter: 0,
            descriptionFilters: [
                {
                    name: 'Все товары',
                    id: 0,
                },
                {
                    name: 'Товары с описанием',
                    id: 1,
                },
                {
                    name: 'Товары без описания',
                    id: 2,
                },
            ],
        }),
        computed: {
            quantities() {
                return this.$store.getters.QUANTITIES_v2;
            },
            products() {
                let products = this.$store.getters.MODERATOR_PRODUCTS
                    .filter(product => {
                        if (this.descriptionFilter === 2) {
                            return !product.product_description ||product.product_description.length <= 100;
                        }
                        if (this.descriptionFilter === 1) {
                            return product.product_description && product.product_description.length > 100;
                        }
                        return true;
                    })
                    .filter(product => {
                        if (this.photoFilter === 1) {
                            return product.product_images.length === 0 || (product.product_images.length === 1 && product.product_images[0].images === 'product/product_image_default.jpg');
                        }
                        if (this.photoFilter === 2) {
                            return product.product_images.length !== 0;
                        }
                        return product;
                    })
                    .filter(product => {
                        if (this.hideNotInStock) {
                            return product.quantity > 0;
                        }
                        return product;
                    });

                if (this.groupProducts) {
                    products = uniqBy(products, product => product.product_id);
                }

                return products;
            },
            stores() {
                return this.$store.getters.stores;
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
                        value: 'manufacturer.manufacturer_name',
                        text: 'Название производителя',
                        align: ' d-none'
                    },
                    {
                        value: 'category',
                        text: 'Категория'
                    }
                ];

                headers.unshift( {
                    value: 'actions',
                    text: 'Действие',
                    sortable: false
                });

                headers.unshift({
                    value: 'id',
                    text: 'ID',
                    sortable: true
                })
                return headers;
            }
        },
        methods: {
            async changeCount(id, increment) {
                const params = {
                    product_id: id,
                    increment,
                    store_id: this.storeFilter
                };
                await this.$store.dispatch('CHANGE_COUNT_v2', params);
            },
            async deleteProduct() {
                try {
                    await this.$store.dispatch('DELETE_PRODUCT_v2',
                        this.productId,
                    );
                    this.$toast.success('Товар успешно удален');
                } catch (e) {
                    console.log(e.response);
                    this.$toast.error('Произошла ошибка', TOAST_TYPE.ERROR);
                } finally {
                    this.productId = null;
                    this.deleteModal = false;
                }
            },
            async groupProduct() {
                await axios.get('/api/v2/products/group');
                this.$toast.success('Товары успешно сгруппированы!')
            },
            updatePage(page) {

            },
            onCloseProductModal(event) {
                if (event === PRODUCT_MODAL_EVENTS.ADD_PRODUCT) {
                    this.pagination.page = this.pageCount;
                }

                this.closeProductModal();
            },
            async showProductModal(id = null, action = PRODUCT_MODAL_EVENTS.ADD_PRODUCT) {
                if (id !== null) {
                    this.productId = id;
                    await this.$store.dispatch('GET_PRODUCT_v2', id);
                }
                return this.$store.commit('modals/showProductModal', {
                    id, action
                });
            },
            closeProductModal() {
                return this.$store.commit('modals/closeProductModal');
            },
            async showProductSkuModal(id = null, edit = false) {
                if (id === null) {
                    return false
                }
                await this.$store.dispatch('GET_PRODUCT_v2', id);
                return this.$store.commit('modals/showProductSkuModal', {
                    id, edit
                });
            },
            async addProductQuantity(batch) {
                try {
                    await this.$store.dispatch('ADD_PRODUCT_QUANTITY_v2', {
                        id: this.productId,
                        batch,
                        store_id: this.storeFilter,
                    });
                    this.$toast.success('Количество товар успешно изменено!');
                } catch (e) {
                    this.$toast.error('При добавлении количества произошла ошибка');
                } finally {
                    this.productId = false;
                    this.productQuantityModal = false;
                }
            }
        },
        mixins: [product, product_search],
        watch: {
            storeFilter(value) {
                this.$store.dispatch('GET_MODERATOR_PRODUCT_QUANTITIES', value);
            }
        }
    }
</script>

<style>
    .actions-products__container {
        display: flex;
        flex-direction: column;
        row-gap: 10px;
        margin-bottom: 10px;
        width: 200px;
    }

    .v-data-table__mobile-row:first-child > .v-data-table__mobile-row__header {
        display: none;
    }

    .v-data-table__mobile-row:first-child > .v-data-table__mobile-row__cell {
        width: 100%;
    }

    .v-data-table__mobile-row .actions-products__container {
        margin-top: 10px;
        width: 100%;
    }
</style>
