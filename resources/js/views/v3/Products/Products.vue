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
                <v-btn color="error" @click="showProductModal()" v-if="is_admin || IS_BOSS">Добавить товар
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </div>
            <v-btn color="success" v-if="is_admin || IS_BOSS" @click="exportProductBatches">Выгрузить себестоимости
            </v-btn>
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
                        <v-col cols="12" xl="2">
                            <v-checkbox
                                label="Скрывать отсутствующие"
                                v-model="hideNotInStock"
                            />
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" xl="4">
                            <v-autocomplete
                                label="Производитель"
                                :items="manufacturers"
                                v-model="manufacturerId"
                                item-value="id"
                                item-text="manufacturer_name"
                            />
                        </v-col>
                        <v-col cols="12" xl="4">
                            <v-autocomplete
                                label="Категория"
                                :items="categories"
                                v-model="categoryId"
                                item-value="id"
                                item-text="name"
                            />
                        </v-col>
                        <v-col cols="12" xl="4">
                            <v-autocomplete
                                label="Подкатегория"
                                :items="subcategories"
                                v-model="subcategoryId"
                                item-value="id"
                                item-text="subcategory_name"
                            />
                        </v-col>
                        <v-col cols="12" xl="4">
                            <v-autocomplete
                                :items="marginTypes"
                                label="Тип маржинальности"
                                item-value="id"
                                item-text="title"
                                v-model="currentMarginType"
                            />
                        </v-col>
                        <v-col cols="12" xl="4">
                            <v-autocomplete
                                :items="kaspiVisibleFilters"
                                label="Виден на каспи"
                                item-value="id"
                                item-text="text"
                                v-model="isKaspiVisibleFilter"
                            />
                        </v-col>
                        <v-col cols="12" xl="4">
                            <v-checkbox
                                label="Показать только главные товары"
                                v-model="showMainProducts"
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
                        <template v-slot:item.additional_data="{ item }">
                            <v-list>
                                <v-list-item v-if="item.product_name_web">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.product_name_web }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Название интернет-магазина
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.margin_type.title }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Тип маржинальности
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon color="success" v-if="item.is_kaspi_visible">mdi-check</v-icon>
                                            <v-icon color="error" v-else>mdi-close</v-icon>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Виден в каспи
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon color="success" v-if="item.is_iherb">mdi-check</v-icon>
                                            <v-icon color="error" v-else>mdi-close</v-icon>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                           Товар IHerb
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </template>
                        <template v-slot:item.quantity="{item}">
                            <span v-if="storeFilter === -1">
                                <v-list v-if="quantities[item.id]">
                                    <v-list-item v-for="(quantity) of getQuantities(item.id)">
                                        <v-list-item-content>
                                            <v-list-item-title>{{ quantity.quantity }} шт</v-list-item-title>
                                            <v-list-item-title
                                                class="font-weight-black">{{ quantity.name }}</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                                <v-list v-else>
                                    <v-list-item>
                                         <v-list-item-content>
                                            <v-list-item-title>0 шт</v-list-item-title>
                                            <v-list-item-title class="font-weight-black">Всего</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </span>
                            <span v-else>
                                {{ item.quantity }}
                            </span>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <div class="actions-products__container" v-if="IS_FRANCHISE">
                                <v-btn color="success" @click="changeCount(item.id, 1)">
                                    Количество
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn v-if="!item.sku_can_be_created" color="warning" @click="showProductModal(item.id, 'editProduct')">
                                    Ассортимент
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <div class="mb-2 d-flex justify-space-between" v-if="storeFilter !== -1">
                                    <v-btn color="error" class="mr-2" @click="changeCount(item.id, -1)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <v-btn color="success" class="ml-2" @click="changeCount(item.id, 1)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </div>
                            <div class="actions-products__container" v-if="IS_SENIOR_SELLER">
                                <v-btn color="warning" @click="showProductSkuModal(item.id, true)"
                                       v-if="item.sku_can_be_created">
                                    Ассортимент
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn color="success" @click="changeCount(item.id, 1)">
                                    Количество
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn v-if="!item.sku_can_be_created" color="warning" @click="showProductModal(item.id, 'editProduct')">
                                    Ассортимент
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </div>
                            <div class="actions-products__container" v-if="IS_MODERATOR">
                                <v-btn color="warning" @click="showProductModal(item.id, 'editProduct')">
                                    Товар
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </div>
                            <div class="actions-products__container" v-if="IS_SUPERUSER">
                                <v-btn color="success" @click="showProductSkuModal(item.id)"
                                       v-if="item.sku_can_be_created">
                                    Ассортимент
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn color="warning" @click="showProductSkuModal(item.id, true)"
                                       v-if="item.sku_can_be_created">
                                    Ассортимент
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn color="success" @click="showProductModal(item.id, 'addProductRange')">
                                    Новый товар
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <v-btn color="primary" @click="productId = item.id; productQuantityModal = true;"
                                       v-if="storeFilter !== -1">
                                    Количество
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                                <!--  <v-btn color="primary" @click="priceTag = item; priceTagModal = true;">
                                      Печать ценника
                                      <v-icon>mdi-plus</v-icon>
                                  </v-btn>-->
                                <v-btn color="warning" @click="showProductModal(item.id, 'editProduct')">
                                    Товар
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-btn color="error" @click="productId = item.id; deleteModal = true;">
                                    Удалить
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                                <div class="mb-2 d-flex justify-space-between" v-if="storeFilter !== -1">
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
            v-on:cancel="onCloseProductModal"/>
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
        <!-- <PriceTagModal
             :state="priceTagModal"
             :priceTag="priceTag"
             @cancel="priceTagModal = false"
         />-->
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
    import {PRODUCT_MODAL_EVENTS} from "@/config/consts";
    import SkuModal from "@/components/v2/Modal/SkuModal";

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
            this.showMainProducts = !!this.IS_MODERATOR;
            const store_id = (this.is_admin || this.IS_BOSS) ? null : this.user.store_id;
            console.log(store_id);
            try {
                await this.$store.dispatch('GET_PRODUCTS_v2');
            } catch (e) {
                console.log(e.response);
            }
            await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
            if (this.IS_SUPERUSER) {
                this.storeFilter = -1;
            } else {
                this.storeFilter = -1;
            }

            if (this.IS_FRANCHISE) {
                this.storeFilter = this.user.store_id;
            }

            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_ATTRIBUTES);
            await this.$store.dispatch(ACTIONS.GET_SUPPLIERS);
        },
        data: () => ({
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
            hideNotInStock: false,
            manufacturerId: -1,
            categoryId: -1,
            subcategoryId: -1,
            currentMarginType: -1,
            kaspiVisibleFilters: [
                {
                    id: -1,
                    text: 'Не важно'
                },
                {
                    id: true,
                    text: 'Да'
                },
                {
                    id: false,
                    text: 'Нет'
                },
            ],
            isKaspiVisibleFilter: -1,
            showMainProducts: false,
        }),
        computed: {
            marginTypes () {
                return [{id: -1, title: 'Все'}, ...this.$store.getters.MARGIN_TYPES];
            },
            manufacturers() {
                return [
                    {
                        id: -1,
                        manufacturer_name: 'Все'
                    },
                    ...this.$store.getters.manufacturers
                ];
            },
            quantities() {
                return this.$store.getters.QUANTITIES_v2;
            },
            products() {
                let products = this.showMainProducts ? this.$store.getters.MAIN_PRODUCTS_v2 : this.$store.getters.PRODUCTS_v2;
                if (this.hideNotInStock) {
                    products = products.filter(product => product.quantity > 0);
                }

                if (this.manufacturerId !== -1) {
                    products = products.filter(p => p.manufacturer.id === this.manufacturerId);
                }

                if (this.categoryId !== -1) {
                    products = products.filter(p => p.category.id === this.categoryId);
                }

                if (this.subcategoryId !== -1) {
                    products = products.filter(p => p.subcategory_id === this.subcategoryId);
                }

                if (this.currentMarginType !== -1) {
                    products = products.filter(p => p.margin_type.id === this.currentMarginType);
                }

                if (this.isKaspiVisibleFilter !== -1) {
                    products = products.filter(p => p.is_kaspi_visible === this.isKaspiVisibleFilter);
                }

                return products;
            },
            stores() {
                return [{
                    name: 'Все',
                    id: -1
                }, ...this.$store.getters.stores];
            },
            categories() {
                return [{
                    id: -1,
                    name: 'Все'
                }, ...this.$store.getters.categories];
            },
            subcategories() {
                return [
                    {
                        id: -1,
                        subcategory_name: 'Все'
                    }, ...this.categories
                        .find(c => c.id === this.categoryId)
                        .subcategories || []];
            },
            totalProducts() {
                return this.$store.getters.totalProducts;
            },
            user() {
                return this.$store.getters.USER;
            },
            is_admin() {
                return this.$store.getters.IS_ADMIN || this.$store.getters.IS_STOREKEEPER;
            },
            headers() {
                let headers = [
                    {
                        value: 'product_name',
                        text: 'Наименование',
                        sortable: true,
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
                    },
                    {
                        value: 'additional_data',
                        text: 'Доп данные'
                    }
                ];

                if (this.is_admin || this.IS_BOSS || this.IS_SENIOR_SELLER || this.IS_MODERATOR || this.IS_FRANCHISE) {
                    headers.unshift({
                        value: 'actions',
                        text: 'Действие',
                        sortable: false
                    });

                    headers.unshift({
                        value: 'id',
                        text: 'ID',
                        sortable: true
                    })
                }

                if (this.IS_MODERATOR) {
                    headers = headers.filter(h => !['quantity', 'additional_data'].includes(h.value));
                }

                if (!this.IS_SUPERUSER) {
                    headers = headers.filter(h => h.value !== 'product_name_web');
                }

                return headers;
            },
        },
        methods: {
            getQuantities(id) {
                let qnt = this.quantities[id];
                if (!this.IS_SUPERUSER) {
                    qnt = qnt.filter(q => {
                        return [-1, 1, 6, this.user.store_id].includes(q.store_id);
                    });
                    qnt = qnt.map(q => {
                        if (q.store_id === -1) {
                            q.quantity = qnt.filter(q => q.store_id !== -1).reduce((a, c) => {
                                return a + c.quantity;
                            }, 0)
                            console.log(q);
                        }
                        return q;
                    })
                }
                return qnt;
            },
            async exportProductBatches() {
                this.loading = true;
                const {data} = await axios.get('/api/v2/documents/batches/purchases');
                const link = document.createElement('a');
                link.href = data.path;
                link.click();
                this.loading = false;
            },
            async changeCount(id, increment) {
                const params = {
                    product_id: id,
                    increment,
                    store_id: this.storeFilter
                };

                try {
                    const result = await this.$store.dispatch('CHANGE_COUNT_v2', params);
                } catch (e) {
                    if (increment === 1) {
                        this.productId = id;
                        this.productQuantityModal = true;
                    }
                }
            },
            async deleteProduct() {
                try {
                    await this.$store.dispatch('DELETE_PRODUCT_v2',
                        this.productId,
                    );
                    this.$toast.success('Товар успешно удален');
                } catch (e) {
                    console.log(e.response);
                    this.$toast.error('Произошла ошибка');
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
        mixins: [product, product_search]
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
