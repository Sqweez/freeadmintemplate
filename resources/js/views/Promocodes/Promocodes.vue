<template>
    <div>
        <v-card>
            <v-card-title>
                Промокоды
            </v-card-title>
            <v-card-text>
                <v-btn color="success" @click="$router.push('/promocodes/create')">
                    Добавить промокод
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-text-field
                    class="mt-2"
                    v-model="search"
                    solo
                    clearable
                    label="Поиск товара"
                    single-line
                    hide-details
                ></v-text-field>
                <v-data-table
                    :search="search"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :items="promocodes"
                    :items-per-page="10"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                    <template v-slot:item.is_active="{item}">
                        <v-icon color="success" v-if="item.is_active">
                            mdi-check
                        </v-icon>
                        <v-icon color="red" v-else>
                            mdi-close
                        </v-icon>
                    </template>
                    <template v-slot:item.partner="{item}">
                        {{ item.partner.client_name }}
                    </template>
                    <template v-slot:item.discount="{item}">
                        <span v-if="item.promocode_type.id === 1">
                            {{ item.discount}}%
                        </span>
                        <span v-if="item.promocode_type.id === 2">
                            {{ item.discount | priceFilters}}
                        </span>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn icon  color="success" @click="promocode_id = item.id; hideModal = true;">
                            <v-icon v-if="item.is_active">
                                mdi-eye-off
                            </v-icon>
                            <v-icon v-else>
                                mdi-eye
                            </v-icon>
                        </v-btn>
<!--                        <v-btn icon color="primary" @click="promocode = item; editMode = true; promocodeModal = true;">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                        </v-btn>-->
                        <v-btn icon color="red" @click="deleteModal = true; promocode_id = item.id">
                            <v-icon>
                                mdi-delete
                            </v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ConfirmationModal
            :state="hideModal"
            :on-confirm="togglePromocodeActivity"
            @cancel="hideModal = false; promocode_id = null;"
            message="Вы действительно хотите изменить активность промокода?"
        />
        <ConfirmationModal
            :state="deleteModal"
            :on-confirm="deletePromocode"
            @cancel="deleteModal = false; promocode_id = null;"
            message="Вы действительно хотите изменить активность промокода?"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal},
        data: () => ({
            search: '',
            hideModal: false,
            deleteModal: false,
            promocode: {},
            promocodeModal: false,
            editMode: false,
            promocode_id: null,
            headers: [
                {
                    value: 'promocode',
                    text: 'Промокод'
                },
                {
                    value: 'partner',
                    text: 'Партнер'
                },
                {
                    value: 'discount',
                    text: 'Скидка'
                },
                {
                    value: 'promocode_type.name',
                    text: 'Тип промокода'
                },
                {
                    value: 'is_active',
                    text: 'Активен'
                },
                {
                    value: 'active_until',
                    text: 'Активен до'
                },
                {
                    value: 'min_total',
                    text: 'Минимальная сумма покупки'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ]
        }),
        methods: {
            async togglePromocodeActivity() {
                this.$loading.enable();
                const promocode = this.promocodes.find(p => p.id === this.promocode_id);
                await this.$store.dispatch('editPromocode', {
                    id: promocode.id,
                    is_active: !promocode.is_active
                })
                this.$loading.disable();
                this.promocode_id = null;
                this.hideModal = false;
            },
            async deletePromocode() {
                this.$loading.enable();
                await this.$store.dispatch('deletePromocode', this.promocode_id);
                this.$loading.disable();
                this.promocode_id = null;
                this.deleteModal = false;
            },
            async onSubmit(promocode) {
                try {
                    this.$loading.enable();
                    if (this.editMode) {
                        const _promocode = {
                            id: promocode.id,
                            client_id: promocode.client_id,
                            discount: promocode.discount,
                            promocode: promocode.promocode,
                            is_active: true,
                            active_until: promocode.active_until,
                            promocode_type_id: promocode.promocode_type_id,
                            min_total: promocode.min_total,
                        }

                        await this.$store.dispatch('editPromocode', _promocode);
                    } else {
                        await this.$store.dispatch('addPromocode', promocode);
                    }
                } catch (e) {
                    this.$toast.error('Произошла ошибка!');
                } finally {
                    this.promocode = {};
                    this.promocodeModal = false;
                    this.editMode = false;
                    this.$loading.disable();
                }
            }
        },
        computed: {
            partners() {
                return this.$store.getters.PARTNERS;
            },
            promocodes() {
                return this.$store.getters.PROMOCODES;
            }
        },
        async created() {
            this.$loading.enable();
            await Promise.all([
                // await this.$store.dispatch(ACTIONS.GET_CLIENTS),
                // await this.$store.dispatch('GET_PRODUCTS_v2'),
                // await this.$store.dispatch(ACTIONS.GET_MANUFACTURERS),
                // await this.$store.dispatch(ACTIONS.GET_CATEGORIES),
                await this.$store.dispatch('getPromocodeTypes'),
                await this.$store.dispatch('getPromocodes')
            ]);
            this.$loading.disable();
        }
    }
</script>

<style scoped>

</style>
