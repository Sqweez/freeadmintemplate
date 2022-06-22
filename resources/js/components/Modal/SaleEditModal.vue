<template>
    <v-dialog max-width="1800" v-model="state" persistent>
        <v-card v-if="!IS_LOADING_STATE">
            <v-card-title class="headline justify-space-between">
                <span class="white--text">Редактирование заказа</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-list v-if="client" class="d-flex">
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">ФИО</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Телефон</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_phone }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Сумма покупок</v-list-item-subtitle>
                            <v-list-item-title>{{ client.total_sum | priceFilters}}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Баланс</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_balance | priceFilters }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-subtitle class="client__table-heading">Скидка</v-list-item-subtitle>
                            <v-list-item-title>{{ client.client_discount }}%</v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-btn depressed icon @click="cancelClient">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                            <v-btn depressed icon @click="clientCartModal = true">
                                <v-icon>mdi-account</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
                <v-row>
                    <v-col sm="10"></v-col>
                    <v-col sm="2">
                        <v-text-field
                            v-model.number="discountPercent"
                            class="w-100px"
                            type="number"
                            suffix="%"
                            :max="100"
                            color="white darken-2"
                            label="Скидка"
                            outlined
                        />
                    </v-col>
                </v-row>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th>#</th>
                            <th>Товар</th>
                            <th>Количество</th>
                            <th>Скидка</th>
                            <th>Цена</th>
                            <th>Стоимость</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
                        <tr v-for="(item, index) of cart" :key="`product-id-${item.uuid}`">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>

                                <div class="d-flex align-center">
                                    <v-btn depressed text icon color="error" @click="decreaseCartCount(index)">
                                        <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <v-text-field v-model="item.count" type="number" style="min-width: 40px; max-width: 40px; text-align: center"  @change="updateCount($event, item)" @input="updateCount($event, item)"/>
                                    <v-btn depressed text icon color="success" @click="increaseCartCount(index)">
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </td>
                            <td>
                                <v-text-field
                                    type="number"
                                    v-model="item.discount"
                                    @input="updateDiscount(item)"
                                    suffix="%"
                                    @change="updateDiscount(item)"
                                />
                            </td>
                            <td>{{ item.product_price | priceFilters}}</td>
                            <td>{{ item.product_price * item.count - (Math.max(discountPercent, item.discount) / 100 * item.product_price * item.count) | priceFilters }}</td>
                            <td>
                                <v-btn icon color="error" @click="deleteFromCart(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Процент скидки</th>
                            <th class="text-center">Скидка</th>
                            <th class="green--text darken-1 text-center">Итого к оплате</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr class="pt-5">
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal | priceFilters}}</td>
                            <td class="text-center">{{ discount }}%</td>
                            <td class="text-center">{{ discountTotal | priceFilters}}</td>
                            <td class="text-center green--text darken-1">{{ finalPrice | priceFilters }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-btn color="error" class="mt-5" block @click="onSubmit">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
                <v-row>
                    <v-col cols="12" xl="10">
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
                    <v-col cols="12" xl="2">
                        <v-checkbox
                            v-model="hideNotInStock"
                            label="Скрывать отсутствующие"
                        />
                    </v-col>
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
                    <v-col cols="12" xl="4" v-if="IS_SUPERUSER">
                        <v-select
                            :items="stores"
                            item-text="name"
                            v-model="storeFilter"
                            item-value="id"
                            label="Склад"
                            disabled
                        />
                    </v-col>
                </v-row>
                <v-data-table
                    class="background-tooman-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    @current-items="getFiltered"
                    :headers="headers"
                    :loading="loading"
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
                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.attributes="{ item }">
                        {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn depressed icon @click="addToCart(item)" color="success">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-btn depressed icon @click="addToCart(item, true)" color="success"  v-if="cart.find(c => c.id === item.id)">
                            <span>+1</span>
                        </v-btn>
                    </template>
                    <template v-slot:item.quantity="{item}">
                        {{ item.quantity - getCartCount(item.id) }}
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
            <v-card-actions class="p-2">
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <!--<v-btn
                    text
                    type="submit"
                    color="success"
                    @click="onSubmit"
                >
                    <b>Редактировать</b>
                    <v-icon>mdi-check</v-icon>
                </v-btn>-->
            </v-card-actions>
        </v-card>
        <ClientCart
            @cancel="clientCartModal = false"
            @onClientChosen="onClientChosen"
            :state="clientCartModal"
        />
    </v-dialog>
</template>

<script>
    import ACTIONS from "@/store/actions";
    import { mapActions } from "vuex";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import cart from "@/mixins/cart";
    import ClientCart from "@/components/Modal/ClientCart";

    export default {
        components: {ClientCart},
        mixins: [
            product, cart, product_search
        ],
        data: () => ({
            clientCartModal: false,
            headers: [
                {
                    text: 'Наименование',
                    value: 'product_name',
                    sortable: false,
                    align: ' fz-18'
                },
                {
                    text: 'Атрибуты',
                    value: 'attributes',
                    align: ' d-none'
                },
                {
                    value: 'manufacturer.manufacturer_name',
                    text: 'Производитель',
                    align: ' d-none'
                },
                {
                    text: 'Остаток',
                    value: 'quantity'
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
            loading: true,
            cart: [],
            discountPercent: 0,
            client: {
                id: -1,
                client_name: 'Гость',
                sale_sum: 0,
                client_balance: 0,
                client_discount: 0,
                total_sum: 0,
            }
        }),
        watch: {
            async state(value) {
                if (value) {
                    this.$loading.enable();
                    console.log(this.report);
                    await this.onInit(this.report.store.id)
                    if (this.report.client.id !== -1) {
                        this.client = this.clients.find(c => c.id === this.report.client.id);
                    }
                    let productIds = this.report.products.map(p => p.product_id);
                    let products = JSON.parse(JSON.stringify(this.productsWithoutFilters))
                        .filter(p => productIds.includes(p.id))
                        .map((p, idx) => {
                            p.quantity += this.report.products[idx].count;
                            return p;
                        });
                    products.forEach((product, idx) => {
                        this.cart.push({
                            ...product,
                            count: this.report.products[idx].count,
                            product_price: product.product_price,
                            discount: this.report.products[idx].discount,
                            uuid: Math.random()
                        })
                    });
                    this.discountPercent = this.report.discount;
                    this.$loading.disable();
                } else {
                    this.cart = [];
                    this.client = {
                        id: -1,
                        client_name: 'Гость',
                        sale_sum: 0,
                        client_balance: 0,
                        client_discount: 0,
                        total_sum: 0,
                    };
                    this.storeFilter = null;
                    this.discountPercent = 0;
                }
            }
        },
        methods: {
            ...mapActions([
                ACTIONS.GET_PRODUCT,
                ACTIONS.GET_CLIENTS,
                ACTIONS.GET_STORES,
            ]),
            cancelClient() {
                this.client = {
                    id: -1,
                    client_name: 'Гость',
                    sale_sum: 0,
                    client_balance: 0,
                    client_discount: 0,
                    total_sum: 0,
                };
            },
            onClientChosen(client) {
                this.clientCartModal = false;
                this.client = client;
            },
            async onSubmit() {
                const sale = {
                    cart: this.cart.map(c => {
                        return {id: c.id, product_price: c.product_price, count: c.count, discount: c.discount};
                    }),
                    client_id: this.client.id,
                    discount: this.discount,
                    id: this.report.id,
                };

                await this.$store.dispatch(ACTIONS.EDIT_SALE_LIST, sale);
                this.$toast.success('Продажа изменена');
                this.$emit('cancel');
            },
            getFiltered(e) {
                if (e.length === 1 && e[0].product_barcode === this.searchQuery) {
                    this.addToCart(e[0], false);
                    this.searchQuery = "";
                    this.searchValue = "";
                }
            },
            async onInit(store_id) {
                await this.$store.dispatch('GET_PRODUCTS_v2');
                await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
                this.storeFilter = store_id;
            }
        },
        computed: {
            finalPrice() {
                let total = this.total;
                return Math.max(0, Math.ceil(total));
            },
            total() {
                return this.subtotal - this.discountTotal;
            },
            discountTotal() {
                return this.cart.reduce((a, c) => {
                    return a + Math.max(this.discount, c.discount) / 100 * c.product_price * c.count;
                }, 0);
            },
            clients() {
                return this.$store.getters.clients;
            },
            discount() {
                return Math.min(Math.max(this.discountPercent, this.client.client_discount, 0), 100);
                /* if (this.isFree) {
                     return 100;
                 }
                 if (!this.client) {
                     return Math.min(this.discountPercent, 100);
                 }
                 return Math.min(Math.max(this.discountPercent, (!this.isRed ? this.client.client_discount : 0), 0), 100);*/
            },
        },
        async mounted() {
            this.$loading.enable();
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
            this.$loading.disable();
            this.loading = false;
        },
        props: {
            state: {
                type: Boolean,
                default: true,
            },
            report: {
                type: Object,
                default: () => ({})
            }
        },
    }
</script>

<style scoped>

</style>
