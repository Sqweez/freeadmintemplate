<template>
    <v-dialog
        v-model="state"
        persistent
        max-width="700"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Добавление количества товара</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field
                        label="Закупочная стоимость"
                        type="number"
                        v-model.number="batch.purchase_price"
                    />
                    <v-text-field
                        label="Количество"
                        type="number"
                        v-model.number="batch.quantity"
                    />
                    <v-select
                        label="Склад"
                        :items="stores"
                        item-value="id"
                        item-text="name"
                        v-model="batch.store_id"
                    />
                </v-form>
            </v-card-text>
            <v-divider />
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="addProductRange">
                    Добавить
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import showToast from "../../utils/toast";

    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                type: Number,
                required: true,
                default: -1,
            }
        },
        watch: {
            state() {
                this.batch = {
                    quantity: 0,
                    purchase_price: 0,
                    store_id: null,
                };
            }
        },
        data: () => ({
            batch: {
                quantity: 0,
                purchase_price: 0,
                store_id: null,
            },
        }),
        computed: {
            stores() {
                return this.$store.getters.stores;
            }
        },
        methods: {
            async addProductRange() {
                this.batch.product_id = this.id;
                await this.$store.dispatch(ACTIONS.ADD_PRODUCT_QUANTITY, this.batch);
                showToast('Поставка добавлена');
                this.$emit('cancel');
            }
        }
    }
</script>

<style scoped>

</style>
