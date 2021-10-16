<template>
    <v-card>
        <v-card-title>
            Бронирование товаров
        </v-card-title>
        <v-card-text>
            <router-link to="/booking/create" tag="v-btn" btn color="success">
                Добавить <v-icon>mdi-plus</v-icon>
            </router-link>
            <v-data-table
                :headers="headers"
                :items="bookings"
                no-data-text="Нет результатов"
                no-results-text="Нет результатов"
                :items-per-page="10"
                :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
            >
                <template v-slot:item.products="{item}">
                    <v-list>
                        <v-list-item v-for="product of item.products" :key="product.id">
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ product.product_name }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ product.manufacturer }}, {{ product.attributes.join(', ') }}
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
                <template v-slot:item.paid_sum="{item}">
                    {{ item.paid_sum | priceFilters }}
                </template>
                <template v-slot:item.actions="{item}">
                    <v-btn text color="error" @click="bookingId = item.id; deleteModal = true;" v-if="item.is_sold === 0">
                        Удалить
                    </v-btn>
                    <v-btn text color="success" @click="onSaleModal(item)" v-if="item.is_sold === 0">
                        Продать
                    </v-btn>
                </template>
                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                </template>
            </v-data-table>
        </v-card-text>
        <ConfirmationModal
            @cancel="closeDeleteModal"
            :on-confirm="deleteBooking"
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранную бронь?"
        />
    </v-card>
</template>

<script>
    import ACTIONS from "@/store/actions";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    export default {
        components: {ConfirmationModal},
        data: () => ({
            deleteModal: false,
            bookingId: null,
            headers: [
                {
                    text: '№',
                    value: 'id',
                },
                {
                    text: 'Состав',
                    value: 'products'
                },
                {
                    text: 'Магазин',
                    value: 'store.name'
                },
                {
                    text: 'Клиент',
                    value: 'client.client_name'
                },
                {
                    text: 'Продавец',
                    value: 'user.name'
                },
                {
                    text: 'Предоплата',
                    value: 'paid_sum'
                },
                {
                    text: 'Дата создания',
                    value: 'date_create'
                },
                {
                    text: 'Статус',
                    value: 'status'
                },
                {
                    text: 'Действия',
                    value: 'actions'
                }
            ],
        }),
        methods: {
            closeDeleteModal() {
                this.bookingId = null;
                this.deleteModal = false;
            },
            async deleteBooking() {
                try {
                    await this.$store.dispatch(ACTIONS.DELETE_BOOKING, this.bookingId);
                    this.$toast.success('Бронь удалена!');
                } catch {
                    this.$toast.error('Что-то пошло не так');
                } finally {
                    this.closeDeleteModal();
                }
            },
            onSaleModal(booking) {
                if (!booking.can_sold) {
                    return this.$toast.error('Товар из данной брони еще не поступил на склад!')
                }
                return this.$router.push(`/booking/${booking.id}`)
            }
        },
        computed: {
            bookings() {
                return this.$store.state.bookingModule.bookings;
            }
        },
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_BOOKINGS)
        }
    }
</script>

<style scoped>

</style>
