<template>
    <base-modal :state="state" max-width="700" persistent :title="editMode ? 'Редактирование производителя' : 'Создание производителя'">
        <template #default>
            <v-form>
                <v-text-field
                    label="Наименование"
                    v-model="manufacturer.manufacturer_name"
                />
                <div v-if="photoPreview" class="d-flex align-start">
                    <img :src="photoPreview"
                         alt="" height="200">
                    <v-btn color="error" small class="ml-4" icon @click="photoPreview = null; manufacturer.manufacturer_img = null;">
                        <v-icon>
                            mdi-close
                        </v-icon>
                    </v-btn>
                </div>
                <div v-else>
                    <div>
                        <v-btn color="primary" @click="chooseFile">Выбрать изображение</v-btn>
                        <input type="file" name="file" ref="fileInput" @change="uploadFile" class="d-none">
                    </div>
                </div>
                <v-checkbox
                    label="Показывать на главной сайта"
                    v-model="manufacturer.show_on_main"
                />
                <v-textarea
                    label="Описание"
                    v-model="manufacturer.manufacturer_description"></v-textarea>
            </v-form>
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">Отмена</v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="success" @click="addManufacturer">
                {{ editMode ? 'Обновить' : 'Добавить'}}
                <v-icon v-if="!editMode">mdi-plus</v-icon>
                <v-icon v-else>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>
    import uploadFile from "@/api/upload";
    import ACTIONS from "@/store/actions";
    import {__deepClone, createObjectURL, toFormData} from '@/utils/helpers';

    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            editing_manufacturer: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state() {
                this.manufacturer = {};
                if (Object.keys(this.editing_manufacturer).length) {
                    this.manufacturer = __deepClone(this.editing_manufacturer);
                    this.photoPreview = this.manufacturer.manufacturer_img;
                }
            }
        },
        data: () => ({
            manufacturer: {
                manufacturer_name: '',
                manufacturer_description: '',
                manufacturer_img: null,
                show_on_main: false,
            },
            photoPreview: null,
        }),
        computed: {
            editMode() {
                return Object.keys(this.editing_manufacturer).length > 0;
            }
        },
        methods: {
            async addManufacturer() {
                if (!this.editMode) {
                    await this.$store.dispatch(ACTIONS.CREATE_MANUFACTURER, toFormData(this.manufacturer));
                    this.$toast.success('Производитель добавлен');
                } else {
                    if (this.manufacturer.manufacturer_img === this.editing_manufacturer.manufacturer_img) {
                        delete this.manufacturer.manufacturer_img;
                    }
                    await this.$store.dispatch(ACTIONS.EDIT_MANUFACTURER, {
                        payload: toFormData(this.manufacturer), id: this.manufacturer.id
                    });
                    this.$toast.success('Производитель изменен');
                }

                this.$emit('cancel');
            },
            chooseFile() {
                this.$refs.fileInput.click();
            },
            async uploadFile(e) {
                const file = e.target.files[0];
                if (file) {
                    this.manufacturer.manufacturer_img = file;
                    this.photoPreview = createObjectURL(file);
                }
                this.$refs.fileInput.value = null;
            }
        }
    }
</script>

<style scoped>

</style>
