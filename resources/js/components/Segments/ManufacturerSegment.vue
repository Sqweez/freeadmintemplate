<template>
    <div class="mt-5">
        <v-btn color="error" class="float-right d-block" @click="createMode = true" v-if="!createMode">
            Добавить производителя
            <v-icon>mdi-plus</v-icon>
        </v-btn>
        <br><br>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(manuf, index) of manufacturers" :key="index">
                    <td>
                        <span v-if="!editMode || manufacturer.id !== manuf.id">
                            {{ manuf.manufacturer_name }}
                        </span>
                        <v-text-field
                            v-else
                            label="Наименование"
                            v-model="manufacturer.manufacturer_name"
                        />
                    </td>
                    <td>
                        <div v-if="!editMode || manufacturer.id !== manuf.id">
                            <v-btn icon @click="manufacturer = {...manuf}; editMode = true;">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="manufacturerId = manuf.id; deleteModal = true">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                        <div v-else>
                            <v-btn icon @click="cancelEditing">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                            <v-btn icon @click="editManufacturer">
                                <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </div>
                    </td>
                </tr>
                <tr v-if="createMode">
                    <td>
                        <v-text-field
                            label="Наименование"
                            v-model="manufacturer.manufacturer_name"
                        />
                    </td>
                    <td>
                        <v-btn icon @click="cancelCreation">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn icon @click="createManufacturer">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранного производителя?"
            v-on:cancel="manufacturerId = null; deleteModal = false"
            :on-confirm="deleteManufacturer"
        />
    </div>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import showToast from "../../utils/toast";
    import ConfirmationModal from "../Modal/ConfirmationModal";

    export default {
        components: {
            ConfirmationModal
        },
        data: () => ({
            createMode: false,
            manufacturer: {},
            manufacturerId: null,
            deleteModal: false,
            editMode: false,
        }),
        methods: {
            cancelEditing() {
                this.manufacturer = {};
                this.editMode = false;
            },
            cancelCreation() {
                this.manufacturer = {};
                this.createMode = false;
            },
            async createManufacturer() {
                await this.$store.dispatch(ACTIONS.CREATE_MANUFACTURER, this.manufacturer);
                this.cancelCreation();
                showToast('Производитель успешно добавлен')
            },
            async editManufacturer() {
                await this.$store.dispatch(ACTIONS.EDIT_MANUFACTURER, this.manufacturer);
                this.cancelEditing();
                showToast('Производитель успешно отредактирован')
            },
            async deleteManufacturer() {
                await this.$store.dispatch(ACTIONS.DELETE_MANUFACTURER, this.manufacturerId);
                this.manufacturerId = null;
                this.deleteModal = false;
                showToast('Производитель удален')
            }
        },
        computed: {
            manufacturers() {
                return this.$store.getters.manufacturers;
            }
        }
    }
</script>

<style scoped>

</style>
