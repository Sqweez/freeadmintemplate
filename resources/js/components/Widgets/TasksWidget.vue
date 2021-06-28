<template>
    <div>
        <v-card>
            <v-card-title>
                Задания
            </v-card-title>
            <v-card-text>
                <v-data-table
                    :items-per-page="10"
                    :items="tasks"
                    :headers="headers"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.attachments="{ item }">
                        <ul>
                            <li v-for="i of item.attachments">
                                <a :href="i.link" target="_blank">
                                    {{ i.name }}
                                </a>
                            </li>
                        </ul>
                    </template>
                    <template v-slot:item.is_completed="{item}">
                        <div v-if="item.is_completion_required">
                            <v-icon v-if="item.is_completed" color="success">mdi-check</v-icon>
                            <v-icon v-else color="error">mdi-close</v-icon>
                        </div>
                        <span v-else>Не требуется</span>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-checkbox
                            @change="onTaskChange(item)"
                            v-if="item.is_completion_required"
                            label="Выполнено"
                            v-model="item.is_completed"
                            :disabled="item.is_completed"
                        />
                        <span v-else>
                            Действие не требуется
                        </span>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    export default {
        data: () => ({
            headers: [
                {
                    text: 'Наименование',
                    value: 'title'
                },
                {
                    text: 'Описание',
                    value: 'text'
                },
                {
                    value: 'start_date',
                    text: 'Дата начала'
                },
                {
                    value: 'finish_date',
                    text: 'Дата окончания'
                },
                {
                    value: 'author',
                    text: 'Автор'
                },
                {
                    value: 'attachments',
                    text: 'Вложения'
                },
                {
                    value: 'actions',
                    text: 'Действия'
                },
            ],
        }),
        methods: {
            async onTaskChange(item) {
                await this.$store.dispatch('CHANGE_TASK_STATUS', item.id);
            }
        },
        computed: {
            tasks() {
                return this.$store.getters.CURRENT_TASKS;
            },
            user() {
                return this.$store.getters.USER;
            }
        },
        async created() {
            await this.$store.dispatch('GET_CURRENT_TASKS', this.user.store_id);
        }
    }
</script>

<style scoped>

</style>
