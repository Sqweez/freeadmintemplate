<template>
    <div>
        <v-card class="background-tooman-darkgrey mb-5 mt-5" v-if="!emptyCart">
            <v-card-text style="padding: 0;">
                <v-checkbox
                    label="Под консигнацию"
                    v-model="isConsignment"
                />
                <v-simple-table v-slot:default class="mt-5">
                    <template>
                        <thead class="background-tooman-darkgrey fz-18">
                        <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Количество</th>
                            <th>Стоимость</th>
                            <th>Скидка</th>
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
                                <v-text-field
                                    type="number"
                                    v-model="item.discount"
                                    @input="updateDiscount(item)"
                                    suffix="%"
                                    @change="updateDiscount(item)"
                                />
                            </td>
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
                        <thead class="background-tooman-darkgrey fz-18">
                        <tr>
                            <th class="text-center">Общее количество</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Скидка</th>
                            <th class="text-center">Баланс партнера</th>
                            <th>Итого</th>
                            <th class="text-center">Склад</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey fz-18">
                        <tr>
                            <td class="text-center">{{ cartCount }} шт.</td>
                            <td class="text-center">{{ subtotal | priceFilters }}</td>
                            <td class="text-center">
                                <v-text-field
                                    type="number"
                                    v-model="discount"
                                    label="Скидка"
                                />
                            </td>
                            <td class="text-center">
                                {{ partnerBalance | priceFilters }}
                            </td>
                            <td class="text-center">
                                {{ (total) | priceFilters }}
                            </td>
                            <td class="text-center">
                                <v-select
                                    :items="partner_stores"
                                    item-text="name"
                                    v-model="child_store"
                                    item-value="id"
                                    label="Склад"
                                />
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div class="background-tooman-grey pa-10">
                    <v-btn color="error" block style="font-size: 16px" @click="onTransfer">
                        Создать
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
        <v-card class="background-tooman-darkgrey">
            <v-card-title>
                Товары
            </v-card-title>
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
                    <v-col cols="12" xl="4">
                        <v-select
                            :items="_stores"
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
        </v-card>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <ConfirmationModal
            :state="confirmationModal"
            message="Сформировать накладную?"
            :on-confirm="getWayBill"
            @cancel="cart = []; confirmationModal = false;"
        />
        <WayBillModal
            :state="wayBillModal"
            v-on:cancel="wayBillModal = false"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import WayBillModal from "@/components/Modal/WayBillModal";
    import ACTIONS from "@/store/actions";
    import axios from 'axios';
    import uploadFile, {deleteFile} from "@/api/upload";
    import product from "@/mixins/product";
    import product_search from "@/mixins/product_search";
    import cart from "@/mixins/cart";

    export default {
        components: {
            ConfirmationModal,
            WayBillModal
        },
        watch: {
            storeFilter() {
                this.cart = [];
            },
            discount(val) {
                this.$nextTick(() => {
                    this.discount = Math.max(0, Math.min(100, val));
                })
            }
        },
        data: () => ({
            cart: [],
            search: '',
            isConsignment: false,
            confirmationModal: false,
            wayBillModal: false,
            child_store: null,
            overlay: false,
            loading: false,
            discount: 0,
            photos: [],
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
            ]
        }),
        async mounted() {
            this.loading = this.products.length === 0;
            await this.$store.dispatch('GET_PRODUCTS_v2');
            await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS);
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            this.loading = false;
        },
        mixins: [product, product_search, cart],
        methods: {
            updateDiscount(item) {
                this.$nextTick(() => {
                    const index = this.cart.findIndex(c => c.uuid === item.uuid);
                    this.$set(this.cart[index], 'discount', Math.max(0, Math.min(100, item.discount)));
                });
            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'transfers');
                this.photos.push(result.data);
            },
            async deleteImage(key) {
                await deleteFile(this.photos[key]);
                this.photos.splice(key, 1);
            },
            async onTransfer() {
                this.overlay = true;

                const sale = {
                    cart: this.cart.map(c => {
                        return {id: c.id, count: c.count, discount: c.discount};
                    }),
                    parent_store_id: this.storeFilter,
                    user_id: this.user.id,
                    child_store_id: this.child_store,
                    photos: JSON.stringify(this.photos),
                    discount: this.discount,
                    is_consignment: this.isConsignment,
                };

                await this.$store.dispatch(ACTIONS.MAKE_TRANSFER, sale);
                await this.$store.dispatch('GET_PRODUCTS_QUANTITIES', this.storeFilter);
                this.overlay = false;

                this.confirmationModal = true;

                this.$toast.success('Перемещение создано успешно!');
                //this.cart = [];
            },
            async getWayBill() {
                this.confirmationModal = false;
                const {data} = await axios.post('/api/excel/transfer/waybill', {
                    child_store: this.child_store,
                    parent_store: this.storeFilter,
                    cart: this.cart,
                });

                const link = document.createElement('a');
                link.href = data.path;
                link.click();
                this.cart = [];
            },
        },
        computed: {
            _stores() {
                return [...this.$store.getters.shops, ...this.$store.getters.warehouses];
            },
            partner_stores() {
                return this.$store.getters.partner_stores;
            },
            partnerBalance() {
                return this.child_store ? this.partner_stores.find(p => p.id === this.child_store).balance : 0;
            },
            discountTotal() {
                return this.cart.reduce((a, c) => {
                    return a + Math.max(this.discount, c.discount) /100 * c.product_price * c.count;
                }, 0);
            },
            total() {
                return this.subtotal - this.discountTotal;
            },
        }
    }
</script>

<style scoped lang="scss">
    * {
    }

    h5 {
        color: #fff;
        font-weight: 300;
        font-size: 18px;
    }

    .top-button {
        width: 340px;
    }

    .background-tooman-grey {
        background-color: #444444;
    }

    .background-tooman-darkgrey {
        background-color: #333333;
    }

    .margin-28 {
        margin-top: 28px;
    }

    .fz-18 th, td {
        font-size: 18px !important;
    }

    .v-data-table {
        font-size: 18px !important;
    }

    .image-container {
        img {
            object-fit: contain;
            object-position: center;
        }

        position: relative;

        .delete-image {
            padding: 8px 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            position: absolute;
            right: 14px;
            top: 14px;
            font-size: 2rem;
            border: none;
            transition: .3s;

            &:hover {
                background-color: rgba(255, 255, 255, 0.6);
            }
        }
    }
</style>
