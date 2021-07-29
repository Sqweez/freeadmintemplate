<template>
    <div>
        <v-card>
            <v-card-title>
                Рейтинг продавцов
            </v-card-title>
            <v-card-text>
                <v-btn color="success" @click="sellerModal = true">
                    Добавить продавца
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>Имя</th>
                        <th>Описание</th>
                        <th>Фотография</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="seller of sellers" :key="seller.id">
                        <td>{{ seller.name }}</td>
                        <td>{{ seller.description }}</td>
                        <td>
                            <img
                                :src="'../storage/' + seller.image"
                                width="150"
                                height="auto"
                                alt="Изображение">
                        </td>
                        <td>
                            <v-btn icon @click="sellerId = seller.id; sellerModal = true">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="sellerId = seller.id; deleteModal = true;">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <h5>Критерии:</h5>
                <v-btn color="success" @click="criteriaModal = true">
                    Добавить критерий <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>Критерий</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item of criteria" :key="item.id">
                        <td>{{ item.criteria }}</td>
                        <td>
                            <v-btn icon @click="criteriaId = item.id; criteriaModal = true">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="criteriaId = item.id; deleteCriteriaModal = true;">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <seller-modal
            :state="sellerModal"
            @cancel="sellerModal = false; sellerId = null"
            :id="sellerId"
        ></seller-modal>
        <confirmation-modal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранного продавца?"
            @cancel="deleteModal = false"
            :on-confirm="deleteSeller"
        />

        <confirmation-modal
            :state="deleteCriteriaModal"
            message="Вы действительно хотите удалить выбранный критерий оценки?"
            @cancel="deleteCriteriaModal = false"
            :on-confirm="deleteCriteria"
        />

        <CriteriaModal
            :state="criteriaModal"
            :id="criteriaId"
            @cancel="criteriaId = null; criteriaModal = false"
        />
    </div>
</template>

<script>
    import SellerModal from "@/components/Modal/SellerModal";
    import ACTIONS from "@/store/actions";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import CriteriaModal from "@/components/Modal/CriteriaModal";

    export default {
        components: {CriteriaModal, ConfirmationModal, SellerModal},
        data: () => ({
            sellerModal: false,
            sellerId: null,
            deleteModal: false,
            criteriaId: null,
            criteriaModal: false,
            deleteCriteriaModal: false,
        }),
        methods: {
            async deleteSeller() {
                await this.$store.dispatch(ACTIONS.DELETE_SELLER, this.sellerId);
                this.sellerId = null;
                this.deleteModal = false;
                this.$toast.success('Продавец удален!');
            },
            async deleteCriteria() {
                await this.$store.dispatch(ACTIONS.DELETE_CRITERIA, this.criteriaId);
                this.criteriaId = null;
                this.deleteCriteriaModal = false;
                this.$toast.success('Критерий удален!');
            }
        },
        computed: {
            sellers() {
                return this.$store.getters.SELLERS;
            },
            criteria() {
                return this.$store.getters.CRITERIA;
            },
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_SELLERS);
            await this.$store.dispatch(ACTIONS.GET_CRITERIA);
        }
    }
</script>

<style scoped>

</style>
