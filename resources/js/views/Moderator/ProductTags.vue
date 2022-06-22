<template>
    <div>
        <div>
            <v-card v-if="!emptyCart">
                <v-card-title class="justify-space-between">
                    <span>Корзина</span>
                </v-card-title>
                <v-card-text style="padding: 0;">
                    <v-divider></v-divider>
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-header>
                                Товары ({{ cart.length }})
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <v-virtual-scroll :items="cart" height="400" item-height="60">
                                    <template v-slot:default="{ item, index }">
                                        <v-list-item style="border-bottom: 1px solid mintcream">
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    {{ item.product_name }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle>
                                                    {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                                        item.manufacturer.manufacturer_name }}
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-btn color="error" icon @click="deleteFromCart(index)">
                                                    <v-icon>
                                                        mdi-close
                                                    </v-icon>
                                                </v-btn>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </template>
                                </v-virtual-scroll>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>
                    <div class="px-4">
                        <h5>Теги:</h5>
                        <div class="d-flex">
                            <div>
                                <v-chip
                                    v-for="(tag, key) of tags"
                                    :key="key"
                                    class="mr-2 mb-2"
                                    close
                                    link
                                    pill
                                    @click:close="removeTag(key)"
                                >{{ tag.name }}
                                </v-chip>
                                <v-text-field
                                    label="Новый тег"
                                    v-model="newTag"
                                    :append-outer-icon="'mdi-plus'"
                                    @click:append-outer="createTag"
                                />
                            </div>
                        </div>
                        <v-btn color="success" class="px-4 mt-2" @click="onSave">
                            Сохранить <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>
            <v-card class="background-tooman-darkgrey">
                <v-card-title>
                    Товары
                </v-card-title>
                <v-card-text>
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
                        <v-col cols="12" xl="4">
                            <v-autocomplete
                                :items="categories"
                                item-text="name"
                                v-model="categoryId"
                                item-value="id"
                                label="Категория"
                            />
                        </v-col>
                        <v-col cols="12" xl="4" v-if="categoryId !== -1 && subcategories.length > 0">
                            <v-autocomplete
                                :items="subcategories"
                                item-text="subcategory_name"
                                v-model="subcategoryId"
                                item-value="id"
                                label="Подкатегория"
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
                            <v-btn color="success" @click="addToCartAll">
                                Добавить все <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-data-table
                        v-show="!addingToCart"
                        class="background-tooman-grey fz-18"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        @current-items="getFiltered"
                        :headers="headers"
                        :loading="loading"
                        :search="searchQuery"
                        loading-text="Идет загрузка товаров..."
                        :items="products"
                        :items-per-page.sync="itemsPerPage"
                    >
                        <template v-slot:item.tags="{item}">
                            <v-chip class="ml-2" v-for="tag of item.tags" :key="`${item.id}-${tag.name}${tag.id}`"  close
                                    link
                                    pill
                                    @click:close="removeTagProduct(item.product_id, tag)">
                                {{ tag.name }}
                            </v-chip>
                        </template>
                        <template v-slot:item.product_name="{item}">
                            <v-list flat>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.product_name }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{
                                                item.manufacturer.manufacturer_name }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </template>
                        <template v-slot:item.actions="{item}">
                            <v-btn depressed icon @click="addToCart(item)" color="success">
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
            </v-card>
        </div>
    </div>
</template>

<script>
import ACTIONS from "@/store/actions";
import {mapActions} from 'vuex';
import axios from "axios";
import product from "@/mixins/product";
import product_search from "@/mixins/product_search";
import cart from "@/mixins/cart";
import shiftModule from "@/store/modules/shifts";
import store from "@/store";
import MarginTypes from "@/views/Economy/MarginTypes";

export default {
    async created() {
        this.$loading.enable();
        await this.$store.dispatch('GET_MODERATOR_PRODUCTS');
        const store_id = (this.is_admin || this.IS_BOSS) ? null : this.user.store_id;
        await this.$store.dispatch(ACTIONS.GET_STORES, store_id);
        await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
        await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
        this.loading = false;
        this.$loading.disable();
    },
    watch: {
        storeFilter() {
            this.cart = [];
        },
        categoryId: {
            immediate: true,
            handler: function (value) {
                this.subcategoryId = -1;
            }
        }
    },
    mixins: [product, product_search, cart],
    data: () => ({
        newTag: '',
        tags: [],
        addingToCart: false,
        itemsPerPage: 10,
        activeSegment: 'settings',
        showCart: false,
        loading: true,
        cart: [],
        certificate: null,
        used_certificate: null,
        overlay: false,
        currentItems: [],
        currentMarginType: 1,
        marginTypeFilter: -1,
        filtered: [],
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
                text: 'Теги',
                value: 'tags'
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
        ]
    }),
    methods: {
        removeTag (idx) {
            this.tags.splice(idx, 1);
        },
        async removeTagProduct (product_id, tag) {
            await this.$store.dispatch(ACTIONS.REMOVE_TAG, { product_id, tag_id: tag.id });
            this.$toast.success('Тег удален!');
        },
        createTag () {
            if (!this.newTag.length) {
                return false;
            }
            if (this.tags.length > 0 && this.tags.find(t => t.name === this.newTag.trim().toLowerCase())) {
                return this.$toast.error('Данный тег уже существует!')
            }
            this.tags.push({
                name: this.newTag.trim().toLowerCase()
            });

            this.newTag = '';
        },
        ...mapActions([
            ACTIONS.GET_PRODUCTS_v2,
            ACTIONS.GET_CLIENTS,
            ACTIONS.GET_STORES,
        ]),
        async onSave () {
            this.newTag = '';
            const products = this.cart
                .map(c => c.product_id)
            const payload = {
                tags: this.tags,
                products,
            };
            await this.$store.dispatch(ACTIONS.SET_TAGS, payload);
            this.cart = [];
            this.$toast.success('Теги установлены!')
            this.tags = [];
        },
        getFiltered(e) {
            return this.filtered = [...e];
        },
        toggleInput(index) {
            this.$set(this.cart[index], 'inputMode', !this.cart[index].inputMode);
        },
        changeCount(item, index) {
            this.$set(this.cart[index], 'count', Math.min(this.cart[index]._count, item.quantity));
            this.toggleInput(index);
        },
        checkAvailability(item = {}) {
            return !((this.getQuantity(item.quantity) - this.getCartCount(item.id)) === 0);
        },
        addToCartAll() {
            this.addingToCart = true;
            this.itemsPerPage = -1;
            this.$loading.enable();
            setTimeout(() => {
                const oldCart = [...this.cart];
                this.cart = this.filtered.map(product => {
                    return {
                        ...product, count: 1, product_price: product.product_price, discount: 0, uuid: Math.random()
                    }
                });
                this.cart = [...this.cart, ...oldCart];
                this.itemsPerPage = 10;
                this.addingToCart = false;
                this.$loading.disable();
            }, 3000);
        }
    },
    computed: {
        is_admin() {
            return this.$store.getters.IS_ADMIN;
        },
        marginTypes () {
            return this.$store.getters.MARGIN_TYPES;
        },
        marginTypesAll () {
            return [{id: -1, title: 'Все'}, ...this.$store.getters.MARGIN_TYPES];
        },
        products() {
            let products = this.$store.getters.MAIN_MODERATOR_PRODUCTS
            if (this.manufacturerId !== -1) {
                products = products.filter(product => product.manufacturer.id === this.manufacturerId);
            }
            if (this.categoryId !== -1) {
                products = products.filter(product => product.category.id === this.categoryId);
            }
            if (this.marginTypeFilter !== -1) {
                products = products.filter(p => p.margin_type.id === this.marginTypeFilter);
            }
            if (this.subcategoryId !== -1) {
                products = products.filter(product => product.subcategory_id === this.subcategoryId);
            }
            return products.map(p => {
                p.quantity = 10000;
                return p;
            });
        },
        shops() {
            return this.$store.getters.shops.map(shop => {
                return {
                    ...shop,
                    percent: 0
                }
            });
        },
    },
}
</script>

<style lang="scss">
.background-tooman-grey {
    background-color: #444444;
}

.background-tooman-darkgrey {
    background-color: #333333;
}

.fz-18 > tr > td, th {
    font-size: 16px !important;
}

.margin-28 {
    margin-top: 28px;
}

.w-50px {
    width: 50px;
}

.cart__parameters__checkboxes {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-gap: 10px;
    padding: 15px 25px;
}

.cart__parameters {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 10px;
    padding: 15px 25px;
}

.cart__parameters > div:nth-child(2n+1):last-child {
    grid-column: 1 / 3;
}

.client__table-heading {
    padding-bottom: 10px;
}

.product__list {
    background-color: #444444 !important;
}

.split__payment {
    padding: 15px 25px;

    div {
        display: grid;
        grid-template-columns: 200px 1fr;
        grid-gap: 10px;
        align-content: center;
        align-items: center;
    }
}
</style>
