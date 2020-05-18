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
            <v-btn color="error" @click="productModal = true">Добавить товар <v-icon>mdi-plus</v-icon></v-btn>
            <v-row>
                <v-col>
                    <v-row>
                        <v-col cols="12" xl="8">
                            <v-text-field
                                class="mt-2"
                                v-model="search"
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
                            />
                        </v-col>
                    </v-row>
                    <v-data-table
                        :search="search"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
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
                            {{ item.product_price }} ₸
                        </template>
                        <template v-slot:item.categories="{ item }">
                            <ul>
                                <li v-for="(cat, index) of item.categories" :key="index">
                                   {{ cat.category_name }}
                                </li>
                            </ul>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <div>
                                <v-btn color="primary" @click="productId = item.id; productQuantityModal = true;">
                                    Количество
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </div>
                            <div>
                                <v-btn color="primary" @click="productId = item.id; rangeMode = true; productModal = true;">
                                    Ассортимент
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </div>
                            <div>
                                <v-btn color="warning" @click="productId = item.id; productModal = true;">
                                    Редактировать
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </div>
                            <div>
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
    </v-card>
</template>

<script>
    import ProductRangeModal from "../../components/Modal/ProductRangeModal";
    import ProductModal from "../../components/Modal/ProductModal";
    import ConfirmationModal from "../../components/Modal/ConfirmationModal";
    import ProductQuantityModal from "../../components/Modal/ProductQuantityModal";
    import showToast from "../../utils/toast";
    import ACTIONS from "../../store/actions";

    export default {
        components: {
            ProductModal,
            ConfirmationModal,
            ProductQuantityModal,
            ProductRangeModal
        },
        async mounted() {
            this.loading = this.products.length === 0;
            await this.$store.dispatch(ACTIONS.GET_PRODUCT);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            this.loading = false;
        },
        data: () => ({
            search: '',
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
            pagination: {
                ascending: true,
                rowsPerPage: 10,
                page: 1
            },
            headers: [
                {
                    value: 'actions',
                    text: 'Действие',
                    sortable: false
                },
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
                }
            ]
        }),
        computed: {
            products() {
                return this.$store.getters.products;
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
                showToast('Товар успешно удален');
            },
            getQuantity(quantity = []) {
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
        }
    }
</script>

<style scoped>
    .v-btn {
        width: 180px!important;
        text-align: left!important;
        margin-bottom: 10px;
    }
</style>
