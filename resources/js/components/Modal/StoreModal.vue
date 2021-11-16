<template>
    <v-dialog max-width="900" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ id !== null ? 'Редактирование' : 'Добавление' }} склада</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-2">
                    <v-text-field
                        class="mt-3"
                        label="Название"
                        v-model="store.name"
                    />
                    <v-select
                        class="mt-3"
                        label="Тип"
                        :items="store_types"
                        v-model="store.type_id"
                        item-text="type"
                        item-value="id"
                    />
                    <v-autocomplete
                        class="mt-3"
                        label="Город"
                        v-model="store.city_id"
                        item-text="name"
                        item-value="id"
                        no-data-text="Нет данных"
                        :items="cities"
                    />
                    <v-divider />
                    <div v-if="store.type_id === 1">
                        <v-textarea
                            label="Описание"
                            v-model.trim="description"
                        />
                        <v-text-field
                            label="Ссылка на карту"
                            v-model.trim="mapUrl"
                        />
                        <div class="d-flex flex-wrap align-center items-center" v-if="images.length">
                            <div
                                class="image-container"
                                v-for="(image, idx) of images"
                                :key="idx">
                                <button class="delete-image" @click.prevent="deleteImage(idx)">&times;</button>
                                <img
                                    :src="'../storage/' + image"
                                    height="150"
                                    alt="Изображение">
                            </div>

                        </div>
                        <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                            Загрузить фото
                            <v-icon>mdi-photo</v-icon>
                        </v-btn>
                        <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                        <v-divider></v-divider>
                        <h5>Продавцы:</h5>
                        <div v-for="(seller, index) of sellers">
                            <div class="d-flex justify-space-between align-center">
                                <v-text-field
                                    label="Имя"
                                    v-model="seller.name"
                                    type="text"
                                />
                                <v-btn color="error" class="ml-5" icon @click="deleteSeller(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </div>
                            <v-textarea
                                label="Доп данные"
                                v-model="seller.description"
                                type="text"
                            />
                            <img :src="`../storage/${seller.image}`" alt="" height="150" v-if="seller.image">
                            <input type="file" :data-index="index" class="d-none" ref="sellerFileInput" @change="uploadSellerPhoto">
                            <v-btn text class="mt-3" @click="$refs.sellerFileInput[index].click()">
                                Загрузить фото
                                <v-icon>mdi-photo</v-icon>
                            </v-btn>
                        </div>
                        <v-btn color="success" @click="sellers.push({name: '', photo: null, description: ''})">
                            Добавить продавца +
                        </v-btn>
                    </div>
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
                    <b>{{ this.id !== null ? 'Сохранить' : 'Создать' }}</b>
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
    import ACTIONS from "@/store/actions";
    import uploadFile, {deleteFile} from "@/api/upload";

    export default {
        watch: {
            state() {
                this.store = {};
                if (this.id !== null) {
                    this.store = {...this.$store.getters.store(this.id)};
                    if (this.store.etc) {
                        this.description = this.store.etc.description;
                        this.mapUrl = this.store.etc.mapUrl;
                        this.sellers = this.store.etc.sellers ?? [];
                        this.images = this.store.etc.images;
                    }
                }

                if (!this.state) {
                    this.description = '';
                    this.mapUrl = '';
                    this.sellers = [];
                    this.images = [];
                }
            }
        },
        computed: {
            store_types() {
                return this.$store.getters.store_types;
            },
            cities() {
                return this.$store.getters.cities;
            }
        },
        data: () => ({
            loading: false,
            store: {},
            description: '',
            mapUrl: '',
            sellers: [],
            images: [],
        }),
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
        methods: {
            deleteSeller(index) {
                this.sellers.splice(index, 1);
            },
            async uploadSellerPhoto(e) {
                const index = +e.target.dataset.index;
                const result = await this.$file.upload(e.target.files[0], 'sellers');
                this.$set(this.sellers, index, {...this.sellers[index], image: result.data});
            },
            async deleteImage(key) {
                await this.$file.remove(this.images[key]);
                this.images.splice(key, 1);
            },
            async uploadPhoto(e) {
                const result = await this.$file.upload(e.target.files[0], 'stores');
                this.images.push(result.data);
            },
            async createStore() {
                if (this.store.type_id === 1) {
                    this.store.etc = {
                        description: this.description,
                        sellers: this.sellers,
                        images: this.images,
                        mapUrl: this.mapUrl
                    };
                }
                await this.$store.dispatch(ACTIONS.CREATE_STORE, this.store);
                this.$toast.success('Склад создан')
            },
            async editStore() {
                delete this.store.type;
                if (this.store.type_id === 1) {
                    this.store.etc = {
                        description: this.description,
                        sellers: this.sellers,
                        images: this.images,
                        mapUrl: this.mapUrl
                    };
                }
                await this.$store.dispatch(ACTIONS.EDIT_STORE, this.store);
                this.$toast.success('Склад отредактирован')

            },
            async submit() {
                if (this.id !== null) {
                    await this.editStore();
                } else {
                    await this.createStore();
                }
                this.$emit('onSubmit')
            }
        }
    }
</script>

