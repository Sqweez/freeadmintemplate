<template>
    <v-dialog
        persistent
        max-width="1000"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span
                    class="white--text">{{
                    confirmMode
                    ? (editMode ? 'Редактирование поступления' : 'Подтвердите поступление')
                    : 'Информация о поступлении' }}</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <v-simple-table v-if="!loading">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th v-if="confirmMode">Действие</th>
                            <th>Наименование</th>
                            <th>Изображение</th>
                            <th v-if="IS_SUPERUSER">Закуп</th>
                            <th>Количество</th>
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
                                                {{ item.product_name }} <v-badge v-if="item.is_new" content="Новинка" color="error" />
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>
                                <img :src="item.product_image" height="200" alt="">
                            </td>
                            <td v-if="IS_SUPERUSER">
                                <v-text-field
                                    v-if="confirmMode && editMode"
                                    v-model="item.purchase_price"
                                    type="number"
                                />
                                <span v-else>
                                    {{ item.purchase_price }} тнг
                                </span>
                            </td>
                            <td style="min-width: 200px;">

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
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <div
                    class="text-center d-flex align-center justify-center"
                    style="min-height: 300px"
                    v-if="loading">
                    <v-progress-circular
                        indeterminate
                        size="65"
                        color="primary"
                    ></v-progress-circular>
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Закрыть
                </v-btn>
                <v-spacer/>
                <v-btn color="primary" text v-if="confirmMode && hasAccepted && editMode" @click="saveChanges" :disabled="!IS_SUPERUSER || IS_MARKETOLOG">
                    Сохранить изменения
                    <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn color="success" text v-if="confirmMode && hasAccepted && !editMode && !search" @click="accept" :disabled="!IS_SUPERUSER || IS_MARKETOLOG">
                    Подтвердить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {createBatch, changeArrival} from "@/api/arrivals";

    export default {
        props: {
            state: {
                default: false
            },
            confirmMode: {
                type: Boolean,
                default: true
            },
            arrival: {
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
                    this.products = this.arrival.products.map(p => {
                        p.accepted = true;
                        return p;
                    }).filter(p => {
                        if (!this.search) {
                            return p;
                        }
                        return p.product_name.toLowerCase().includes(this.search.toLowerCase())
                    });
                } else {
                    this._arrival = {};
                }
            }
        },
        data: () => ({
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
        }),
        methods: {
            async accept() {
                this.loading = true;
                const products = this.products
                    .filter(p => p.accepted)
                    .map(p => {
                        return {
                            product_id: p.id,
                            count: p.count - p.booking_count,
                            purchase_price: p.purchase_price
                        }
                    }).filter(p => p.count > 0);

                await createBatch({
                    arrival_id: this.arrival.id,
                    products: products
                })

                this.loading = false;
                this.$toast.success('Поступление успешно создано!');
                this.$emit('submit')
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
            async saveChanges() {
                this.loading = true;
                const products = this.products
                    .filter(p => p.accepted)
                    .map(p => {
                        return {
                            product_id: p.id,
                            count: p.count,
                            purchase_price: p.purchase_price
                        }
                    })

                await changeArrival(
                    this.arrival.id,
                    products
                )

                this.loading = false;
                this.$toast.success('Поступление успешно отредактировано!');
                this.$emit('edit')
            }
        },
        computed: {
            hasAccepted() {
                return true;
                //return !!this.transfer.filter(t => t.accepted).length
            }
        }
    }
</script>

<style scoped>

</style>
