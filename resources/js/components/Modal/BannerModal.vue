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

    export default {
        data: () => ({
            banner: {
                image: null,
                mobile_image: null,
                description: '',
                is_active: true,
                order: 0
            }
        }),
        methods: {
            onCancel() {
                this.$emit('cancel');
            },
            async onConfirm() {
                this.$loading.enable();
                const banner = Object.keys(this._banner).length ? await this.editBanner() : await this.createBanner();
                this.$loading.disable();
                this.$toast.success('Успешно!');
                this.$emit('confirm', banner.data);
            },
            async editBanner() {
                const banner_id = this.banner.id;
                delete this.banner.id;
                const response = await axios.patch(`/api/shop/banners/${banner_id}`, this.banner);
                return response.data;
            },
            async createBanner() {
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
                    this.banner = JSON.parse(JSON.stringify(this._banner));
                } else {
                    this.banner = {
                        image: null,
                        description: '',
                        is_active: true,
                        order: 0
                    };
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
