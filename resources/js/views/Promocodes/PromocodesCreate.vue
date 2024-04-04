<template>
    <div>
        <i-card-page title="Создание промокода">
            <v-select
                v-model="promocode.promocode_apply_type_id"
                :items="applyTypes"
                item-text="name"
                item-value="id"
                label="Способ применения промокода"
            />
            <v-text-field
                v-model="promocode.title"
                label="Название"
                type="text"
            />
            <v-text-field
                v-if="promocode.promocode_apply_type_id === 1"
                v-model="promocode.promocode"
                label="Промокод"
                type="text"
            />
            <v-autocomplete
                v-if="promocode.promocode_apply_type_id === 1"
                v-model="promocode.client_id"
                :items="partners"
                item-text="client_name"
                item-value="id"
                label="Партнер"
            />
            <v-autocomplete
                class="mb-2"
                label="Применимо в магазинах:"
                v-model="promocode.available_stores"
                multiple
                :items="$stores"
                item-text="name"
                item-value="id"
                hint="Если промокод может применяться в любом магазине, оставьте это значение пустым"
                persistent-hint
            />
            <v-select
                label="Применимо к клиентам:"
                v-model="promocode.apply_to_clients_id"
                :items="[
                    {id: 1, name: 'Все клиенты'},
                    {id: 2, name: 'Выбранные клиенты'},
                 ]"
                item-text="name"
                item-value="id"
            />
            <v-text-field
                v-model="promocode.total_use_quantity"
                label="Общее количество применений"
                type="number"
                hint="Если не ограничено, оставьте пустым"
                persistent-hint
                class="my-2"
            />
            <v-text-field
                v-model="promocode.per_client_use_quantity"
                label="Общее количество применений для каждого клиента"
                type="number"
                hint="Если не ограничено, оставьте пустым"
                persistent-hint
                class="my-2"
            />
            <v-select
                v-model="promocode.promocode_type_id"
                :items="types"
                item-text="name"
                item-value="id"
                label="Тип промокода"
            />
            <div v-show="promocode.promocode_type_id === 3">
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header color="pink darken-2">
                            Подарок&nbsp;&nbsp;<span><v-icon>mdi-gift</v-icon></span>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
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
                                    <tr v-for="(item, index) of freeProducts">
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <v-list class="product__list" flat>
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title>
                                                            {{ item.product_name }}
                                                        </v-list-item-title>
                                                        <v-list-item-subtitle>
                                                            {{ item.manufacturer.manufacturer_name }} |
                                                            {{ item.category.category_name }} | {{
                                                                item.attributes.map(a => a.attribute_value).join(', ')
                                                            }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </td>
                                        <td>
                                            {{ item.product_price | priceFilters }}
                                        </td>
                                        <td>
                                            {{ item.count }} шт.
                                        </td>
                                        <td>
                                            <v-btn color="error" icon @click="freeProducts.splice(index, 1)">
                                                <v-icon>mdi-close</v-icon>
                                            </v-btn>
                                        </td>
                                    </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                            <v-text-field
                                v-model="searchInput"
                                class="mt-2"
                                clearable
                                hide-details
                                label="Поиск товара"
                                single-line
                                solo
                            ></v-text-field>
                            <v-row class="d-flex align-center">
                                <v-col cols="12" xl="4">
                                    <v-autocomplete
                                        v-model="categoryId"
                                        :items="categoriesFilters"
                                        item-text="name"
                                        item-value="id"
                                        label="Категория"
                                    />
                                </v-col>
                                <v-col cols="12" xl="4">
                                    <v-autocomplete
                                        v-model="manufacturerId"
                                        :items="manufacturersFilters"
                                        item-text="manufacturer_name"
                                        item-value="id"
                                        label="Бренд"
                                    />
                                </v-col>
                            </v-row>
                            <v-data-table
                                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                                :headers="product_headers"
                                :items="filteredProducts"
                                :no-data-text="!search.length ? 'Начните что-нибудь искать 🤖' : 'Нет результатов 🥲'"
                                class="background-iron-grey fz-18"
                                loading-text="Идет загрузка товаров..."
                                :loading="isSearching"
                                no-results-text="Нет результатов"
                            >
                                <template v-slot:item.product_name="{item}">
                                    <v-list flat>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    {{ item.product_name }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle>
                                                    {{ item.manufacturer.manufacturer_name }} |
                                                    {{ item.category.category_name }} |
                                                    {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </template>
                                <template v-slot:item.actions="{item}">
                                    <div class="d-flex py-3"
                                         style="flex-direction: column; justify-content: center; row-gap: 16px;">
                                        <v-btn
                                            color="success"
                                            depressed
                                            small
                                            @click="freeProducts.push(item)"
                                        >
                                            Подарок
                                            <v-icon>mdi-gift</v-icon>
                                        </v-btn>
                                    </div>
                                </template>
                                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                                </template>
                            </v-data-table>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <v-text-field
                v-if="[1, 2, 5].includes(promocode.promocode_type_id)"
                v-model="promocode.discount"
                :label="getDiscountFieldLabel"
                type="number"
            />
            <v-select
                v-if="promocode.promocode_type_id !== 5"
                v-model="promocode.promocode_condition_id"
                :disabled="isCascadePromocodeTypeChosen"
                :items="conditions"
                item-text="name"
                item-value="id"
                label="Условие промокода"
            />
            <v-text-field
                v-if="[2,3,4].includes(promocode.promocode_condition_id)"
                v-model="min_total"
                label="Минимальная сумма покупки"
                type="number"
            />
            <v-autocomplete
                v-if="promocode.promocode_condition_id === 3"
                v-model="conditionalBrandId"
                :items="manufacturers"
                item-text="manufacturer_name"
                item-value="id"
                label="Необходимый бренд"
            />
            <v-autocomplete
                v-if="promocode.promocode_condition_id === 4"
                v-model="conditionalCategoryId"
                :items="categories"
                item-text="name"
                item-value="id"
                label="Необходимая категория"
            />
            <div v-show="promocode.promocode_condition_id === 5">
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header color="blue darken-2">
                            Необходимые к покупке товары
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <div v-if="required_products.length">
                                <h5>Обязательны к покупке:</h5>
                                <v-simple-table v-slot:default>
                                    <template>
                                        <thead class="fz-18">
                                        <tr>
                                            <th>#</th>
                                            <th>Товар</th>
                                            <th>Цена</th>
                                            <th>Количество</th>
                                            <th>Удалить</th>
                                        </tr>
                                        </thead>
                                        <tbody class="background-iron-grey">
                                        <tr v-for="(item, index) of required_products">
                                            <td>{{ index + 1 }}</td>
                                            <td>
                                                <v-list class="product__list" flat>
                                                    <v-list-item>
                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ item.product_name }}
                                                            </v-list-item-title>
                                                            <v-list-item-subtitle>
                                                                {{ item.manufacturer.manufacturer_name }} |
                                                                {{ item.category.category_name }} | {{
                                                                    item.attributes.map(a => a.attribute_value).join(', ')
                                                                }}
                                                            </v-list-item-subtitle>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                </v-list>
                                            </td>
                                            <td>
                                                {{ item.product_price | priceFilters }}
                                            </td>
                                            <td>
                                                {{ item.count }} шт.
                                            </td>
                                            <td>
                                                <v-btn color="error" icon @click="deleteFromRequiredList(index)">
                                                    <v-icon>mdi-close</v-icon>
                                                </v-btn>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </div>
                            <v-text-field
                                v-model="searchInput"
                                class="mt-2"
                                clearable
                                hide-details
                                label="Начните что-нибудь искать 🤖"
                                single-line
                                solo
                            />
                            <v-row class="d-flex align-center">
                                <v-col cols="12" xl="4">
                                    <v-autocomplete
                                        v-model="categoryId"
                                        :items="categoriesFilters"
                                        item-text="name"
                                        item-value="id"
                                        label="Категория"
                                    />
                                </v-col>
                                <v-col cols="12" xl="4">
                                    <v-autocomplete
                                        v-model="manufacturerId"
                                        :items="manufacturersFilters"
                                        item-text="manufacturer_name"
                                        item-value="id"
                                        label="Бренд"
                                    />
                                </v-col>
                            </v-row>
                            <v-data-table
                                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                                :headers="product_headers"
                                :items="filteredProducts"
                                :no-data-text="!search.length ? 'Начните что-нибудь искать 🤖' : 'Нет результатов 🥲'"
                                class="background-iron-grey fz-18"
                                loading-text="Идет загрузка товаров..."
                                :loading="isSearching"
                                no-results-text="Нет результатов"
                            >
                                <template v-slot:item.product_name="{item}">
                                    <v-list flat>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    {{ item.product_name }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle>
                                                    {{ item.manufacturer.manufacturer_name }} |
                                                    {{ item.category.category_name }} |
                                                    {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </template>
                                <template v-slot:item.actions="{item}">
                                    <div class="d-flex py-3"
                                         style="flex-direction: column; justify-content: center; row-gap: 16px;">
                                        <v-btn
                                            color="success"
                                            depressed
                                            small
                                            @click="addToRequiredList(item)"
                                        >
                                            Обязателен к покупке
                                            <v-icon>mdi-check</v-icon>
                                        </v-btn>
                                    </div>
                                </template>
                                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                                </template>
                            </v-data-table>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <v-select
                v-model="promocode.promocode_purpose_id"
                :items="purposes"
                item-text="name"
                item-value="id"
                label="Назначение промокода"
            />
            <div v-show="promocode.promocode_purpose_id === 2">
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header color="blue darken-2">
                            Товары
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <div v-if="purposeProducts.length">
                                <h5>Товары:</h5>
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
                                        <tr v-for="(item, index) of purposeProducts">
                                            <td>{{ index + 1 }}</td>
                                            <td>
                                                <v-list class="product__list" flat>
                                                    <v-list-item>
                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ item.product_name }}
                                                            </v-list-item-title>
                                                            <v-list-item-subtitle>
                                                                {{ item.manufacturer.manufacturer_name }} |
                                                                {{ item.category.category_name }} | {{
                                                                    item.attributes.map(a => a.attribute_value).join(', ')
                                                                }}
                                                            </v-list-item-subtitle>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                </v-list>
                                            </td>
                                            <td>
                                                {{ item.product_price | priceFilters }}
                                            </td>
                                            <td>
                                                <v-btn color="error" icon @click="purposeProducts.splice(index, 1)">
                                                    <v-icon>mdi-close</v-icon>
                                                </v-btn>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </div>
                            <v-text-field
                                v-model="searchInput"
                                class="mt-2"
                                clearable
                                hide-details
                                label="Начните что-нибудь искать 🤖"
                                single-line
                                solo
                            />
                            <v-row class="d-flex align-center">
                                <v-col cols="12" xl="4">
                                    <v-autocomplete
                                        v-model="categoryId"
                                        :items="categoriesFilters"
                                        item-text="name"
                                        item-value="id"
                                        label="Категория"
                                    />
                                </v-col>
                                <v-col cols="12" xl="4">
                                    <v-autocomplete
                                        v-model="manufacturerId"
                                        :items="manufacturersFilters"
                                        item-text="manufacturer_name"
                                        item-value="id"
                                        label="Бренд"
                                    />
                                </v-col>
                            </v-row>
                            <v-data-table
                                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                                :headers="product_headers"
                                :items="filteredProducts"
                                :no-data-text="!search.length ? 'Начните что-нибудь искать 🤖' : 'Нет результатов 🥲'"
                                class="background-iron-grey fz-18"
                                loading-text="Идет загрузка товаров..."
                                :loading="isSearching"
                                no-results-text="Нет результатов"
                            >
                                <template v-slot:item.product_name="{item}">
                                    <v-list flat>
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    {{ item.product_name }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle>
                                                    {{ item.manufacturer.manufacturer_name }} |
                                                    {{ item.category.category_name }} |
                                                    {{ item.attributes.map(a => a.attribute_value).join(', ') }}
                                                </v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </template>
                                <template v-slot:item.actions="{item}">
                                    <div class="d-flex py-3"
                                         style="flex-direction: column; justify-content: center; row-gap: 16px;">
                                        <v-btn
                                            color="success"
                                            depressed
                                            small
                                            @click="addToPurposeList(item)"
                                        >
                                            Добавить в список
                                            <v-icon>mdi-check</v-icon>
                                        </v-btn>
                                    </div>
                                </template>
                                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                                </template>
                            </v-data-table>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <v-autocomplete
                v-if="promocode.promocode_purpose_id === 3"
                v-model="purposeCategories"
                :items="categories"
                item-text="name"
                item-value="id"
                label="Применимо к категориям"
                multiple
            />
            <v-autocomplete
                v-if="promocode.promocode_purpose_id === 4"
                v-model="purposeBrands"
                :items="manufacturers"
                item-text="manufacturer_name"
                item-value="id"
                label="Применимо к брендам"
                multiple
            />
            <div v-if="isCascadePromocodeTypeChosen">
                <v-select
                    v-model="promocode_cascade.type"
                    :items="cascades"
                    item-text="name"
                    item-value="id"
                    label="Каскадный тип"
                />
                <div>
                    <div v-for="(item, index) of promocode_cascade.payload"
                         :key="`cascade-${index}`" class="d-flex align-center align-items-center">
                        <v-text-field
                            v-model="promocode_cascade.payload[index].amount"
                            label="Порог"
                            type="number"
                        />
                        <v-text-field
                            v-model="promocode_cascade.payload[index].discount"
                            label="Скидка"
                            type="number"
                        />
                        <v-btn color="success" icon @click="promocode_cascade.payload.push({amount: 1, discount: 0})">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-btn color="error" icon @click="promocode_cascade.payload.splice(index, 1)">
                            <v-icon>mdi-minus</v-icon>
                        </v-btn>
                    </div>
                </div>
            </div>
            <v-card-actions>
                <v-spacer/>
                <v-btn color="success" text @click="_onSubmit">
                    Сохранить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </i-card-page>
    </div>
</template>

<script>
import ACTIONS from '@/store/actions';
import { __debounce, __hardcoded } from '@/utils/helpers';
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        freeProducts: [],
        categoryId: -1,
        manufacturerId: -1,
        search: '',
        searchInput: '',
        isSearching: false,
        min_total: 0,
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
        promocode: {
            promocode: '',
            promocode_type_id: 1,
            promocode_condition_id: 1,
            promocode_purpose_id: 1,
            client_id: null,
            discount: null,
            promocode_apply_type_id: 1,
            title: '',
            available_stores: [],
            apply_to_clients_id: 1,
            total_use_quantity: null,
            per_client_use_quantity: null,
        },
        conditionalBrandId: null,
        conditionalCategoryId: null,
        purposeBrands: [],
        purposeProducts: [],
        purposeCategories: [],
        promocode_cascade: {
            type: null,
            payload: [
                {
                    amount: 1,
                    discount: 0,
                }
            ],
        },
        required_products: [],
        products: [],
    }),
    async mounted() {
        this.$loading.enable();
        await Promise.all([
            this.$store.dispatch('getPromocodeTypes'),
            this.$store.dispatch(ACTIONS.GET_MANUFACTURERS),
            this.$store.dispatch(ACTIONS.GET_CATEGORIES),
            this.$store.dispatch(ACTIONS.GET_PARTNERS),
        ]);
        this.$loading.disable();
    },
    computed: {
        getDiscountFieldLabel () {
            if ([1, 2].includes(this.promocode.promocode_type_id)) {
                return 'Скидка';
            }
            if ([5].includes(this.promocode.promocode_type_id)) {
                return 'Стоимость набора';
            }
            return 'Неизвестно';
        },
        filteredProducts() {
            let products = this.products;
            if (this.manufacturerId !== -1) {
                products = products.filter(product => product.manufacturer.id === this.manufacturerId);
            }
            if (this.categoryId !== -1) {
                products = products.filter(product => product.category.id === this.categoryId);
            }
            return products;
        },
        isCascadePromocodeTypeChosen() {
            return this.promocode.promocode_type_id === __hardcoded(4);
        },
        types() {
            return this.$store.getters.PROMOCODE_TYPES.types;
        },
        conditions() {
            return this.$store.getters.PROMOCODE_TYPES.conditions;
        },
        purposes() {
            return this.$store.getters.PROMOCODE_TYPES.purposes;
        },
        cascades() {
            return this.$store.getters.PROMOCODE_TYPES.cascades;
        },
        applyTypes () {
            return this.$store.getters.PROMOCODE_TYPES.apply_types;
        },
        partners() {
            return this.$store.getters.PARTNERS;
        },
        manufacturersFilters() {
            return [
                {
                    id: -1,
                    manufacturer_name: 'Все'
                }, ...this.$store.getters.manufacturers];
        },
        manufacturers() {
            return this.$store.getters.manufacturers;
        },
        categoriesFilters() {
            return [
                {
                    id: -1,
                    name: 'Все'
                }, ...this.categories,
            ];
        },
        categories() {
            return this.$store.getters.categories;
        },
    },
    methods: {
        async _onSubmit() {
            if (!this._validateForm()) {
                return false;
            }
            const payload = this._transformPayload();
            this.$loading.enable();
            try {
                await axiosClient.post('promocode', payload);
                this.$toast.success('Промокод создан успешно')
            } catch (e) {
                this.$toast.error('Произошла ошибка при создании промокода')
            } finally {
                this.$loading.disable();
            }
        },
        _validateForm() {
            if (this.promocode.promocode_apply_type_id === __hardcoded(1) && !this.promocode.promocode) {
                return this.$toast.error('Введите значение промокода');
            }

            if (this.promocode.promocode_apply_type_id === __hardcoded(2) && !this.promocode.title) {
                return this.$toast.error('Введите название промокода');
            }

            if ([1, 2].includes(this.promocode.promocode_type_id)) {
                if (!this.promocode.discount) {
                    return this.$toast.error('Укажите значение скидки');
                }

                if (this.promocode.promocode_type_id === 1 && this.promocode.discount > 100) {
                    return this.$toast.error('Значение скидки не должно превышать 100%');
                }
            }

            if ([5].includes(this.promocode.promocode_type_id)) {
                if (!this.promocode.discount) {
                    return this.$toast.error('Укажите стоимость набора');
                }

                if (!this.purposeProducts.length) {
                    return this.$toast.error('Выберите необходимые товары входящие в набор');
                }
            }

            if (this.promocode.promocode_type_id === 3) {
                if (this.freeProducts.length === 0) {
                    return this.$toast.error('Выберите подарок');
                }
            }

            if (this.promocode.promocode_type_id === 4) {
                if (this.promocode_cascade.payload.length === 0 || !this.promocode_cascade.type) {
                    return this.$toast.error('Выберите правила каскадной скидки');
                }
            }

            /*if (this.promocode.promocode_condition_id === 2) {
                if (!this.required_products.length) {
                    return this.$toast.error('Выберите необходимые товары1');
                }
            }*/

            if (this.promocode.promocode_condition_id === 3) {
                if (!this.conditionalBrandId) {
                    return this.$toast.error('Выберите необходимый бренд');
                }
            }

            if (this.promocode.promocode_condition_id === 4) {
                if (!this.conditionalCategoryId) {
                    return this.$toast.error('Выберите необходимую категорию');
                }
            }

            if (this.promocode.promocode_condition_id === 5) {
                if (!this.required_products.length) {
                    return this.$toast.error('Выберите необходимые товары2');
                }
            }

            if (this.promocode.promocode_purpose_id === 2) {
                if (!this.purposeProducts.length) {
                    return this.$toast.error('Выберите необходимые товары3');
                }
            }

            if (this.promocode.promocode_purpose_id === 3) {
                if (!this.purposeCategories.length) {
                    return this.$toast.error('Выберите необходимые категории');
                }
            }

            if (this.promocode.promocode_purpose_id === 4) {
                if (!this.purposeBrands.length) {
                    return this.$toast.error('Выберите необходимые бренды');
                }
            }

            return true;
        },
        _transformPayload() {
            let payload = {
                promocode_type_id: this.promocode.promocode_type_id,
                promocode_purpose_id: this.promocode.promocode_purpose_id,
                promocode_purpose_payload: null,
                promocode_condition_id: this.promocode.promocode_condition_id,
                promocode_condition_payload: null,
                promocode: this.promocode.promocode,
                client_id: this.promocode.client_id,
                discount: null,
                promocode_cascade: null,
                promocode_gifts: null,
                promocode_apply_type_id: this.promocode.promocode_apply_type_id,
                title: this.promocode.title,
                available_stores: this.promocode.available_stores,
                apply_to_clients_id: this.promocode.apply_to_clients_id,
                total_use_quantity: this.promocode.total_use_quantity,
                per_client_use_quantity: this.promocode.per_client_use_quantity,
            };

            if ([1, 2].includes(this.promocode.promocode_type_id)) {
                payload.discount = this.promocode.discount;
            }

            if (this.promocode.promocode_type_id === 3) {
                payload.promocode_gifts = this.freeProducts.map(f => ({
                    id: f.product_id,
                    count: 1,
                }))
            }

            if ([2, 3, 4].includes(this.promocode.promocode_condition_id)) {
                payload.promocode_condition_payload = {
                    min_total: this.min_total,
                };

                if (this.promocode.promocode_condition_id === 3) {
                    payload.promocode_condition_payload.brand_id = this.conditionalBrandId;
                }

                if (this.promocode.promocode_condition_id === 4) {
                    payload.promocode_condition_payload.category_id = this.conditionalCategoryId;
                }
            }

            if ([5].includes(this.promocode.promocode_condition_id)) {
                payload.promocode_condition_payload = {
                    products: this.required_products.map(p => ({
                        id: p.product_id,
                        count: p.count
                    }))
                };
            }

            if (this.promocode.promocode_purpose_id === 2) {
                payload.promocode_purpose_payload = this.purposeProducts.map(p => p.product_id);
            }

            if (this.promocode.promocode_purpose_id === 3) {
                payload.promocode_purpose_payload = this.purposeCategories;
            }

            if (this.promocode.promocode_purpose_id === 4) {
                payload.promocode_purpose_payload = this.purposeBrands;
            }

            if (this.promocode.promocode_type_id === 4) {
                payload.promocode_cascade = {
                    type: this.promocode_cascade.type,
                    payload: this.promocode_cascade.payload,
                };
            }

            return payload;
        },
        addToPurposeList(product) {
            const findIndex = this.purposeProducts.findIndex(p => p.product_id === product.product_id);
            if (findIndex === -1) {
                this.purposeProducts.push({
                    ...product,
                    count: 1
                });
            }
        },
        addToRequiredList(product) {
            const findIndex = this.required_products.findIndex(p => p.product_id === product.product_id);
            if (findIndex === -1) {
                this.required_products.push({
                    ...product,
                    count: 1
                });
            } else {
                this.$set(this.required_products, findIndex, {
                    ...this.required_products[findIndex],
                    count: this.required_products[findIndex].count + 1
                })
            }
        },
        deleteFromRequiredList(index) {
            this.required_products.splice(index, 1);
        },
        async _searchProducts(search) {
            const { data: { data } } = await axiosClient.get(`/v2/products/search/${search}`);
            this.products = data;
        },
    },
    watch: {
        isCascadePromocodeTypeChosen(value) {
            if (value) {
                this.$nextTick(() => {
                    this.promocode.promocode_condition_id = __hardcoded(1);
                })
            }
        },
        searchInput: __debounce(function (newValue) {
            this.search = newValue;
        }),
        async search(value) {
            if (!value || value.length < 3 || this.isSearching) {
                return null;
            }
            this.isSearching = true;
            await this._searchProducts(value);
            this.isSearching = false;
        }
    },
}
</script>

<style lang="scss" scoped>

</style>
