<template>
    <base-modal :state="state" :title="modalTitle" @cancel="$emit('cancel')" :max-width="2000">
        <template #default>
            <v-row v-if="editMode || confirmMode">
                <v-col cols="8"></v-col>
                <v-col cols="4">
                    <v-text-field
                        label="Доставка"
                        v-model="arrival.payment_cost"
                    />
                    <v-text-field
                        :disabled="!editMode"
                        label="Ожидаемое прибытие"
                        v-model="arrival.arrived_at"
                        type="date"
                    />
                    <v-textarea label="Комментарий" rows="3" v-model="arrival.comment"></v-textarea>
                    <v-text-field
                        label="Расчет по курсу"
                        v-model.number="moneyRate"
                        type="number"
                    />
                </v-col>
            </v-row>
            <v-simple-table v-if="!loading">
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th v-if="confirmMode">Действие</th>
                        <th>Наименование</th>
                        <!--                        <th v-if="IS_SUPERUSER">Закуп</th>-->
                        <th>Продажа</th>
                        <th v-if="IS_SUPERUSER">
                            Расчет закупа
                        </th>
                        <th v-if="false">Количество</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, idx) of products" :key="idx">
                        <td v-if="confirmMode">
                            <v-checkbox
                                v-model="item.accepted"
                            />
                        </td>
                        <td>
                            <v-list flat>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.product_name }}
                                            <v-badge v-if="item.is_new" content="Новинка" color="error"/>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            {{ item.attributes.map(a => a.attribute_value).join(', ') }},
                                            {{ item.manufacturer.manufacturer_name }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                            <a :href="item.product_image" class="mt-2"
                               style="margin-top: 10px; width: 200px; height: 200px; display: block;" target="_blank">
                                <img :src="item.product_image" style="object-fit: cover; width: 100%; height: 100%;">
                            </a>
                        </td>
                        <!--                        <td v-if="IS_SUPERUSER">

                                                </td>-->
                        <td>
                            <div v-if="IS_SUPERUSER">
                                <v-text-field
                                    label="Закупочная стоимость"
                                    v-if="confirmMode && editMode"
                                    v-model.number="item.purchase_price"
                                    type="number"
                                />
                                <span v-else>
                                    {{ item.purchase_price | priceFilters }}
                            </span>
                            </div>
                            <div class="d-flex align-center" v-if="IS_SUPERUSER  && (confirmMode || editMode)">
                                <div style="max-width: 200px; min-width: 200px;">
                                    <v-text-field
                                        label="Продажная цена"
                                        :persistent-hint="editMode || confirmMode"
                                        :hint="!(confirmMode || editMode)
                                    ? 'Редактирование НЕ приведет к реальному изменению цены'
                                    : 'Редактирование ИЗМЕНИТ реальные цены товаров'"
                                        v-model.number="item.product_price"
                                        type="number"
                                        @change.passive="onPriceChange(item)"
                                        :error-messages="item.has_error_price ? 'У данных товаров должны совпадать цены' : ''"
                                    />
                                </div>
                                <div class="d-flex flex-column ml-2">
                                    <v-btn small text color="primary"
                                           @click.prevent="item.product_price = item.old_price">
                                        Сброс
                                        <v-icon>mdi-undo</v-icon>
                                    </v-btn>
                                    <v-btn small text v-if="hasSimilarProducts(item)" color="primary" class="ml-2"
                                           @click.prevent="syncProductPrices(item)">
                                        Синхронизировать
                                        <v-icon>mdi-sync</v-icon>
                                    </v-btn>
                                </div>
                            </div>
                            <span v-else>
                                    {{ item.product_price | priceFilters }}
                            </span>
                            <div class="mt-2">
                                <p>Количество:</p>
                                <v-btn icon color="error" @click="decreaseCount(idx)" v-if="confirmMode && editMode">
                                    <v-icon>
                                        mdi-minus
                                    </v-icon>
                                </v-btn>
                                <span>
                                   {{ item.count }} ед.
                                </span>
                                <v-btn icon color="success" @click="increaseCount(idx)" v-if="confirmMode && editMode">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </v-btn>
                                <div v-if="item.booking_count">
                                    <b>Забронировано:</b> {{ item.booking_count }} ед.
                                </div>
                            </div>
                        </td>
                        <td v-if="IS_SUPERUSER">
                            <v-list flat>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ item.purchase_price * moneyRate | priceFilters }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Расчетный закуп по курсу
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ deliverySurcharge | priceFilters }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Наценка за доставку
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ deliverySurcharge + item.purchase_price | priceFilters }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Итоговая закупочная
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                                <span
                                                    :class="$economy.getMarginLevel(item.product_price, deliverySurcharge + item.purchase_price)"
                                                >
                                                    {{
                                                        $economy.getMarginPercentage(item.product_price, deliverySurcharge + item.purchase_price)
                                                    }}%
                                                </span>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Текущая маржинальность
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                                <span
                                                    :class="$economy.getSurchargeLevel(item.product_price, deliverySurcharge + item.purchase_price)"
                                                >
                                                    {{
                                                        $economy.getSurchargePercentage(item.product_price, deliverySurcharge + item.purchase_price)
                                                    }}%
                                                </span>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Текущая наценка
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>

                        </td>
                        <td style="min-width: 200px;" v-if="false">

                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">
                Закрыть
            </v-btn>
            <v-spacer/>
            <v-btn
                color="primary"
                text
                v-if="confirmMode && editMode && IS_SUPERUSER"
                @click="updateArrival"
            >
                Сохранить изменения
                <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
                color="success"
                text
                v-if="confirmMode && hasAccepted && !editMode && !search && IS_SUPERUSER"
                @click="submitArrival"
            >
                Подтвердить
                <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>
import {mapActions} from 'vuex';
import {__deepClone} from '@/utils/helpers';

export default {
    props: {
        state: {
            default: false
        },
        confirmMode: {
            type: Boolean,
            default: true
        },
        arrivalProp: {
            type: Object,
            default: {}
        },
        editMode: {
            type: Boolean,
            default: false
        },
        search: {
            type: String,
            default: '',
        }
    },
    watch: {
        state() {
            if (this.state) {
                this.arrival = __deepClone(this.arrivalProp);
                this.products = this.arrival.products.map(p => {
                    p.accepted = true;
                    p.old_price = p.product_price;
                    p.has_error_price = false;
                    return p;
                }).filter(p => {
                    if (!this.search) {
                        return p;
                    }
                    return p.product_name.toLowerCase().includes(this.search.toLowerCase())
                });
            } else {
                this.arrival = {};
                this.moneyRate = 1;
            }
        }
    },
    data: () => ({
        moneyRate: 1,
        selected: [],
        products: [],
        loading: false,
        headers: [
            {
                text: 'Наименование',
                value: 'product_name',
                sortable: false,
            },
            {
                text: 'Атрибуты',
                value: 'attributes',
                sortable: false,
            },
            {
                text: 'Количество',
                value: 'count',
                sortable: false
            }
        ],
        arrival: {},
    }),
    methods: {
        ...mapActions({
            '$updateArrival': 'updateArrival',
            '$submitArrival': 'submitArrival',
        }),
        async updateArrival() {
            const isPurchasePricesSet = this.products.every(p => p.product_price && p.purchase_price);
            if (!isPurchasePricesSet) {
                return this.$toast.error('Заполните все необходимые данные!');
            }

            const hasProducts = this.products.some(p => p.count > 0);
            if (!hasProducts) {
                return this.$toast.error('В поступлении не осталось товаров!')
            }

            this.products = this.checkPriceMismatches();

            if (this.hasMismatches) {
                return this.$toast.error('Проверьте заполнение таблицы! У вас не совпадают цены.')
            }

            const payload = {
                ...this.arrival,
                products: this.products.map(product => ({
                    purchase_price: product.purchase_price,
                    count: product.count,
                    product_id: product.id,
                    base_product_id: product.base_product_id,
                    product_price: product.product_price,
                })),
            };
            try {
                this.$loading.enable();
                await this.$updateArrival(payload);
                this.$toast.success('Поступление успешно отредактировано!');
                this.$emit('cancel');
            } catch (e) {
                console.log(e);
                this.$toast.error('Произошла ошибка');
            } finally {
                this.$loading.disable();
            }
        },
        async submitArrival() {

            this.products = this.checkPriceMismatches();

            if (this.hasMismatches) {
                return this.$toast.error('Проверьте заполнение таблицы! У вас не совпадают цены.')
            }

            const payload = {
                ...this.arrival,
                products: this.products
                    .filter(p => p.accepted)
                    .map(p => ({
                        product_id: p.id,
                        count: p.count - p.booking_count,
                        purchase_price: p.purchase_price + this.deliverySurcharge,
                        product_price: p.product_price,
                        base_product_id: p.base_product_id
                    }))
                    .filter(p => p.count > 0)
            };

            try {
                this.$loading.enable();
                await this.$submitArrival(payload);
                this.$toast.success('Поступление успешно оприходовано!');
                this.$emit('cancel');
            } catch (e) {
                this.$toast.error('Произошла ошибка!')
            } finally {
                this.$loading.disable();
            }

        },
        decreaseCount(idx) {
            const newValue = {
                ...this.products[idx],
                count: Math.max(0, this.products[idx].count - 1)
            };
            newValue.accepted = newValue.count > 0;
            this.products.splice(idx, 1, newValue)
        },
        increaseCount(idx) {
            const newValue = {
                ...this.products[idx],
                count: this.products[idx].count + 1
            };
            newValue.accepted = newValue.count > 0;
            this.products.splice(idx, 1, newValue)
        },
        onPriceChange(event, product) {
            console.log(event);
            console.log(product);
        },
        hasSimilarProducts(item) {
            return this.products.filter(p => p.base_product_id === item.base_product_id).length > 1;
        },
        checkPriceMismatches() {
            return this.products.map(product => {
                return {
                    ...product,
                    has_error_price: this.hasPriceMismatches(product)
                }
            })
        },
        hasPriceMismatches(item) {
            const baseProductId = item.base_product_id;
            const selfPrice = item.product_price;
            const baseProductsLength = this.products.filter(p => p.base_product_id === baseProductId).length;
            const baseProductAndPriceLength = this.products.filter(p => p.base_product_id === baseProductId && p.product_price === selfPrice).length;
            console.log(baseProductsLength);
            console.log(baseProductAndPriceLength);
            return baseProductsLength !== baseProductAndPriceLength;
        },
        syncProductPrices(item) {
            const baseProductId = item.base_product_id;
            const selfPrice = item.product_price;
            this.products = __deepClone(this.products).map(p => {
                if (p.base_product_id === baseProductId) {
                    p.product_price = selfPrice;
                    p.has_error_price = false;
                }
                return p;
            });
            this.$toast.success('Цены синхронизированы!');
        }
    },
    computed: {
        hasAccepted() {
            return true;
            //return !!this.transfer.filter(t => t.accepted).length
        },
        modalTitle() {
            return this.confirmMode
                ? (this.editMode ? 'Редактирование поступления' : 'Подтверждение поступления')
                : 'Информация о поступлении';
        },
        deliverySurcharge() {
            return Math.ceil(this.arrival.payment_cost / this.products.reduce((a, c) => {
                return a + c.count;
            }, 0));
        },
        hasMismatches() {
            return !this.products.every(p => !p.has_error_price);
        },
    }
}
</script>

<style scoped>

</style>
