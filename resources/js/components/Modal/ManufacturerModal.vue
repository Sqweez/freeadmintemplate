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
                    <div>
                        <img v-if="manufacturer.manufacturer_img" :src="'../storage/' . manufacturer.manufacturer_img" alt="" width="200" height="200">
                        <div>
                            <v-btn color="primary" @click="chooseFile">Выбрать изображение</v-btn>
                            <input type="file" name="file" ref="fileInput" @change="uploadFile" class="d-none">
                        </div>
                    </div>
                </v-form>
            </v-card-text>
            <v-divider />
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="addManufacturer">
                    Добавить
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import showToast from "../../utils/toast";
    import uploadFile from "../../api/upload";

    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            },
        },
        watch: {
            state() {
                this.manufacturer = {}
            }
        },
        data: () => ({
            manufacturer: {
                manufacturer_name: ''
            }
        }),
        computed: {
        },
        methods: {
            async addManufacturer() {
                await this.$store.dispatch(ACTIONS.CREATE_MANUFACTURER, this.manufacturer);
                showToast('Производитель добавлен');
                this.$emit('cancel');
            },
            chooseFile() {
                    this.$refs.fileInput.click();
            },
            async uploadFile (e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'manufacturers');
                this.manufacturer.manufacturer_img = result.data;
            }
        }
    }
</script>

<style scoped>

</style>
