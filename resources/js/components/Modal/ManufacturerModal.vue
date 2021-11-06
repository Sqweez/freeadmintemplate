<template>
    <v-dialog
        v-model="state"
        persistent
        max-width="700"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Добавление производителя</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field
                        label="Наименование"
                        v-model="manufacturer.manufacturer_name"
                    />
                    <div v-if="manufacturer.manufacturer_img">
                        <img :src="'../storage/' + manufacturer.manufacturer_img"
                             alt="" height="200"><br>
                        <v-btn color="error" @click="manufacturer.manufacturer_img = null;">Удалить картинку</v-btn>
                    </div>

                    <div v-else>
                        <div>
                            <v-btn color="primary" @click="chooseFile">Выбрать изображение</v-btn>
                            <input type="file" name="file" ref="fileInput" @change="uploadFile" class="d-none">
                        </div>
                    </div>
                    <v-textarea
                        label="Описание"
                        v-model="manufacturer.manufacturer_description"></v-textarea>
                </v-form>
            </v-card-text>
            <v-divider/>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="addManufacturer">
                    {{ editMode ? 'Обновить' : 'Добавить'}}
                    <v-icon v-if="!editMode">mdi-plus</v-icon>
                    <v-icon v-else>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import uploadFile from "@/api/upload";
    import ACTIONS from "@/store/actions";

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
                    this.manufacturer = JSON.parse(JSON.stringify(this.editing_manufacturer))
                }
            }
        },
        data: () => ({
            manufacturer: {
                manufacturer_name: '',
                manufacturer_description: '',
                manufacturer_img: null
            }
        }),
        computed: {
            editMode() {
                return Object.keys(this.editing_manufacturer).length;
            }
        },
        methods: {
            async addManufacturer() {
                if (!this.editMode) {
                    await this.$store.dispatch(ACTIONS.CREATE_MANUFACTURER, this.manufacturer);
                    this.$toast.success('Производитель добавлен');
                } else {
                    await this.$store.dispatch(ACTIONS.EDIT_MANUFACTURER, this.manufacturer);
                    this.$toast.success('Производитель изменен');
                }

                this.$emit('cancel');
            },
            chooseFile() {
                this.$refs.fileInput.click();
            },
            async uploadFile(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'manufacturers');
                this.manufacturer.manufacturer_img = result.data;
                console.log(123);
            }
        }
    }
</script>

<style scoped>

</style>
