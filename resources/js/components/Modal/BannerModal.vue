<template>
    <div>
        <v-dialog
            v-model="state"
            persistent
            width="800">
            <v-card>
                <v-card-title class="headline d-flex justify-space-between">
                    <span class="white--text">Баннер</span>
                    <v-btn icon text class="float-right">
                        <v-icon color="white" @click="onCancel">
                            mdi-close
                        </v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="modal-text">
                    <h6 class="text-center">Загружать баннеры можно размером не более 1мб!</h6>
                    <h6 class="text-center">Сжать можно через <a href="https://squoosh.app/" target="_blank">SQUOOSH</a></h6>
                    <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                        Загрузить фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>
                    <v-btn text class="mt-3" @click="$refs.fileInputMobile.click()">
                        Загрузить мобильное фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>
                    <img
                        class="d-block"
                        v-if="banner.image"
                        width="400"
                        :src="`../storage/${banner.image}`"
                        alt="">
                    <img
                        class="d-block"
                        v-if="banner.mobile_image"
                        width="400"
                        :src="`../storage/${banner.mobile_image}`"
                        alt="">
                    <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                    <input type="file" class="d-none" ref="fileInputMobile" @change="uploadMobilePhoto">
                    <v-divider></v-divider>
                    <v-text-field
                        label="Описание"
                        v-model="banner.description"
                        persistent-hint
                        hint="Краткое описание для SEO"
                    />
                    <v-text-field
                        label="Порядок"
                        v-model="banner.order"
                        type="number"
                        persistent-hint
                        hint="Если порядок баннера не важен, оставить 0"
                    />
                    <v-checkbox
                        label="Активен"
                        persistent-hint
                        hint="Отвечает за отображение на сайте"
                        v-model="banner.is_active"
                    />
                    <v-select
                        label="Сайт"
                        v-model="banner.website"
                        :items="websites"
                        item-value="value"
                        item-text="title"
                    />
                    <v-autocomplete
                        v-if="!showInEveryCity"
                        label="Показывать в городах"
                        v-model="banner.cities"
                        multiple
                        :items="$cities"
                        item-value="id"
                        item-text="name"
                    />
                    <v-checkbox
                        label="Показывать во всех городах"
                        v-model="showInEveryCity"
                    />
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn text @click="onCancel">
                        Отмена
                    </v-btn>
                    <v-spacer/>
                    <v-btn color="success" text @click="onConfirm">
                        Сохранить
                        <v-icon>mdi-check</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import uploadFile from "../../api/upload";
import axios from 'axios';
import {__deepClone} from "@/utils/helpers";

const DEFAULT_BANNER_STATE = {
    image: null,
    mobile_image: null,
    description: '',
    is_active: true,
    order: 0,
    website: 1,
    cities: [],
};

export default {
        data: () => ({
            banner: DEFAULT_BANNER_STATE,
            showInEveryCity: false,
            websites: [
                {
                    title: 'Основной',
                    value: 1,
                },
                {
                    title: 'IHerb',
                    value: 2
                }
            ]
        }),
        methods: {
            onCancel() {
                this.$emit('cancel');
            },
            _validate (banner) {
                if (!banner.image) {
                    return this.$toast.error('Загрузите баннер для компьютера!')
                }
                if (!banner.mobile_image) {
                    return this.$toast.error('Загрузите мобильный баннер!');
                }
                return true;
            },
            async onConfirm() {
                if (this._validate(this.banner) !== true) {
                    return;
                }
                this.$loading.enable();
                const banner = Object.keys(this._banner).length ? await this.editBanner() : await this.createBanner();
                this.$loading.disable();
                this.$toast.success('Успешно!');
                this.$emit('confirm', banner.data);
            },
            async editBanner() {
                const banner_id = this.banner.id;
                delete this.banner.id;
                if (this.showInEveryCity) {
                    this.banner.cities = null;
                }
                const response = await axios.patch(`/api/shop/banners/${banner_id}`, this.banner);
                return response.data;
            },
            async createBanner() {
                if (this.showInEveryCity) {
                    this.banner.cities = null;
                }
                const response = await axios.post(`/api/shop/banners`, this.banner);
                return response.data;
            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'banners');
                this.banner.image = result.data;
            },
            async uploadMobilePhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'banners');
                this.banner.mobile_image = result.data;
            }
        },
        computed: {},
        watch: {
            state() {
                if (Object.keys(this._banner).length) {
                    this.banner = __deepClone(this._banner);
                    if (!this.banner.cities || this.banner.cities.length === 0) {
                        this.showInEveryCity = true;
                    }
                } else {
                    this.banner = DEFAULT_BANNER_STATE;
                }
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false
            },
            _banner: {
                type: Object,
                default: () => ({})
            }
        }
    }
</script>

<style scoped>

</style>
