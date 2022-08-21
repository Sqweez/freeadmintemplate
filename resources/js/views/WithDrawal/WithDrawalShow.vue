<template>
    <div>
        <v-row>
            <v-col cols="12" xl="4">
                <v-menu
                    ref="startMenu"
                    v-model="startMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="start"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="start"
                            label="Дата начала"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="start"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="startMenu = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate(startMenu, start)"
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
                <v-menu
                    ref="finishMenu"
                    v-model="finishMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    :return-value.sync="finish"
                    transition="scale-transition"
                    min-width="290px"
                    offset-y
                    full-width
                >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="finish"
                            label="Дата окончания"
                            prepend-icon="event"
                            readonly
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="finish"
                        locale="ru"
                        no-title
                        scrollable
                    >
                        <div class="flex-grow-1"></div>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="finishMenu = false"
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            text
                            outlined
                            color="primary"
                            @click="changeCustomDate(finishMenu, finish) "
                        >
                            OK
                        </v-btn>
                    </v-date-picker>
                </v-menu>
            </v-col>
            <v-col cols="12" xl="4">
                <v-select
                    label="Склад"
                    v-model="storeId"
                    :items="$storeFilters"
                    item-value="id"
                    item-text="name"
                />
                <v-select
                    label="Пользователь"
                    v-model="userId"
                    :items="$userFilters"
                    item-value="id"
                    item-text="name"
                />
            </v-col>
            <v-col cols="12" xl="4">
                <v-list>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ totalAmount | priceFilters }}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                Общая сумма
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-col>
        </v-row>
        <v-data-table
            loading-text="Идет загрузка товаров..."
            class="background-iron-grey fz-18"
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :items="filteredItems"
            :headers="headers"
            :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
        >
            <template v-slot:item.amount="{ item }">
                {{ item.amount | priceFilters }}
            </template>
            <template v-slot:item.image="{ item }">
                <a style="width: 300px; height: 300px; overflow: hidden; display: block;" :href="item.image" target="_blank">
                    <img :src="item.image" style="object-fit: contain; object-position: center; width: 100%; height: 100%;">
                </a>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-btn color="error" :disabled="!item.can_delete" @click="id = item.id; showModal = true;">
                    Удалить <v-icon>mdi-close</v-icon>
                </v-btn>
            </template>
        </v-data-table>
        <ConfirmationModal
            :state="showModal"
            :on-confirm="onDelete"
            v-on:cancel="id = null; showModal = false"
            message="Вы действительно хотите удалить выбранное изъятие?"
        />
    </div>
</template>

<script>
import axios from "axios";
import date_mixin from "@/mixins/date_mixin";
import moment from 'moment';
import ConfirmationModal from "@/components/Modal/ConfirmationModal";

export default {
    components: {ConfirmationModal},
    data: () => ({
        id: null,
        showModal: false,
        userId: -1,
        storeId: -1,
        headers: [
            {
                value: 'amount',
                text: 'Сумма'
            },
            {
                value: 'user.name',
                text: 'Пользователь'
            },
            {
                value: 'store.name',
                text: 'Склад'
            },
            {
                value: 'image',
                text: 'Изображение'
            },
            {
                value: 'description',
                text: 'Комментарий'
            },
            {
                value: 'date',
                text: 'Дата'
            },
            {
                value: 'actions',
                text: 'Действие'
            }
        ]
    }),
    computed: {
        items () {
            return this.$store.getters.withdrawals;
        },
        filteredItems () {
            return this.items.filter(i => {
                return this.storeId === -1 ? true : i.store.id === this.storeId;
            }).filter(i => {
                return this.userId === - 1 ? true : i.user.id === this.userId;
            }).filter(i => {
                if (!(this.start && this.finish)) {
                    return true;
                }
                const startDate = moment(this.start);
                const endDate = moment(this.finish);
                const currentDate = moment(i.created_at);
                return currentDate.isSameOrAfter(startDate, 'day') && currentDate.isSameOrBefore(endDate, 'day');
            });
        },
        totalAmount () {
            return this.filteredItems.reduce((a, c) => a + c.amount, 0);
        }
    },
    mixins: [date_mixin],
    methods: {
        async getWithDrawals() {
            try {
                this.$loading.enable();
                const { data: { data } } = await axios.get('/api/v2/with-drawal');
                this.$store.commit('SET_WITHDRAWALS', data);
            } catch (e) {
            } finally {
                this.$loading.disable();
            }
        },
        async onDelete () {
            await axios.delete(`/api/v2/with-drawal/${this.id}`);
            this.$store.commit('DELETE_WITHDRAWAL', this.id);
            this.id = null;
            this.showModal = false;
            this.$toast.success('Изъятие удалено!');
        }
    },
    async mounted() {
        await this.getWithDrawals();
    }
}
</script>

<style scoped lang="scss">

</style>
