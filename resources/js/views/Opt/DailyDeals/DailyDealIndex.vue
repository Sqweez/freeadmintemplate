<template>
    <i-card-page title="Предложения дня">
        <router-link to="/opt/daily-deals/create">
            <v-btn color="success">
                Создать "товары дня"
            </v-btn>
        </router-link>
        <v-data-table
            :headers="headers"
            :items="items"
        >
            <template v-slot:item.is_active="{item}">
                <div v-if="item.is_active">
                    <v-icon color="success">
                        mdi-check
                    </v-icon>
                    <span>
                        &nbsp;({{ item.remaining_time }})
                    </span>
                </div>
                <div v-else>
                    <v-icon color="error">
                        mdi-cancel
                    </v-icon>
                </div>
            </template>
            <template v-slot:item.actions="{ item }">
                <router-link :to="`/opt/daily-deals/${item.id}/update`">
                    <v-btn icon color="primary">
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                </router-link>
            </template>
        </v-data-table>
    </i-card-page>
</template>

<script>
import DailyDealRepository from '@/repositories/DailyDealRepository';

export default {
    data: () => ({
        dailyDealRepository: DailyDealRepository,
        headers: [
            {
                value: 'id',
                text: '#'
            },
            {
                value: 'from_date',
                text: 'Дата начала'
            },
            {
                value: 'to_date',
                text: 'Дата начала'
            },
            {
                value: 'product_count',
                text: 'Количество товаров'
            },
            {
                value: 'is_active',
                text: 'Активна?'
            },
            {
                value: 'actions',
                text: 'Действие'
            }
        ],
        items: [],
    }),
    methods: {},
    computed: {},
    async mounted () {
        try {
            this.$loading.enable();
            const { data } = await this.dailyDealRepository.get();
            this.items = data.deals;
        } catch (e) {
            this.$report(e);
        } finally {
            this.$loading.disable();
        }
    }
};
</script>

<style scoped>

</style>
