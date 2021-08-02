<template>
    <div>
        <v-card>
            <v-card-title>
                Штрафы/Премии
            </v-card-title>
            <v-card-text>
                <v-btn color="success" @click="shiftPenaltyModal = true" class="mb-3">
                    Добавить штраф/премию
                </v-btn>
                <v-select
                    label="Фильтр по продавцу"
                    :items="sellers"
                    item-value="id"
                    item-text="name"
                    v-model="sellerId"
                />
                <v-data-table
                    :headers="headers"
                    no-data-text="Нет данных"
                    :items="penalties"
                    :items-per-page="10"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.amount="{ item }">
                        {{ item.amount | priceFilters }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            icon
                            text
                            @click="shiftPenaltyModalDelete = true; penaltyId = item.id;"
                            color="error"
                        >
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <ShiftPenaltyModal
            :state="shiftPenaltyModal"
            @cancel="shiftPenaltyModal = false"
        />
        <ConfirmationModal
            :message="'Вы действительно хотите удалить выбранную позицию?'"
            :state="shiftPenaltyModalDelete"
            :on-confirm="deletePenalty"
            @cancel="penaltyId = null; shiftPenaltyModalDelete = false"
        />
    </div>
</template>

<script>
    import ShiftPenaltyModal from '@/components/Modal/ShiftPenaltyModal';
    import ACTIONS from '@/store/actions';
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, ShiftPenaltyModal},
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_SHIFT_PENALTIES);
        },
        data: () => ({
            headers: [
                {
                    value: 'user.name',
                    text: 'Продавец'
                },
                {
                    value: 'amount',
                    text: 'Сумма',
                },
                {
                    value: 'comment',
                    text: 'Комментарий'
                },
                {
                    value: 'author.name',
                    text: 'Автор'
                },
                {
                    value: 'date',
                    text: 'Дата'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ],
            shiftPenaltyModal: false,
            shiftPenaltyModalDelete: false,
            penaltyId: null,
            sellerId: -1,
        }),
        methods: {
            async deletePenalty() {
                await this.$store.dispatch(ACTIONS.DELETE_SHIFT_PENALTY, this.penaltyId);
                this.penaltyId = null;
                this.shiftPenaltyModalDelete = false;
                this.$toast.success('Успешно удалено');
            }
        },
        computed: {
            penalties() {
                return this.$store.getters.PENALTIES.filter(p => {
                    return p.user.id === this.sellerId || this.sellerId === -1;
                })
            },
            sellers() {
                return [
                    {
                        id: -1,
                        name: 'Все'
                    },
                    ...this.$store.getters.USERS_SELLERS
                ];
            }
        }
    }
</script>

<style scoped>

</style>
