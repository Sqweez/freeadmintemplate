<template>
    <div>
        <div
            class="text-center d-flex align-center justify-center"
            style="min-height: 651px"
            v-if="loading">
            <v-progress-circular
                indeterminate
                size="65"
                color="primary"
            ></v-progress-circular>
        </div>
        <v-card-text style="padding: 0;" v-if="!emptyCart">
            <v-select
                label="Способ оплаты"
                v-model="payment_type"
                :items="payment_types"
                item-text="name"
                outlined
                class="w-150px"
                item-value="id" />
            <v-textarea
                rows="3"
                auto-grow
                v-model="comment"
                label="Комментарий"
            />
            <v-simple-table v-slot:default class="mt-5">
                <template>
                    <thead class="background-tooman-darkgrey fz-18">
                    <tr>
                        <th>#</th>
                        <th>Наименование</th>
                        <th>Количество</th>
                        <th>Стоимость</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody class="background-tooman-grey">
                    <tr v-for="(item, index) of cart" :key="item.id * 85">
                        <td>{{ index + 1 }}</td>
                        <td><v-list class="product__list" flat>
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
                        </v-list></td>
                        <td class="d-flex align-center">
                            <v-btn icon color="error" @click="decreaseCartCount(index)">
                                <v-icon>mdi-minus</v-icon>
                            </v-btn>
                            <v-text-field
                                v-model.number="item.count"
                                @input="updateCount($event, item)"
                                @change="updateCount($event, item)"
                                style="min-width: 40px; max-width: 40px; text-align: center"
                                type="number"
                            ></v-text-field>
                            <v-btn icon color="success" @click="increaseCartCount(index)">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </td>
                        <td>{{ item.product_price }} ₸</td>
                        <td>
                            <v-btn icon color="error" @click="deleteFromCart(index)">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
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
                        <v-btn depressed icon @click="client = null">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
            <v-simple-table v-slot:default>
                <template>
                    <thead class="background-tooman-darkgrey fz-18">
                    <tr>
                        <th class="text-center">Общее количество</th>
                        <th class="text-center">Общая сумма</th>
                    </tr>
                    </thead>
                    <tbody class="background-tooman-grey fz-18">
                    <tr>
                        <td class="text-center">{{ cartCount }} шт.</td>
                        <!--<td class="text-center">{{ subtotal }} ₸</td>-->
                        <td class="text-center">
                            <v-text-field
                                label="Предоплата"
                                v-model="amount"
                            />
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>

            <div class="background-tooman-grey pa-10">
                <v-btn depressed color="error" block style="font-size: 16px" @click="clientCartModal = true" v-if="!client">
                    Выбрать клиента
                </v-btn>
                <v-btn depressed color="error" block style="font-size: 16px" @click="onSubmit" v-else>
                    Оформить заказ
                </v-btn>
            </div>
        </v-card-text>
        <v-card-text style="padding: 0;" v-if="!loading">
            <v-row>
                <v-col cols="12" xl="8">
                    <v-text-field
                        class="mt-2"
                        v-model="searchValue"
                        @input="searchInput"
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
                    />
                </v-col>
            </v-row>
            <v-data-table
                class="background-tooman-grey fz-18"
                :search="searchQuery"
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :headers="headers"
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
                <template v-slot:item.product_price="{ item }">
                    {{ item.product_price | priceFilters}}
                </template>
                <template v-slot:item.actions="{item}">
                    <v-btn icon @click="addToCart(item)" color="success">
                        <v-icon>mdi-plus</v-icon>
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
        <ClientCart
            v-on:cancel="clientCartModal = false"
            v-on:onClientChosen="onClientChosen"
            :state="clientCartModal"/>
    </div>
</template>

<script>
    import ACTIONS from "@/store/actions";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import cart from "@/mixins/cart";
    import ClientCart from "@/components/Modal/ClientCart";
    import {TOAST_TYPE} from "@/config/consts";

    export default {
        components: {
            ClientCart
        },
        data: () => ({
            payment_type: 0,
            comment: '',
            loading: true,
            amount: 0,
            cart: [],
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
            client: null,
            clientCartModal: false,
        }),
        mixins: [product, product_search, cart],
        methods: {
            onClientChosen(client) {
                this.clientCartModal = false;
                this.client = client;
            },
            async onSubmit() {
                const preorder = {
                    client_id: this.client.id,
                    user_id: this.user.id,
                    store_id: this.storeFilter,
                    payment_type: this.payment_type,
                    status: 0,
                    comment: this.comment,
                    amount: this.amount,
                    products: this.cart.map(c => {
                        return {
                            id: c.id,
                            count: c.count
                        }
                    })
                };

                try {
                    await this.$store.dispatch('CREATE_PREORDER', preorder);
                    this.$toast.success('Предзаказ создан');
                    this.cart = [];
                    this.client = null;
                    this.payment_type = 0;
                    this.comment = '';
                } catch (e) {
                    this.$toast.success('Произошла ошибка');
                    console.log(e);
                }
            }
        },
        computed: {
            products() {
                let products = this.$store.getters.PRODUCTS_v2;
                if (this.manufacturerId !== -1) {
                    products = products.filter(product => product.manufacturer.id === this.manufacturerId);
                }
                if (this.hideNotInStock) {
                    products = products.filter(product => product.quantity > 0);
                }
                if (this.categoryId !== -1) {
                    products = products.filter(product => product.category.id === this.categoryId);
                }
                return products.map(p => {
                    p.quantity = 9999;
                    return p;
                });
            },
            payment_types() {
                return this.$store.getters.payment_types;
            },
            user() {
                return this.$store.getters.USER;
            }
        },
        async created() {
            this.loading = true;
            await this.$store.dispatch('GET_PRODUCTS_v2');
            const store_id = this.IS_SUPERUSER ? null : this.user.store_id;
            await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_CLIENTS);
            this.loading = false;
        },
        watch: {
            amount() {
                this.$nextTick(() => {
                    this.amount = Math.min(this.subtotal, Math.max(0, this.amount));
                });
            }
        }
    }
</script>

<style scoped>

</style>
