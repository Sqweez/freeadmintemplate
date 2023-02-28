<template>
    <div>
        <i-card-page title="Товарная матрица">
            <v-card class="mb-5 mt-5" v-if="!emptyCart">
                <v-card-text style="padding: 0;">
                    <v-simple-table v-slot:default class="mt-5">
                        <template>
                            <thead class="background-iron-darkgrey fz-18">
                            <tr>
                                <th>#</th>
                                <th>Наименование</th>
                                <th>Количество</th>
                                <th>Стоимость</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody class="background-iron-grey">
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
                                <td>{{ item.product_price | priceFilters }}</td>
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
                            <thead class="background-iron-darkgrey fz-18">
                            <tr>
                                <th class="text-center">Общее количество</th>
                                <th class="text-center">Общая сумма</th>
                                <th class="text-center">Склад</th>
                            </tr>
                            </thead>
                            <tbody class="background-iron-grey fz-18">
                            <tr>
                                <td class="text-center">{{ cartCount }} шт.</td>
                                <td class="text-center">{{ subtotal | priceFilters }}</td>
                                <td class="text-center">
                                    <v-select
                                        disabled
                                        :items="$stores"
                                        item-text="name"
                                        v-model="store_id"
                                        item-value="id"
                                        label="Склад"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                    <div class="background-iron-grey pa-10">
                        <v-btn color="error" block style="font-size: 16px" @click="onSubmit">
                            Обновить товарную матрицу
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>
            <v-card class="background-iron-darkgrey">
                <v-card-title>
                    Товары
                </v-card-title>
                <v-card-text>
                </v-card-text>
                <v-card-text style="padding: 0;">
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
                        <v-col cols="12" xl="6">
                            <v-autocomplete
                                :items="categories"
                                item-text="name"
                                v-model="categoryId"
                                item-value="id"
                                label="Категория"
                            />
                        </v-col>
                        <v-col cols="12" xl="6">
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
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </i-card-page>
    </div>
</template>

<script>
import product from '@/mixins/product';
import product_search from '@/mixins/product_search';
import cart from '@/mixins/cart';
import ACTIONS from '@/store/actions';
import axiosClient from '@/utils/axiosClient';

export default {
    mixins: [product, product_search, cart],
    data: () => ({
        cart: [],
        matrix: null,
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
        store_id: null,
        hideNotInStock: false,
    }),
    computed: {
        id () {
            return this.$route.params.id;
        }
    },
    methods: {
        async onSubmit () {
            const payload = {
                products: this.cart.map(c => ({
                    id: c.id,
                    count: c.count
                })),
            };

            try {
                this.$loading.enable();
                await axiosClient.patch(`/v2/matrix/${this.id}`, payload);
                this.$toast.success('Товарная матрица успешно обновлена');
                await this.$router.push('/matrixes');
            } catch (e) {
                console.log(e.response);
                this.$toast.error(e.response.data.message);
            } finally {
                this.$loading.disable();
            }
        },
        addToCart(item, merge = false) {
            const index = this.cart.findIndex(c => c.id === item.id);
            if (index === -1 || merge) {
                this.cart.push({...item, count: 1, product_price: item.product_price, discount: 0, uuid: Math.random(), initial_price: item.product_price});
            } else {
                this.increaseCartCount(index);
            }
        },
        increaseCartCount(index) {
            this.$set(this.cart[index], 'count', this.cart[index].count + 1);
        },
        decreaseCartCount(index) {
            this.$set(this.cart[index], 'count', Math.max(1, this.cart[index].count - 1))
        },
    },
    async mounted () {
        this.$loading.enable('Пожалуйста, подождите');
        await Promise.all([
            await this.$store.dispatch('GET_PRODUCTS_v2'),
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS),
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES),
            axiosClient.get(`/v2/matrix/${this.id}`)
                .then(response => {
                    this.matrix = response.data.data;
                })
        ]);
        this.matrix.products.forEach(product => {
            const needleProduct = this.products.find(p => p.id === product.id);
            this.cart.push({
                ...needleProduct,
                count: product.count,
                product_price: needleProduct.product_price,
                discount: 0,
                uuid: Math.random(),
                initial_price: needleProduct.product_price
            })
        });
        this.store_id = this.matrix.store.id;
        this.$loading.disable();
    }
}
</script>

<style scoped lang="scss">

</style>
