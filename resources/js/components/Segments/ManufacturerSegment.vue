<template>
    <div class="mt-5">
        <v-btn color="error" @click="showManufacturerModal = true">
            Добавить производителя
            <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-text-field
            class="my-4"
            v-model="search"
            solo
            clearable
            label="Поиск"
            single-line
            hide-details
        ></v-text-field>
        <v-data-table
            :search="search"
            :headers="headers"
            :items="manufacturers"
        >
            <template v-slot:item.manufacturer_img="{ item }">
                <div style="width: 200px; height: 200px;" v-if="item.manufacturer_img">
                    <img :src="item.manufacturer_img" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <span v-else>-</span>
            </template>
            <template v-slot:item.show_on_main="{item}">
                <v-icon color="success" v-if="item.show_on_main">
                    mdi-check
                </v-icon>
                <v-icon color="error" v-else>
                    mdi-close
                </v-icon>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-btn icon @click="manufacturer = item; showManufacturerModal = true;">
                    <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon @click="manufacturerId = item.id; deleteModal = true">
                    <v-icon>mdi-delete</v-icon>
                </v-btn>
            </template>
        </v-data-table>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранного производителя?"
            v-on:cancel="manufacturerId = null; deleteModal = false"
            :on-confirm="deleteManufacturer"
        />
        <ManufacturerModal
            @cancel="showManufacturerModal = false; manufacturer = {}"
            :state="showManufacturerModal"
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
            headers: [
                {
                    value: 'manufacturer_name',
                    text: 'Наименование'
                },
                {
                    value: 'manufacturer_img',
                    text: 'Изображение'
                },
                {
                    value: 'show_on_main',
                    text: 'На главной'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ],
            search: '',
            showManufacturerModal: false,
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
