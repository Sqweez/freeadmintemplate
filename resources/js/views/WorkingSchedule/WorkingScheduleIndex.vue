<template>
    <div>
        <i-card-page title="Рабочий график">
            <v-select
                :items="monthsList"
                v-model="selectedMonth"
                label="Выбранный месяц"
                item-value="value"
                item-text="name"
                @change="_onMonthChanged"
            />

            <v-simple-table v-slot:default>
                <thead>
                <tr>
                    <th>
                        Дата <v-icon>mdi-calendar</v-icon>
                    </th>
                    <th>График</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item) of dateRange" :key="item">
                    <td>
                        {{ item }}
                    </td>
                    <td>
                        <div v-if="schedule[item]">
                            <v-chip
                                v-for="(user) of schedule[item]"
                                :key="`${item}-${user.id}`"
                                class="ma-2"
                                color="primary"
                                label
                                close
                                @click:close="_handleDelete(user.id)"
                            >
                                <v-icon left>
                                    mdi-account-circle-outline
                                </v-icon>
                                {{ user.user.name }}
                            </v-chip>
                            <v-btn icon title="Добавить себя в расписание" @click="_onWorkingScheduleCreate(item)">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </div>
                        <div v-else>
                            <v-alert
                                @click="_onWorkingScheduleCreate(item)"
                                style="cursor: pointer;"
                                type="warning"
                                dense
                            >
                                Нет данных, добавьте себя в расписание
                            </v-alert>
                        </div>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </i-card-page>
    </div>
</template>

<script>
import moment from 'moment/moment';
import months from '@/common/enums/months.ru';
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        selectedMonth: null,
        dateRange: [],
        schedule: [],
    }),
    computed: {
        monthsList() {
            const dateStart = moment().add(2, 'month');
            return new Array(2)
                .fill({})
                .map(_ => {
                    return {
                        value: dateStart.subtract(1, 'month').format('YYYY-MM'),
                        name: `${months[+dateStart.get('month')]}, ${dateStart.get('year')}`
                    };
                })
                .reverse();
        },
    },
    methods: {
        $init () {
            this.selectedMonth = this.monthsList.at(0).value;
            this._onMonthChanged(this.selectedMonth);
        },
        async _onMonthChanged (value) {
            await this.getMyWorkingSchedule(value);
        },
        async getMyWorkingSchedule (value) {
            try {
                this.$loading.enable();
                const { data } = await axiosClient.get(`/v3/working-schedule/my?date=${value}`);
                this.dateRange = data.period;
                this.schedule = data.schedule;
            } catch (e) {
                console.log(e.response.data.message);
            } finally {
                this.$loading.disable();
            }
        },
        async _onWorkingScheduleCreate (date) {
            try {
                this.$loading.enable();
                const { data } = await axiosClient.post(`/v3/working-schedule`, {
                    date,
                });
                this.dateRange = data.period;
                this.schedule = data.schedule;
                this.$toast.success('Смена добавлена');
            } catch (e) {
                this.$toast.warning('Что-то пошло не так')
            } finally {
                this.$loading.disable();
            }
        },
        _handleDelete (id) {
            this.$confirm('Вы действительно хотите удалить выбранную смену?')
                .then(async _ => {
                    try {
                        this.$loading.enable();
                        await axiosClient.delete(`/v3/working-schedule/${id}`);
                        await this.getMyWorkingSchedule(this.selectedMonth);
                    } catch (e) {
                        console.log(e);
                    } finally {
                        this.$loading.disable();
                    }
                })
        }
    },
    mounted() {
        this.$init();
    }
}
</script>

<style scoped lang="scss">

</style>
