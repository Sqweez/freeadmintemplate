<template>
    <v-dialog persistent max-width="600" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Загрузите файл</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-btn @click="$refs.fileInput.click()" v-if="!excelFile">Загрузить файл</v-btn>
                <h4 class="color-text--green" v-else>Файл успешно загружен!</h4>
                <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', excelFile)">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import uploadFile from "@/api/upload";

    export default {
        data: () => ({
            excelFile: null
        }),
        methods: {
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'excel/revisions');
                this.excelFile = result.data;
            }
        },
        watch: {
            state() {
                this.excelFile = null;
            }
        },
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: false,
            }
        }
    }
</script>

<style scoped>

</style>
