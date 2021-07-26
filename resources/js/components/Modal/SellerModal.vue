<template>
    <v-dialog max-width="900" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ id != null ? 'Редактирование' : 'Добавление' }} продавца</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-2">
                    <v-text-field label="Имя" v-model.trim="seller.name" autofocus/>
                    <v-textarea label="Описание" v-model="seller.description" class="w-250"></v-textarea>
                    <div class="d-flex" v-if="seller.image">
                        <div
                            class="image-container">
                            <button class="delete-image" @click.prevent="deleteImage">&times;</button>
                            <img
                                :src="'../storage/' + seller.image"
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
            <v-card-actions class="p-2" v-if="!loading">
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    type="submit"
                    color="success"
                    @click="submit"
                >
                    <b>Создать</b>
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
            <v-progress-linear
                indeterminate
                :active="loading"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
        </v-card>
    </v-dialog>
</template>

<script>
    import uploadFile, {deleteFile} from "@/api/upload";
    import ACTIONS from "@/store/actions";

    export default {
        data: () => ({
            seller: {
                name: '',
                description: '',
                image: null
            },
            loading: false
        }),
        watch: {
            state() {
                if (this.id == null) {
                    this.seller = {
                        name: '',
                        description: '',
                        image: null
                    }
                } else {
                    this.seller = JSON.parse(JSON.stringify(this.$store.getters.SELLERS.find(s => s.id == this.id)));
                }
            }
        },
        methods: {
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'sellers');
                this.seller.image = result.data;
            },
            async deleteImage() {
                await deleteFile(this.seller.image);
                this.seller.image = null;
            },
            async createSeller() {
                await this.$store.dispatch(ACTIONS.CREATE_SELLER, this.seller);
            },
            async editSeller() {
                await this.$store.dispatch(ACTIONS.EDIT_SELLER, this.seller);
            },
            async submit() {
                if (this.id == null) {
                    await this.createSeller();
                } else {
                    await this.editSeller();
                }
                this.$emit('cancel');
            }
        },
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: true,
            },
            id: {
                type: Number,
                default: null,
            }
        },
    }
</script>

<style scoped>

</style>
