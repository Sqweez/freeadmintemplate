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
                        confirmMode ? 'Подтвердите перемещение' : 'Информация о перемещении'
                    }}</span>
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
                            <th v-if="canEdit">Действие</th>
                            <th>Наименование</th>
                            <th>Количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, idx) of transfer" :key="idx">
                            <td v-if="canEdit">
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
                                                {{ item.attributes.map(a => a.attribute_value).join(', ') }},
                                                {{ item.manufacturer.manufacturer_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>

                                <v-btn icon color="error" @click="decreaseCount(idx)" v-if="canEdit">
                                    <v-icon>
                                        mdi-minus
                                    </v-icon>
                                </v-btn>
                                {{ item.count }}
                                <v-btn icon color="success" @click="increaseCount(idx)" v-if="canEdit">
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
                <v-btn color="success" text v-if="canEdit && hasAccepted && !search" @click="accept">
                    Подтвердить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {acceptTransfer, getTransferInfo} from "@/api/transfers";

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
        },
        search: {
            type: String,
            default: ''
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
                }).filter(p => {
                    if (!this.search) {
                        return p;
                    }
                    return p.product_name.toLowerCase().includes(this.search.toLowerCase());
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
            const response = await acceptTransfer(accepted, this.id);
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
        },
        canEdit() {
            return (this.IS_SUPERUSER || this.IS_STOREKEEPER) && this.confirmMode;
        }
    }
}
</script>

<style scoped>

</style>
<!--
<template>
    <v-dialog
        v-model="state"
        max-width="1000"
        persistent
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span
                    class="white&#45;&#45;text">{{
                        confirmMode ? 'Подтвердите перемещение' : 'Информация о перемещении'
                    }}</span>
                <v-btn class="float-right" icon text>
                    <v-icon color="white" @click="onCancel">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <v-data-table
                    v-if="!loading"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                    :headers="headers"
                    :items="transfer"
                    :items-per-page="-1"
                    :loading="loading"
                    :search="search"
                    no-data-text="Нет данных"
                    no-results-text="Нет результатов"
                >
                    <template v-slot:item.is_accepted="{item, index}">
                        <v-checkbox
                            v-model="item.accepted"
                        />
                    </template>
                    <template v-slot:item.product="{ item, isMobile }">
                        <v-list flat v-if="!isMobile">
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.product_name }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ item.attributes.map(a => a.attribute_value).join(', ') }},
                                        {{ item.manufacturer.manufacturer_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                        <span v-else>
                            {{ item.product_name }}
                        </span>
                    </template>
                    <template v-slot:item.count="{ item, index }">
                        <div style="width: 100%; height: 100%;">
                            <v-btn v-if="canEdit" color="error" icon @click="decreaseCount(index)">
                                <v-icon>
                                    mdi-minus
                                </v-icon>
                            </v-btn>
                            {{ item.count }}
                            <v-btn v-if="canEdit" color="success" icon @click="increaseCount(index)">
                                <v-icon>
                                    mdi-plus
                                </v-icon>
                            </v-btn>
                        </div>
                    </template>
                    <template v-slot:footer></template>
                </v-data-table>
                <div
                    v-if="loading"
                    class="text-center d-flex align-center justify-center"
                    style="min-height: 300px">
                    <v-progress-circular
                        color="primary"
                        indeterminate
                        size="65"
                    ></v-progress-circular>
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Закрыть
                </v-btn>
                <v-spacer/>
                <v-btn v-if="canEdit && hasAccepted && !search" color="success" text @click="accept">
                    Подтвердить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {acceptTransfer, getTransferInfo} from "@/api/transfers";

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
        },
        search: {
            type: String,
            default: ''
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
                }).filter(p => {
                    if (!this.search) {
                        return p;
                    }
                    return p.product_name.toLowerCase().includes(this.search.toLowerCase());
                });
                this.loading = false;
            }
        },
    },
    data: () => ({
        selected: [],
        loading: true,
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
            await acceptTransfer(accepted, this.id);
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
        },
        canEdit() {
            return this.IS_SUPERUSER && this.confirmMode;
        },
        headers() {
            let headers = [
                {
                    text: 'Товар',
                    value: 'product',
                    sortable: false,
                },
                {
                    text: 'Количество',
                    value: 'count',
                    sortable: false
                }
            ];

            if (this.canEdit) {
                headers.unshift({
                    text: 'Действие',
                    value: 'is_accepted',
                    sortable: false,
                },)
            }

            return headers;
        },
    }
}
</script>

<style scoped>

</style>
-->
