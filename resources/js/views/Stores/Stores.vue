<template>
    <v-card>
        <v-card-title>
            Список складов
        </v-card-title>
        <v-card-text>
            <v-btn color="error" @click="storeModal = true">Добавить склад <v-icon>mdi-plus</v-icon></v-btn>
            <h3 class="ml-3">
                Магазины
            </h3>
            <v-row>
                <v-col>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Город</th>
                                <th>Тип</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(store, idx) of shops" :key="idx">
                                <td>{{ store.name }}</td>
                                <td>{{ store.city }}</td>
                                <td>{{ store.type.type }}</td>
                                <td>
                                    <v-btn icon @click="storeId = store.id; storeModal = true;">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn icon @click="confirmationModal = true; storeId = store.id;">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-col>
            </v-row>
            <v-divider />
            <h3 class="ml-3">
                Склады
            </h3>
            <v-row>
                <v-col>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Город</th>
                                <th>Тип</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(store, idx) of warehouses" :key="idx">
                                <td>{{ store.name }}</td>
                                <td>{{ store.city }}</td>
                                <td>{{ store.type.type }}</td>
                                <td>
                                    <v-btn icon @click="storeId = store.id; storeModal = true;">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn icon @click="confirmationModal = true; storeId = store.id;">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-col>
            </v-row>
            <v-divider />
            <h3 class="ml-3">
                Магазины партнеров
            </h3>
            <v-row>
                <v-col>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Город</th>
                                <th>Тип</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(store, idx) of partner_stores" :key="idx">
                                <td>{{ store.name }}</td>
                                <td>{{ store.city }}</td>
                                <td>{{ store.type.type }}</td>
                                <td>
                                    <v-btn icon @click="storeId = store.id; storeModal = true;">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn icon @click="confirmationModal = true; storeId = store.id;">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-col>
            </v-row>
        </v-card-text>
        <ConfirmationModal
            :state="confirmationModal"
            v-on:cancel="confirmationModal = false; storeId = null;"
            message="'Вы действительно хотите удалить выбранный склад?'"
            :on-confirm="deleteStore"
        />
        <StoreModal
            :state="storeModal"
            v-on:cancel="storeModal = false; storeId = null"
            :id="storeId"
            v-on:onSubmit="storeModal = false; storeId = null;"
        />
    </v-card>
</template>

<script>
    import ConfirmationModal from "../../components/Modal/ConfirmationModal";
    import StoreModal from "../../components/Modal/StoreModal";
    import {mapGetters} from 'vuex';
    import ACTIONS from "../../store/actions";
    export default {
        data: () => ({
            storeId: null,
            confirmationModal: false,
            storeModal: false,
        }),
        components: {
            StoreModal,
            ConfirmationModal
        },
        created() {
            this.$store.dispatch(ACTIONS.GET_STORES);
            this.$store.dispatch(ACTIONS.GET_STORE_TYPES);
            this.$store.dispatch(ACTIONS.GET_CITIES);
        },
        computed: {
            ...mapGetters(['shops', 'warehouses', 'partner_stores'])
        },
        methods: {
            async deleteStore() {
                await this.$store.dispatch(ACTIONS.DELETE_STORE, this.storeId);
                this.confirmationModal = false;
                alert('Магазин удален');
                this.storeId = null;
            },
        }
    }
</script>

<style scoped>

</style>
