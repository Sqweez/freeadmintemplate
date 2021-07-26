<template>
    <v-dialog
        persistent
        max-width="600"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Спортсмен</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form @submit.prevent="onSubmit" ref="goalForm">
                    <v-text-field
                        label="Имя"
                        v-model="sportsmen.name"
                    ></v-text-field>
                    <v-text-field
                        label="Инстаграм"
                        v-model="sportsmen.instagram"
                    ></v-text-field>
                    <div class="d-flex" v-if="sportsmen.image">
                        <div
                            class="image-container">
                            <button class="delete-image" @click.prevent="deleteImage">&times;</button>
                            <img
                                :src="'../storage/' + sportsmen.image"
                                width="150"
                                alt="Изображение">
                        </div>
                    </div>
                    <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                        Загрузить фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>
                    <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" text type="submit" @click="onSubmit">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import uploadFile, {deleteFile} from "@/api/upload";
    import ACTIONS from "@/store/mutations";

    export default {
        data: () => ({
            sportsmen: {
                name: '',
                instagram: '',
                image: null,
            }
        }),
        methods: {
            async onSubmit() {
                if (this.id == -1) {
                    await this.$store.dispatch(ACTIONS.CREATE_SPORTSMAN, this.sportsmen);
                    this.$toast.success('Спортсмен успешно создан!');
                } else {
                    await this.$store.dispatch(ACTIONS.EDIT_SPORTSMEN, this.sportsmen);
                    this.$toast.success('Спортсмен успешно отредактирован!');
                }
                this.$emit('cancel');
            },
            async deleteImage() {
                await deleteFile(this.sportsmen.image);
                this.sportsmen.image = null;
            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'sportsmen');
                this.sportsmen.image = result.data;
            }
        },
        computed: {},
        watch: {
            state() {
                this.sportsmen = {
                    name: '',
                    instagram: '',
                    image: null,
                }
                if (this.id == -1) {
                    return;
                }

                this.sportsmen = JSON.parse(JSON.stringify(this.$store.getters.SPORTSMAN(this.id)));
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false
            },
            id: {
                type: Number,
                default: -1,
            }
        }
    }
</script>

<style scoped>

</style>
