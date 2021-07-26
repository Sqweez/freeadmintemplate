<template>
    <v-dialog
        persistent
        max-width="1000"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span
                    class="white--text">{{ confirmMode ? 'Одобрите перемещение' : 'Информация о перемещении' }}</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onCancel">
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
                            <th>Количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, idx) of transfer" :key="idx">
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
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }}, {{ item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>

                                <v-btn icon color="error" @click="decreaseCount(idx)" v-if="confirmMode">
                                    <v-icon>
                                        mdi-minus
                                    </v-icon>
                                </v-btn>
                                {{ item.count }}
                                <v-btn icon color="success" @click="increaseCount(idx)" v-if="confirmMode">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </v-btn>
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
                <v-btn color="success" text v-if="confirmMode && hasAccepted" @click="accept">
                    Подтвердить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {acceptTransferFromSeller, getTransferInfo} from "@/api/transfers";

    export default {
        props: {
            state: {
                default: false
            },
            id: {
                default: null
            },
            confirmMode: {
                default: false,
            }
        },
        watch: {
            async state() {
                this.loading = true;
                if (this.state === false) {
                    this.transfer = [];
                } else {
                    const transfer = await getTransferInfo(this.id);
                    this.transfer = transfer.products.map(p => {
                        p.accepted = true;
                        p.initial_count = p.count;
                        return p;
                    });
                    this.loading = false;
                }
            },
        },
        data: () => ({
            selected: [],
            loading: true,
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
            transfer: [],
        }),
        methods: {
            onCancel() {
                this.$emit('cancel');
            },
            async accept() {
                this.loading = true;
                const accepted = this.transfer
                    .filter(t => t.accepted)
                    .map(t => {
                        return {
                            count: t.count,
                            product_id: t.product_id,
                        };
                    });
                const response = await acceptTransferFromSeller(accepted, this.id);
                this.loading = false;
                this.$toast.success('Перемещение подтверждено!');
                this.$emit('confirmed');
            },
            decreaseCount(idx) {
                const newValue = {
                    ...this.transfer[idx],
                    count: Math.max(0, this.transfer[idx].count - 1)
                };
                newValue.accepted = newValue.count > 0;
                this.transfer.splice(idx, 1, newValue)
            },
            increaseCount(idx) {
                const newValue = {
                    ...this.transfer[idx],
                    count: Math.min(this.transfer[idx].initial_count, this.transfer[idx].count + 1)
                };
                newValue.accepted = newValue.count > 0;
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
