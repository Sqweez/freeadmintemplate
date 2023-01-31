<template>
    <v-dialog
        persistent
        max-width="1200"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Промокод</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field label="Промокод" type="text" v-model="promocode.promocode"></v-text-field>
                <v-select
                    label="Тип промокода"
                    v-model="promocode.promocode_type_id"
                    :items="types"
                    item-text="name"
                    item-value="id"
                />
                <v-autocomplete
                    v-if="[1, 2].includes(promocode.promocode_type_id)"
                    label="Партнер"
                    :items="partners"
                    item-value="id"
                    item-text="client_name"
                    v-model="promocode.client_id"
                />
                <v-text-field
                    v-if="[1, 2].includes(promocode.promocode_type_id)"
                    label="Скидка"
                    type="number"
                    v-model="promocode.discount"
                />
                <v-text-field
                    v-if="[1, 2, 4].includes(promocode.promocode_type_id)"
                    label="Минимальная сумма покупки"
                    v-model="promocode.min_total"
                />
                <v-text-field
                    label="Активен до"
                    type="date"
                    v-model="promocode.active_until"
                    hint="Оставьте пустым, чтобы сделать промокод бессрочным"
                    persistent-hint
                />
                <div v-if="promocode.promocode_type_id === 3">
                    <h5>Обязательны к покупке:</h5>
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
                            <tbody class="background-iron-grey">
                            <tr v-for="(item, index) of promocode.required_products">
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
                                    <v-btn icon color="error" @click="deleteFromRequiredList(index)">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </div>
                <div v-if="[3, 4].includes(promocode.promocode_type_id)">
                    <h5>Подарок:</h5>
                    <v-simple-table v-slot:default v-if="freeProduct">
                        <template>
                            <thead class="fz-18">
                            <tr>
                                <th>Товар</th>
                                <th>Цена</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody class="background-iron-grey">
                            <tr >
                                <td>
                                    <v-list class="product__list" flat>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    {{ freeProduct.product_name }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle>
                                                    {{ freeProduct.manufacturer.manufacturer_name }} | {{ freeProduct.category.category_name }} | {{ freeProduct.attributes.map(a => a.attribute_value).join(', ') }}
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </td>
                                <td>
                                    {{ freeProduct.product_price | priceFilters }}
                                </td>
                                <td>
                                    <v-btn icon color="error" @click="freeProduct = null;">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </div>
                <div v-if="[3, 4].includes(promocode.promocode_type_id)">
                    <h5>Товары:</h5>
                    <v-text-field
                        class="mt-2"
                        v-model="search"
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
                    </v-row>
                    <v-data-table
                        class="background-iron-grey fz-18"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :headers="product_headers"
                        :search="search"
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
                            <div class="d-flex py-3" style="flex-direction: column; justify-content: center; row-gap: 16px;">
                                <v-btn
                                    v-if="promocode.promocode_type_id === 3"
                                    depressed
                                    color="success"
                                    @click="addToRequiredList(item)"
                                    small
                                >
                                    Обязателен к покупке <v-icon>mdi-check</v-icon>
                                </v-btn>
                                <v-btn
                                    v-if="[3, 4].includes(promocode.promocode_type_id)"
                                    depressed
                                    color="success"
                                    @click="freeProduct = {...item}"
                                    small
                                >
                                    Подарок <v-icon>mdi-gift</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">
                    {{ editMode ? `Отредактировать` : `Создать` }} <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            promocode: {
                promocode: '',
                client_id: null,
                discount: 0,
                promocode_type_id: 1,
                min_total: 0,
                required_products: [],
                free_product_id: null,
                brand_id: null,
                active_until: null,
            },
            freeProduct: null,
            categoryId: -1,
            manufacturerId: -1,
            search: '',
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
        }),
        methods: {
            addToRequiredList (item) {
                const findIndex = this.promocode.required_products.findIndex(p => p.product_id === item.product_id);
                if (findIndex === -1) {
                    this.promocode.required_products.push({
                        ...item,
                        count: 1
                    });
                }
            },
            deleteFromRequiredList (idx) {
                this.promocode.required_products.splice(idx, 1);
            },
            onSubmit () {
                const promocode = { ...this.promocode };
                if (!this.promocode.promocode) {
                    return this.$toast.error('Введите название промокода!');
                }
                if ([1, 2].includes(this.promocode.promocode_type_id)) {
                    if (!this.promocode.discount) {
                        return this.$toast.error('Введите величину скидки!');
                    }
                }
                if (this.promocode.promocode_type_id === 3) {
                    if (this.promocode.required_products.length === 0) {
                        return this.$toast.error('Добавьте хотя бы один обязательный товар!');
                    }
                    if (!this.freeProduct) {
                        return this.$toast.error('Выберите подарок!');
                    }

                    promocode.required_products = promocode.required_products.map(r => ({
                        product_id: r.product_id,
                        count: r.count
                    }))
                    promocode.free_product_id = this.freeProduct.product_id;
                }

                this.$emit('submit', promocode);
            }
        },
        computed: {
            partners() {
                return this.$store.getters.PARTNERS;
            },
            types () {
                return this.$store.getters.PROMOCODE_TYPES;
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
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            editMode: {
                type: Boolean,
                default: false
            },
            _promocode: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(value){
                this.promocode = {
                    promocode: '',
                    client_id: null,
                    discount: null,
                    active_until: null,
                    promocode_type_id: 1,
                    min_total: 0,
                    required_products: [],
                    free_product_id: null,
                    brand_id: null,
                };
                this.freeProduct = null;
                this.categoryId = -1;
                this.manufacturerId = -1;
                if (this.editMode) {
                    this.promocode = JSON.parse(JSON.stringify(this._promocode));
                }
            },
            promocode: {
                deep: true,
                handler(value) {

                }
            }
        }
    }
</script>

<style scoped>

</style>
