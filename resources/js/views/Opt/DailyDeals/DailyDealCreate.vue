<template>
    <div>
        <i-card-page title="Создание товаров дня">
            <v-row>
                <v-col cols="2">
                    <v-text-field label="Дата начала" type="datetime-local" v-model="active_from" dark />
                </v-col>
                <v-col cols="2">
                    <v-text-field label="Дата окончания" type="datetime-local" v-model="active_to" dark background-color="tomato" />
                </v-col>
            </v-row>
            <v-simple-table v-slot:default>
                <thead>
                <tr>
                    <th>
                        Товар
                    </th>
                    <th>
                        Стоимость
                    </th>
                    <th>
                        Скидка
                    </th>
                    <th>
                        Удалить
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) of products" :key="item.product_id">
                    <td>{{ item.name }}</td>
                    <td>
                        <ul v-if="item.wholesale_prices">
                            <li v-for="price of item.wholesale_prices" :key="price.id">
                                {{ price.currency.name }}: {{ $formatPrice(price.discount_price,
                                price.currency.unicode_symbol) }}
                            </li>
                        </ul>
                        <div v-if="item.wholesale_prices && item.wholesale_prices.length === 0">
                           <span>
                               Цены не установлены
                           </span>
                        </div>
                    </td>
                    <td>
                        <v-text-field
                            type="number"
                            v-model="item.discount"
                            @keydown.enter="onInputBlur($event.target.value, item, index)"
                            @blur="onInputBlur($event.target.value, item, index)"
                        />
                    </td>
                    <td>
                        <v-btn icon color="error" @click="products.splice(index, 1)">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
            <v-btn block color="success" outlined @click="onCreate">
                Создать
            </v-btn>
        </i-card-page>
        <i-card-page title="Товары" class="mt-4">
            <v-text-field v-model="search" label="Поиск" solo single-line />
            <v-data-table :headers="headers" :items="productsList" :search="search">
                <template v-slot:item.attributes="{item}">
                <span>
                    {{ item.attributes.map(a => a.attribute_value).join(' ') }}
                </span>
                </template>
                <template v-slot:item.product_price="{ item }">
                    <ul v-if="item.wholesale_prices">
                        <li v-for="price of item.wholesale_prices" :key="price.id">
                            {{ price.currency.name }}: {{ $formatPrice(price.price, price.currency.unicode_symbol) }}
                        </li>
                    </ul>
                    <div v-if="item.wholesale_prices && item.wholesale_prices.length === 0">
                           <span>
                               Цены не установлены
                           </span>
                    </div>
                </template>
                <template v-slot:item.actions="{item}">
                    <v-btn icon color="success" @click="pushToProducts(item)">
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </i-card-page>
    </div>
</template>

<script>
import dailyDealRepository from '@/repositories/DailyDealRepository';

export default {
    data: () => ({
        search: '',
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
                align: ''
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
            }
        ],
        products: [],
        active_from: null,
        active_to: null,
        dailyDealRepository: dailyDealRepository,
    }),
    methods: {
        pushToProducts(product) {
            const index = this.products.findIndex(p => p.product_id === product.product_id);
            if (index === -1) {
                this.products.push({
                    name: `${product.manufacturer.manufacturer_name} ${product.product_name} ${product.attributes.map(
                        a => a.attribute_value).join(' ')}`,
                    discount: 0,
                    wholesale_prices: product.wholesale_prices.map(p => ({
                        ...p,
                        discount_price: p.price
                    })),
                    product_id: product.product_id
                });
            }
        },
        onInputBlur(value, item, index) {

            value = parseInt(value);
            if (value === item.discount) {
                return;
            }
            const discount = Math.min(100, Math.max(0, value));
            const newObject = {
                ...item,
                discount,
                wholesale_prices: this.products[index].wholesale_prices.map(price => ({
                    ...price,
                    discount_price: price.price * (1 - discount / 100)
                }))
            };
            this.$set(this.products, index, newObject);
        },
        async onCreate () {
            const payload = {
                active_from: this.active_from,
                active_to: this.active_to,
                products: this.products.map(p => ({
                    product_id: p.product_id,
                    discount: p.discount
                }))
            };

            if (!payload.active_to || !payload.active_from) {
                return this.$toast.error('Заполните даты')
            }

            if (!payload.products.length) {
                return this.$toast.error('Добавьте товары')
            }

            try {
                this.$loading.enable();
                await this.dailyDealRepository.create(payload);
                await this.$router.push('/opt/daily-deals')
            } catch (e) {
                this.$report(e);
            } finally {
                this.$loading.disable();
            }
        },
    },
    computed: {
        productsList() {
            return this.$store.getters.PRODUCTS_v2;
        }
    },
    async mounted() {
        await this.$store.dispatch('GET_PRODUCTS_v2', {
            only_opt: true,
            only_main: true
        });
    }
};
</script>

<style scoped>
input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    filter: invert(1); /* Инвертирует цвета индикатора */
}
</style>
