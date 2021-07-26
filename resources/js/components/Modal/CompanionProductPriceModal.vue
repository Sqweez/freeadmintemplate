<template>
    <v-dialog max-width="600" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Редактирование стоимости</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <h5>Наша стоимость: {{ _product.product_price | priceFilters }}</h5>
                <v-text-field
                    label="Ваша стоимость"
                    v-model="price"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">
                    Сохранить
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        data: () => ({
            price: 0,
            product: null,
        }),
        watch: {
            state(val) {
                if (!val) {
                    this.price = 0;
                } else {
                    this.price = this._product.prices.find(p => p.store_id == this.user.store_id).price ?? 0;
                }
            }
        },
        methods: {
            async onSubmit() {
                const product = {...this._product};
                delete product.id;
                product.price = product.prices.filter(p => p.store_id !== this.user.store_id);
                product.price.push({
                    store_id: this.user.store_id,
                    price: this.price,
                })

                await this.$store.dispatch('EDIT_PRODUCT_v2', {
                    product,
                    id: this._product.product_id,
                });

                this.$toast.success('Цена успешно обновлена');
                this.$emit('cancel');
            }
        },
        computed: {
            _product() {
                return this.$store.getters.PRODUCT_v2;
            },
            user() {
                return this.$store.getters.USER;
            }
        },
        props: {
            state: {
                type: Boolean,
                required: true,
            },
            id: {
                default: null,
            }
        }
    }
</script>

<style scoped>

</style>
