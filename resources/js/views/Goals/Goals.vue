<template>
    <div>
        <GoalModal
            :state="goalModal"
            @cancel="goalModal = false; goal_id = -1"
            :id="goal_id"
        />
        <ConfirmationModal
            message="Вы действительно хотите удалить выбранную цель?"
            :on-confirm="deleteGoal"
            :state="deleteModal" />
        <v-card class="background-tooman-darkgrey mb-5">
            <v-card-title class="justify-space-between">
                <span>Раздел "Наши цели"</span>
            </v-card-title>
            <v-card-text>
                <v-btn color="error" @click="goalModal = true">
                    Создать цель +
                </v-btn>
                <v-simple-table>
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>Название</th>
                            <th>Категории</th>
                            <th>Описание</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(goal) of goals" :key="goal.id">
                            <td>{{ goal.name }}</td>
                            <td>
                                <ul>
                                    <li v-for="part of goal.parts">
                                        {{ part.name }}
                                    </li>
                                </ul>
                            </td>
                            <td> <ul>
                                <li v-for="part of goal.parts">
                                    {{ part.description }}
                                </li>
                            </ul></td>
                            <td>
                                <v-btn icon @click="goal_id = goal.id; deleteModal = true">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                                <v-btn icon @click="goal_id = goal.id; goalModal = true">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import GoalModal from "@/components/Modal/GoalModal";
    import ACTIONS from "@/store/actions";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, GoalModal},
        data: () => ({
            goalModal: false,
            deleteModal: false,
            goal_id: -1,
        }),
        methods: {
            async deleteGoal() {
                await this.$store.dispatch(ACTIONS.DELETE_GOAL, this.goal_id);
                this.goal_id = -1;
                this.$toast.success('Цель удалена!');
                this.deleteModal = false;
            }
        },
        computed: {
            goals() {
                return this.$store.getters.GOALS;
            }
        },
        async mounted () {
            await this.$store.dispatch('GET_PRODUCTS_v2');
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_GOALS);
        }
    }
</script>

<style scoped lang="scss">
    td:last-child, th:last-child {
        max-width: 100px!important;
    }
</style>
