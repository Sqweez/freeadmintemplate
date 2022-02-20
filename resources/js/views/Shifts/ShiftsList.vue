<template>
    <div>
        <v-card>
            <v-card-title>
                Список смен
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Период"
                    v-model="currentDate"
                    :items="monthsList"
                    item-text="name"
                    item-value="value"
                />
                <v-simple-table
                    :dense="false"
                    v-slot:default
                >
                    <thead>
                    <tr>
                        <th>Продавец</th>
                        <th>Количество смен / Заработок</th>
                        <th>Сумма продаж / Заработок</th>
                        <th>Подробно</th>
                        <th>Штрафы / Премии</th>
                        <th>Итого</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item of payroll" :key="item.id">
                        <td>
                            {{ item.name}}
                        </td>
                        <td>
                            {{ item.shift_count }} / <b>{{ item.shift_salary | priceFilters }}</b>
                        </td>
                        <td>
                            {{ item.sale_amount | priceFilters }} / {{ item.sale_amount_salary | priceFilters }}
                        </td>
                        <td>
                            <v-list v-if="item.calculations && item.calculations.length > 0">
                                <v-list-item v-for="(i, key) of item.calculations" :key="`calc-${key}`">
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            Общая сумма: <span class="color-text--green font-weight-bold">{{ i.amount | priceFilters }}</span> <br>
                                            Зарплата: <span class="color-text--green font-weight-bold">{{ i.salary | priceFilters }}</span> <br>
                                            Текущий процент: <span class="color-text--green font-weight-bold">{{ i.percent }}%</span>
                                        </v-list-item-title>
                                        <v-list-item-subtitle>
                                            Тип маржинальности: {{ i.margin_type }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                            <p v-else>Нет данных</p>
                        </td>
                        <td>
                            {{ item.shift_penalties_amount | priceFilters }}
                        </td>
                        <td>
                            {{ item.total_salary | priceFilters }}
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <v-simple-table v-slot:default v-if="daysInMonth && shifts.length" class="shift__table">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th v-for="shop of shops">
                            {{ shop.name }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(i, date) of daysInMonth">
                        <td>
                            {{ i }}.{{ chosenMonth }}
                        </td>
                        <td
                            v-for="(item, key) of shops"
                            :class="!shifts[key][date].shifts.length ? 'pa-3' : ''"
                            :style="{backgroundColor: `#272727`, cursor: `pointer`}"
                        >
                            <div v-if="shifts[key][date].shifts.length">
                                <div
                                    class="font-weight-medium text-center pa-3"
                                    v-for="shift of shifts[key][date].shifts"
                                    :style="{backgroundColor: getButtonColor(shift.user)}"
                                    @click="showEditShiftModal(shift)"
                                >
                                    {{ shift.user.name }}
                                </div>
                            </div>
                            <div
                                @click="showShiftCreateModal(item, i)"
                                v-else
                                class="font-weight-medium text-center"
                                style="background-color: #272727; height: 100%;">
                                Нет смены
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <CreateShiftModal
            :state="createShiftModal"
            @cancel="createShiftModal = false"
            @create="createShift"
        />
        <EditShiftModal
            :state="editShiftModal"
            :shift="shift"
            @cancel="editShiftModal = false"
            @create="editShift"
        />
    </div>
</template>

<script>
    import months from '@/common/enums/months.ru';
    import moment from 'moment';
    import ACTIONS from "@/store/actions";
    import CreateShiftModal from "@/components/Modal/CreateShiftModal";
    import EditShiftModal from "@/components/Modal/EditShiftModal";

    export default {
        components: {EditShiftModal, CreateShiftModal},
        data: () => ({
            currentDate: null,
            createShiftModal: false,
            storeId: null,
            date: null,
            shift: {},
            editShiftModal: false,
        }),
        methods: {
            getButtonColor(user) {
                try {
                    if (!user) {
                        return 'tomato';
                    }
                    return this.sellers.find(s => s.id === user.id).color;
                } catch (e) {
                    console.log(user);
                    return 'tomato';
                }

            },
            showShiftCreateModal(store, date) {
                this.storeId = store.id;
                this.date = `${this.currentDate}-${date > 9 ? date : `0${date}`}`
                this.createShiftModal = true;
            },
            async showEditShiftModal(shift) {
                this.shift = {...shift};
                this.editShiftModal = true;
            },
            async createShift(sellerId) {
                const shift = {
                    store_id: this.storeId,
                    user_id: sellerId,
                    date: this.date,
                };
                this.createShiftModal = false;
                try {
                    await this.$store.dispatch(ACTIONS.CREATE_SHIFT, shift);
                    await this.$store.dispatch(ACTIONS.GET_PAYROLL, this.currentDate);
                    await this.$store.dispatch(ACTIONS.GET_SHIFTS, this.currentDate);
                    this.$forceUpdate();
                    this.$toast.success('Смена добавлена!');
                } catch (e) {
                    console.log(e);
                    this.$toast.error('При создании смены произошла ошибка');
                }
            },
            async editShift({sellerId, editMode}) {
                const shift = {
                    shift: this.shift,
                    user_id: sellerId,
                    editMode,
                };

                this.editShiftModal = false;

                try {
                    await this.$store.dispatch(ACTIONS.EDIT_SHIFT, shift);
                    await this.$store.dispatch(ACTIONS.GET_PAYROLL, this.currentDate);
                    await this.$store.dispatch(ACTIONS.GET_SHIFTS, this.currentDate);
                    this.$forceUpdate();
                    this.$toast.success('Смена добавлена!');
                }
                catch (e) {
                    console.log(e);
                    this.$toast.error('При создании смены произошла ошибка');
                }
            }
        },
        async created() {
            this.currentDate = moment().format('YYYY-MM');
        },
        computed: {
            monthsList() {
                const dateStart = moment().add(1, 'month');
                return new Array(6)
                    .fill({})
                    .map(_ => {
                        return {
                            value: dateStart.subtract(1, 'month').format('YYYY-MM'),
                            name: `${months[+dateStart.get('month')]}, ${dateStart.get('year')}`
                        };
                });
            },
            payroll() {
                return this.$store.getters.PAYROLL;
            },
            daysInMonth() {
                return new moment(this.currentDate).daysInMonth() ?? 0;
            },
            chosenMonth() {
                const month = new moment(this.currentDate).get('month') + 1;
                return month > 9 ? month : `0${month}`;
            },
            shops() {
                return this.$store.getters.shops;
            },
            sellers() {
                return [...this.$store.getters.users.map(s => {
                    s.color = `#${this.$color.getRandomColor()}`;
                    return s;
                }), {
                    id: -1,
                    color: '#ff0000'
                }];
            },
            shifts() {
                return this.$store.getters.SHIFTS;
            }
        },
        watch: {
            async currentDate(val) {
                if (val) {
                    await this.$store.dispatch(ACTIONS.GET_PAYROLL, val);
                    await this.$store.dispatch(ACTIONS.GET_SHIFTS, val);
                }
            },
            creatShiftModal(val) {
                if (!val) {
                    this.date = null;
                    this.storeId = null;
                }
            },
            editShiftModal(val) {
                if (!val) {
                    this.shift = {};
                }
            }
        }
    }
</script>

<style scoped lang="scss">
    .shift__table {
        td {
            padding: 0;
        }
    }
</style>
