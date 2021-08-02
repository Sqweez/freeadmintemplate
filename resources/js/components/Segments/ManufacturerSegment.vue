<template>
    <div class="mt-5">
        <v-btn color="error" class="float-right d-block" @click="createMode = true">
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
                            <v-btn icon @click="manufacturer = {...manuf}; createMode = true;">
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
                </tbody>
            </template>
        </v-simple-table>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранного производителя?"
            v-on:cancel="manufacturerId = null; deleteModal = false"
            :on-confirm="deleteManufacturer"
        />
        <ManufacturerModal
            v-on:cancel="createMode = false; manufacturer = {}"
            :state="createMode"
            :editing_manufacturer="manufacturer"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "../Modal/ConfirmationModal";
    import ManufacturerModal from "../Modal/ManufacturerModal";
    import ACTIONS from "@/store/actions";

    export default {
        components: {
            ManufacturerModal,
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
                this.$toast.success('Производитель успешно добавлен')
            },
            async editManufacturer() {
                await this.$store.dispatch(ACTIONS.EDIT_MANUFACTURER, this.manufacturer);
                this.cancelEditing();
                this.$toast.success('Производитель успешно отредактирован')
            },
            async deleteManufacturer() {
                await this.$store.dispatch(ACTIONS.DELETE_MANUFACTURER, this.manufacturerId);
                this.manufacturerId = null;
                this.deleteModal = false;
                this.$toast.success('Производитель удален')
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
