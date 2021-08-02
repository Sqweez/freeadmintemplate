<template>
    <div>
        <v-card>
            <v-card-title>
                Задания
            </v-card-title>
            <v-card-text>
                <v-btn color="success" @click="taskModal = true">
                    Создать задание +
                </v-btn>
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
                        <v-btn icon text @click="task = {...item}; taskModal = true;">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon text color="error" @click="taskId = item.id; deleteModal = true;">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <TaskModal
            :state="taskModal"
            @cancel="taskModal = false;"
            :current-task="task"
        />
        <ConfirmationModal
            message="Вы действительно хотите удалить выбранное задание?"
            :state="deleteModal"
            :on-confirm="deleteTask"
            @cancel="taskId = null; deleteModal = false;"
        />
    </div>
</template>

<script>
    import TaskModal from "@/components/Modal/TaskModal";
    import ACTIONS from "@/store/actions";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, TaskModal},
        data: () => ({
            task: {},
            taskId: null,
            taskModal: false,
            deleteModal: false,
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
                    value: 'store',
                    text: 'Магазин'
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
                    value: 'is_completed',
                    text: 'Выполнено'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ],
        }),
        methods: {
            async deleteTask() {
                await this.$store.dispatch('DELETE_TASK', this.taskId);
                this.$toast.success('Задание успешно удалено!');
                this.taskId = null;
                this.deleteModal = false;
            },
        },
        computed: {
            tasks() {
                return this.$store.getters.TASKS;
            }
        },
        async created() {
            await this.$store.dispatch('GET_TASKS');
        }
    }
</script>

<style scoped>

</style>
