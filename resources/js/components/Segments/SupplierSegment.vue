<template>
    <div class="mt-5">
        <v-btn color="error" class="float-right d-block" @click="createMode = true" v-if="!createMode">
            Добавить поставщика
            <v-icon>mdi-plus</v-icon>
        </v-btn>
        <br><br>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Пользователь</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
               <tr v-for="(supplier, idx) of suppliers" :key="idx">
                    <td>
                        <span v-if="!editMode || newSupplier.id !== supplier.id">{{ supplier.supplier_name }}</span>
                        <v-text-field
                            v-else
                            v-model="newSupplier.supplier_name"
                            label="Наименование категории"
                        />
                    </td>
                    <td>

                       <span v-if="!editMode || newSupplier.id !== supplier.id">{{ supplier.user.name }}</span>
                        <v-select
                            v-else
                            label="Пользователь"
                            :items="users"
                            item-text="name"
                            item-value="id"
                            v-model="newSupplier.user_id"
                        />

                    </td>
                    <td>
                        <div v-if="!editMode || newSupplier.id !== supplier.id">
                            <v-btn icon @click="newSupplier = {...supplier}; editMode = true;">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="supplierId = supplier.id; deleteModal = true">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                        <div v-else>
                            <v-btn icon @click="cancelEditing">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                            <v-btn icon @click="editSupplier">
                                <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </div>
                    </td>
                </tr>
                <tr v-if="createMode">
                    <td>
                        <v-text-field
                            label="Имя поставщика"
                            v-model="newSupplier.supplier_name"
                        />
                    </td>
                    <td>
                        <v-select
                            label="Пользователь"
                            :items="users"
                            item-text="name"
                            item-value="id"
                            v-model="newSupplier.user_id"
                        />
                    </td>
                    <td>
                        <v-btn icon @click="cancelCreation">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn icon @click="createSupplier">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>
        <ConfirmationModal
            :state="deleteModal"
            :on-confirm="deleteSupplier"
            v-on:cancel="supplierId = null; deleteModal = false"
            message="Вы действительно хотите удалить выбранного поставщика?"
        />
    </div>
</template>

<script>
    import {VTextField} from 'vuetify/lib';
    import showToast from "../../utils/toastService";
    import ACTIONS from '../../store/actions'
    import ConfirmationModal from "../Modal/ConfirmationModal";
    import uploadFile from "../../api/upload";

    export default {
        components: {ConfirmationModal},
        async mounted() {
            await this.$store.dispatch(ACTIONS.GET_SUPPLIERS);
            await this.$store.dispatch(ACTIONS.GET_USERS);
        },
        data: () => ({
            newSupplier: {
                supplier_name: '',
                user_id: null,
            },
            createMode: false,
            subcategoryFields: [],
            editMode: false,
            deleteModal: false,
            subcategories: [],
            supplierId: null,
        }),
        computed: {
            categories() {
                return this.$store.getters.categories;
            },
            users() {
                return this.$store.getters.USERS_SUPPLIERS;
            },
            suppliers() {
                return this.$store.getters.SUPPLIERS;
            }
        },
        methods: {
            cancelCreation() {
                this.newCategory = {
                    name: '',
                    subcategories: []
                };
                this.subcategoryFields = [];
                this.createMode = false;
            },
            cancelEditing() {
                this.newSupplier = {
                    supplier_name: '',
                    user_id: null
                };
                this.supplierId = null;
                this.editMode = false;
            },
            async editSupplier() {
                const supplier = {
                    supplier_name: this.newSupplier.supplier_name,
                    user_id: this.newSupplier.user_id
                };
                await this.$store.dispatch(ACTIONS.EDIT_SUPPLIER, {
                    supplier: supplier,
                    id: this.newSupplier.id,
                });
                this.cancelEditing();
                showToast('Поставщик успешно отредактирован')
            },
            async createSupplier() {
                const supplier = {
                    supplier_name: this.newSupplier.supplier_name,
                    user_id: this.newSupplier.user_id,
                };

                await this.$store.dispatch(ACTIONS.CREATE_SUPPLIER, supplier);

                this.cancelCreation();
                showToast('Поставщик успешно создан')
            },
            async deleteSupplier() {
                await this.$store.dispatch(ACTIONS.DELETE_SUPPLIER, this.supplierId);
                this.supplier = null;
                this.deleteModal = false;
                showToast('Поставщик успешно удален')
            },
        }
    }
</script>

<style scoped>

</style>
