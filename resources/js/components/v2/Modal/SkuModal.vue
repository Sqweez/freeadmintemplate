<template>
    <v-dialog v-model="SHOW_MODAL" max-width="1200" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ title }} ассортимента v2</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text v-if="product">
                <v-text-field
                    label="Наименование"
                    v-model="product.product_name"
                    :disabled="true"
                />
                <v-text-field
                    label="Базовая цена"
                    v-model="product.product_price"
                    :disabled="true"
                />
                <div v-if="product_sku_images.length">
                    <h4>Изображения для ассортимента:</h4>
                    <div class="d-flex" >
                        <div
                            class="image-container"
                            v-for="(image, idx) of product_sku_images"
                            :key="idx">
                            <button class="delete-image" @click.prevent="deleteImage(idx)">&times;</button>
                            <img
                                :src="'../storage/' + image.image"
                                width="150"
                                height="150"
                                alt="Изображение">
                        </div>
                    </div>
                </div>
                <div v-if="product_sku_thumbs.length">
                    <h4>Миниатюры:</h4>
                    <div class="d-flex" >
                        <div
                            class="image-container"
                            v-for="(image, idx) of product_sku_thumbs"
                            :key="idx">
                            <img
                                :src="'../storage/' + image.image"
                                width="150"
                                height="150"
                                alt="Изображение">
                        </div>
                    </div>
                </div>
                <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                    Загрузить фото
                    <v-icon>mdi-photo</v-icon>
                </v-btn>
                <v-text-field
                    label="Собственная цена"
                    v-model="self_price"
                />
                <p>Оставьте 0, если товар не имеет отдельной стоимости</p>
                <v-text-field
                    label="Штрих-код"
                    v-model="product_barcode"
                />
                <div class="d-flex">
                    <v-select
                        style="max-width: 300px;"
                        :items="attributes"
                        item-text="attribute_name"
                        item-value="id"
                        :disabled="true"
                        label="Атрибут"
                        v-model="attribute.attribute_id"
                    ></v-select>
                    <v-spacer />
                    <v-text-field
                        label="Значение"
                        v-model="attribute.attribute_value"
                    ></v-text-field>
                </div>
            </v-card-text>
            <v-divider />
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer />
                <v-progress-circular
                    v-if="loading"
                    indeterminate
                    color="primary"
                ></v-progress-circular>
                <v-btn color="success" text @click="onSubmit" v-else>{{ buttonText }}<v-icon>mdi-check</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import uploadFile, {deleteFile} from "@/api/upload";
    import {generateThumb} from "@/api/image";

    export default {
        data: () => ({
            loading: false,
            self_price: 0,
            product_barcode: '',
            attribute: {
                attribute_id: null,
                attribute_value: ""
            },
            product_sku_images: [],
            product_sku_thumbs: []

        }),
        computed: {
            SHOW_MODAL() {
                return this.$store.getters['modals/PRODUCT_SKU_MODAL'];
            },
            id() {
                return this.$store.getters['modals/PRODUCT_SKU_MODAL_ID']
            },
            edit() {
                return this.$store.getters['modals/PRODUCT_SKU_MODAL_EDIT']
            },
            product() {
                return this.$store.getters.PRODUCT_v2;
            },
            attributes() {
                return this.$store.getters.attributes;
            },
            title() {
                return this.edit ? 'Редактирование' : 'Добавление';
            },
            buttonText() {
                return this.edit ? 'Редактировать' : 'Добавить';
            }
        },
        methods: {
            async onSubmit() {
                const product = this.getProduct();
                if (!this.validate(product)) {
                    return null;
                }
                try {
                    if (!this.edit) {
                        await this.createProductSku(product);
                    } else {
                        await this.updateProductSku(product);
                    }
                    this.$emit('cancel');
                } catch (e) {

                }
            },
            validate(product) {
                if (!product.product_barcode.length) {
                    this.$toast.error('Заполните поле штрихкод');
                    return false;
                }
                if (!product.attributes[0].attribute_value.length) {
                    this.$toast.error('Заполните значение атрибута!');
                    return false;
                }
                return true;
            },
            getProduct() {
                return {
                    self_price: this.self_price,
                    product_barcode: this.product_barcode,
                    attributes: [this.attribute],
                    product_sku_images: this.product_sku_images.filter(s => s.image),
                    product_sku_thumbs: this.product_sku_thumbs.filter(s => s.image),
                };
            },
            async createProductSku(product) {
                const payload = {
                    id: this.product.product_id,
                    product,
                    sku_id: this.id
                };
                await this.$store.dispatch('CREATE_PRODUCT_SKU', payload);
            },
            async updateProductSku(product) {
                const payload = {
                    id: this.id,
                    product,
                };
                await this.$store.dispatch('UPDATE_PRODUCT_SKU', payload);
            },
            async uploadPhoto(e) {
                try {
                    const file = e.target.files[0];
                    const response = await uploadFile(file, 'file', 'products');
                    this.product_sku_images.push({image: response.data});
                    await this.createImageThumb(response.data)
                } catch (e) {
                    this.$toast.error('Во время загрузки файла произошла ошибка, попробуйте загрузить другую фотографию');
                } finally {
                    this.$refs.fileInput.value = null;
                }
            },
            async createImageThumb(image) {
                try {
                    const response = await generateThumb(image);
                    this.product_sku_thumbs.push({image: response.data});
                } catch (e) {
                    console.log(e);
                }
            },
            async deleteImage(key) {
                await deleteFile(this.product_sku_images[key]);
                this.product_sku_images.splice(key, 1);
                await deleteFile(this.product_sku_thumbs[key]);
                this.product_sku_thumbs.splice(key, 1);
            },
        },
        watch: {
            SHOW_MODAL(value) {
               if (!value) {
                    this.self_price =  0;
                    this.product_barcode = '';
                    this.attribute =  { attribute_id: null, attribute_value: ""};
                } else {
                    if (!this.edit) {
                        this.attribute = {attribute_id: this.product.grouping_attribute_id, attribute_value: ""};
                        return;
                    }
                    this.product_barcode = this.product.product_barcode;
                    this.attribute = this.product.attributes.find(a => a.attribute_id === this.product.grouping_attribute_id);
                    this.self_price = this.product.self_price;
                    this.product_sku_images = this.product.product_sku_images;
                    this.product_sku_thumbs = this.product.product_sku_thumbs;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .quillWrapper {
        background-color: #fff;
        color: #000;
    }

    .image-container {
        img {
            object-fit: contain;
            object-position: center;
        }
        position: relative;

        .delete-image {
            padding: 8px 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            position: absolute;
            right: 14px;
            top: 14px;
            font-size: 2rem;
            border: none;
            transition: .3s;
            &:hover {
                background-color: rgba(255, 255, 255, 0.6);
            }
        }
    }
</style>
