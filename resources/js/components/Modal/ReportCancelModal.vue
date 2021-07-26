<template>
    <v-dialog
        persistent
        max-width="1000"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span
                    class="white--text">Отмена продажи</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onCancel">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <p>Выберите товар и его количество, которое следует отменить:</p>
                <v-simple-table v-if="!loading">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>Действие</th>
                            <th>Наименование</th>
                            <th>Производитель</th>
                            <th>Параметры</th>
                            <th>Количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, idx) of transfer" :key="idx">
                            <td>
                                <v-checkbox
                                    v-model="item.accepted"
                                />
                            </td>
                            <td>{{ item.product_name }}</td>
                            <td>{{ item.manufacturer.manufacturer_name }}</td>
                            <td>
                                <ul>
                                    <li v-for="(attr, index) of item.attributes" :key="index">
                                        {{ attr }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <v-btn icon color="error" @click="decreaseCount(idx)">
                                    <v-icon>
                                        mdi-minus
                                    </v-icon>
                                </v-btn>
                                {{ item._count }}
                                <v-btn icon color="success" @click="increaseCount(idx)">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-checkbox
                    label="Отменить полностью"
                    v-model="fullCancel"
                />
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
                <v-btn color="success" text  @click="accept" v-if="hasAccepted">
                    Подтвердить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {acceptTransfer, getTransferInfo} from "../../api/transfers";
    import showToast from "../../utils/toastService";
    import {cancelSale} from "../../api/sale";
    import ACTIONS from "../../store/actions";
    import {TOAST_TYPE} from "@/config/consts";

    export default {
        props: {
            state: {
                default: false
            },
            products: {
                default: []
            },
            id: {
                default: null,
            }
        },
        watch: {
            async state() {
                this.transfer = [];
                if (this.state) {
                    this.transfer = [...this.products];
                    this.transfer = this.transfer.map(p => {
                        p.accepted = false;
                        p.initial_count = p.count;
                        p._count = 0;
                        return p;
                    })
                }
            },
            async fullCancel(value) {
                if (value) {
                    this.transfer = this.transfer.map(t => {
                        return {
                            ...t,
                            accepted: true,
                            _count: t.initial_count,
                        };
                    });
                } else {
                    this.transfer = this.transfer.map(p => {
                        p.accepted = false;
                        p.initial_count = p.count;
                        p._count = 0;
                        return p;
                    })
                }
            }
        },
        data: () => ({
            selected: [],
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
                    value: '_count',
                    sortable: false
                }
            ],
            transfer: [],
            fullCancel: false,
        }),
        methods: {
            onCancel() {
                this.$emit('cancel');
            },
            async accept() {
                this.loading = true;
                const canceled = this.transfer.filter(t => t.accepted).map(t => {
                    return {
                        count: t._count,
                        product_id: t.product_id,
                        certificate_id: t.certificate_id,
                    }
                });
                try {
                    await this.$store.dispatch(ACTIONS.CANCEL_SALE, {
                        canceled: canceled,
                        id: this.id,
                    });
                    this.$toast.success('Продажа отменена');
                    this.$emit('confirm');
                } catch (e) {
                    this.$toast.error('При отмене произошла ошибка, перезагрузите страницу!')
                } finally {
                    this.loading = false;
                }
            },
            decreaseCount(idx) {
                const newValue = {
                    ...this.transfer[idx],
                    _count: Math.max(0, this.transfer[idx]._count - 1)
                };
                newValue.accepted = newValue._count > 0;
                this.transfer.splice(idx, 1, newValue)
            },
            increaseCount(idx) {
                const newValue = {
                    ...this.transfer[idx],
                    _count: Math.min(this.transfer[idx].initial_count, this.transfer[idx]._count + 1)
                };
                newValue.accepted = newValue._count > 0;
                this.transfer.splice(idx, 1, newValue)
            },
        },
        computed: {
            hasAccepted() {
                return !!this.transfer.filter(t => t.accepted).length
            }
        }
    }
</script>

<style scoped>

</style>
