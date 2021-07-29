<template>
    <div>
        <v-card>
            <v-card-title>Спортсмены</v-card-title>
            <v-card-text>
                <v-container>
                    <v-btn color="error" @click="sportsmenModal = true">Добавить спортсмена <v-icon>mdi-plus</v-icon></v-btn>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Инстаграм</th>
                                <th>Фото</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(sportsman) of sportsmen" :key="sportsman.id">
                                <td>{{ sportsman.name }}</td>
                                <td>
                                    {{ sportsman.instagram }}
                                </td>
                                <td>
                                    <img :src="'../storage/' + sportsman.image" alt="" width="200" height="auto">
                                </td>
                                <td>
                                    <v-btn icon @click="id = sportsman.id; deleteModal = true">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                    <v-btn icon @click="id = sportsman.id; sportsmenModal = true">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-container>
            </v-card-text>
            <SportsmenModal
                :state="sportsmenModal"
                :id="id"
                @cancel="sportsmenModal = false; id = -1"
            />
            <ConfirmationModal
                :state="deleteModal"
                :on-confirm="deleteSportsmen"
                v-on:cancel="id = null; deleteModal = false"
                message="Вы действительно хотите удалить выбранного клиента?" />
        </v-card>
    </div>
</template>

<script>
    import SportsmenModal from "@/components/Modal/SportsmenModal";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import ACTIONS from "@/store/actions";

    export default {
        data: () => ({
            id: -1,
            deleteModal: false,
            sportsmenModal: false,
        }),
        methods: {
            async deleteSportsmen() {
                await this.$store.dispatch(ACTIONS.DELETE_SPORTSMEN, this.id);
                this.deleteModal = false;
                this.$toast.success('Спортсмен успешно удален!');
            }
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_SPORTSMEN);
        },
        computed: {
            sportsmen() {
                return this.$store.getters.SPORTSMEN;
            }
        },
        components: {
            SportsmenModal,
            ConfirmationModal
        }
    }
</script>

<style scoped>

</style>
