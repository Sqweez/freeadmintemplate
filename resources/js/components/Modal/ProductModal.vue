<template>
    <v-dialog v-model="state" max-width="1200" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ id !== -1 && rangeMode ? 'Добавление ассортимента' : id !== -1 ? 'Редактирование' : 'Добавление'}} товара</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form v-if="state">
                    <v-checkbox
                        v-if="rangeMode"
                        label="Привязать к товару"
                        v-model="groupProduct"
                    />
                    <v-text-field
                        label="Наименование"
                        v-model="product.product_name"
                    />

                    <vue-editor v-model="product.product_description"  v-if="state"></vue-editor>
                    <div class="d-flex" v-if="product.product_images.length">
                        <div
                            class="image-container"
                            v-for="(image, idx) of product.product_images"
                            :key="idx">
                            <button class="delete-image" @click.prevent="deleteImage(idx)">&times;</button>
                            <img
                                :src="'../storage/' + image"
                                width="150"
                                height="150"
                                alt="Изображение">
                        </div>

                    </div>
                    <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                        Загрузить фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>
                    <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                    <v-select
                        :items="categories"
                        item-text="name"
                        item-value="id"
                        v-model="product.categories"
                        label="Категория"
                        multiple
                    />
                    <v-select
                        :items="subcategories"
                        item-text="subcategory_name"
                        item-value="id"
                        v-model="product.subcategories"
                        label="Подкатегория"
                        multiple
                    />
                    <v-text-field
                        label="Стоимость"
                        v-model.number="product.product_price"
                        type="number"/>
                    <v-text-field
                        label="Штрихкод"
                        v-model.number="product.product_barcode"
                        type="text"/>
                    <div class="d-flex">
                        <v-autocomplete
                            label="Производитель"
                            :items="manufacturers"
                            item-text="manufacturer_name"
                            item-value="id"
                            v-model="product.manufacturer"
                        />
                        <v-btn icon @click="manufacturerModal = true">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <v-checkbox
                        label="Хит продаж"
                        v-model="product.is_hit"
                    />
                    <v-divider></v-divider>
                    <h5>Теги:</h5>
                    <div class="d-flex">
                        <div>
                            <v-chip
                                v-for="(tag, key) of product.tags"
                                :key="key"
                                class="mr-2 mb-2"
                                close
                                link
                                pill
                                @click:close="removeTag(key)"
                            >{{ tag.name }}</v-chip>
                            <v-text-field
                                label="Новый тег"
                                v-model="newTag"
                                :append-outer-icon="'mdi-plus'"
                                @click:append-outer="createTag"
                            />
                        </div>
                    </div>
                    <v-divider></v-divider>
                    <h5>Цены по городам:</h5>
                    <div class="d-flex">
                        <v-select
                            style="max-width: 300px;"
                            :items="stores"
                            item-text="name"
                            item-value="id"
                            label="Магазин"
                            v-model="product.prices[0].store_id"
                        ></v-select>
                        <v-spacer />
                        <v-text-field
                            label="Стоимость"
                            v-model.number="product.prices[0].price"
                        ></v-text-field>
                        <v-btn icon @click="addPricesSelect">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <div class="d-flex" v-for="(attrs, idx) of pricesSelect" :key="idx * 1500" v-if="pricesSelect.length !== 0">
                        <component
                            v-if="pricesSelect.length !== 0"
                            style="max-width: 300px;"
                            :is="attrs"
                            :items="stores"
                            item-text="name"
                            item-value="id"
                            label="Магазин"
                            v-model="product.prices[idx + 1].store_id"
                        />
                        <v-spacer/>
                        <v-text-field
                            label="Стоимость"
                            v-model.number="product.prices[idx + 1].price"
                        ></v-text-field>
                        <v-btn icon @click="removeAttributeSelect(idx)">
                            <v-icon>mdi-minus</v-icon>
                        </v-btn>
                    </div>
                    <v-divider></v-divider>
                    <h5>Атрибуты:</h5>
                    <div class="d-flex">
                        <v-select
                            style="max-width: 300px;"
                            :items="attributes"
                            item-text="attribute_name"
                            item-value="id"
                            label="Атрибут"
                            v-model="product.attributes[0].attribute_id"
                        ></v-select>
                        <v-spacer />
                        <v-text-field
                            label="Значение"
                            v-model="product.attributes[0].attribute_value"
                        ></v-text-field>
                        <v-btn icon @click="addAttributesSelect">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <div class="d-flex" v-for="(attrs, idx) of attributesSelect" :key="idx * 1500" v-if="attributesSelect.length !== 0">
                        <component
                            v-if="attributesSelect.length !== 0"
                            style="max-width: 300px;"
                            :is="attrs"
                            :items="attributes"
                            item-text="attribute_name"
                            item-value="id"
                            label="Атрибут"
                            v-model="product.attributes[idx + 1].attribute_id"
                        />
                        <v-spacer/>
                        <v-text-field
                            label="Значение"
                            v-model="product.attributes[idx + 1].attribute_value"
                        ></v-text-field>
                        <v-btn icon @click="removeAttributeSelect(idx)">
                            <v-icon>mdi-minus</v-icon>
                        </v-btn>
                    </div>
                </v-form>
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
                <v-btn color="success" text @click="onSubmit" v-else>{{ id === -1 ? 'Создать' : 'Редактировать' }} <v-icon>mdi-check</v-icon></v-btn>
            </v-card-actions>
        </v-card>
        <ManufacturerModal
            :state="manufacturerModal"
            v-on:cancel="manufacturerModal = false; product.manufacturer = manufacturers[manufacturers.length - 1].id"
        />
    </v-dialog>
</template>

<script>
    import {VSelect} from 'vuetify/lib'
    import ACTIONS from "../../store/actions";
    import ManufacturerModal from "./ManufacturerModal";
    import uploadFile, {deleteFile} from "../../api/upload";
    import { VueEditor } from "vue2-editor";

    export default {
        components: {ManufacturerModal, VSelect, VueEditor},
        watch: {
            state() {
                this.attributesSelect = [];
                this.pricesSelect = [];
                this.groupProduct = false;
                this.product = {
                    categories: [],
                    subcategories: [],
                    product_description: '',
                    product_images: [],
                    attributes: [
                        {
                            attribute_id: null,
                            attribute_value: ''
                        }
                    ],
                    prices: [
                        {
                            store_id: null,
                            price: 0,
                        }
                    ],
                    tags: []
                };
                if (this.id !== -1) {
                    this.product = JSON.parse(JSON.stringify(this.$store.getters.product(this.id)));
                    this.product.categories = this.product.categories.map(c => c.id);
                    this.product.subcategories = this.product.subcategories.map(c => c.id);
                    this.product.manufacturer = this.product.manufacturer_id;
                    this.product.attributes = this.product.attributes.map(a => {
                        a.attribute_id = +a.attribute_id;
                        return a;
                    })
                    if (this.product.attributes.length > 1) {
                        this.attributesSelect = new Array(this.product.attributes.length - 1);
                        this.attributesSelect.fill(VSelect);
                    }
                    if (this.product.attributes.length === 0) {
                        this.product.attributes.push(
                            {
                                attribute_id: null,
                                attribute_value: ''
                            }
                        )
                    }

                    if (this.product.prices.length > 1) {
                        this.pricesSelect = new Array(this.product.prices.length - 1);
                        this.pricesSelect.fill(VSelect);
                    }

                    if (this.product.prices.length === 0) {
                        this.product.prices.push(
                            {
                                store_id: null,
                                price: 0
                            }
                        )
                    }
                    delete this.product.manufacturer_id;
                    delete this.product.quantity;
                }
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                default: null,
            },
            rangeMode: {
                type: Boolean,
                default: false,
            }
        },
        computed: {
            attributes() {
                return this.$store.getters.attributes;
            },
            categories() {
                return this.$store.getters.categories;
            },
            stores() {
                return this.$store.getters.stores;
            },
            subcategories() {
                const subcategories = [];
                this.categories.forEach(c => {
                    if (this.product.categories.includes(c.id)) {
                        subcategories.push(...c.subcategories);
                    }
                });
                return subcategories;
            },
            manufacturers() {
                return this.$store.getters.manufacturers;
            }
        },
        data: () => ({
            froalaConfig: {
                placeholderText: 'Введите описание',
                charCounterCount: false,
            },
            product: {
                categories: [],
                subcategories: [],
                attributes: [],
                product_description: '',
            },
            attributesSelect: [],
            pricesSelect: [],
            groupProduct: false,
            manufacturerModal: false,
            loading: false,
            newTag: '',
        }),
        methods: {
            removeTag(idx) {
                this.product.tags.splice(idx, 1);
            },
            createTag() {
                this.product.tags.push({
                    name: this.newTag.trim().toLowerCase()
                });

                this.newTag = '';
            },
            hasDuplicates(arr) {
                return arr.filter(x => {
                    return arr.filter(a => x.store_id === a.store_id).length > 1;
                }).length > 0;
            },
            addAttributesSelect() {
                this.product.attributes.push({
                    attribute_id: null,
                    attribute_value: ''
                });
                this.attributesSelect.push(VSelect);
            },
            addPricesSelect() {
                this.product.prices.push({
                    store_id: null,
                    price: 0
                });
                this.pricesSelect.push(VSelect);
            },
            removePriceSelect(idx) {
                this.pricesSelect.splice(idx, 1);
                this.product.prices.splice(idx + 1, 1);
            },
            removeAttributeSelect(idx) {
                this.attributesSelect.splice(idx, 1);
                this.product.attributes.splice(idx + 1, 1);
            },
            async createProduct() {
                this.product.tags.push(
                    {
                        name: this.product.product_name,
                    },
                    {
                        name: this.manufacturers.find(m => m.id == this.product.manufacturer).manufacturer_name,
                    }
                )
                const product = {...this.product};
                await this.$store.dispatch(ACTIONS.CREATE_PRODUCT, product);
                this.$toast.success('Товар успешно добавлен')
            },
            async editProduct() {
                const product = {...this.product};
                await this.$store.dispatch(ACTIONS.EDIT_PRODUCT, product);
                this.$toast.success('Товар успешно отредактирован')
            },
            async addRange() {
                this.product.id = this.product.group_id;
                this.product.groupProduct = this.groupProduct;
                await this.$store.dispatch(ACTIONS.ADD_PRODUCT_RANGE, this.product);
                this.$toast.success('Ассортимент добавлен ')
            },
            onSubmit() {
                this.loading = true;
                const condition = this.hasDuplicates(this.product.prices);
                if (!condition) {
                    if (this.id === -1) {
                        this.createProduct();
                    } else if (!this.rangeMode){
                        this.editProduct();
                    } else {
                        this.addRange();
                    }
                    this.$emit('cancel');
                } else {
                    this.$toast.error('Внесены несколько одинаковых магазинов!');
                }
                this.loading = false;
            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'products');
                this.product.product_images.push(result.data);
            },
            async deleteImage(key) {
                if (this.product.product_images[key] != 'products/product_image_default.jpg')
                {
                    await deleteFile(this.product.product_images[key]);
                }
                this.product.product_images.splice(key, 1);
            }
        }
    }
</script>

<style scoped lang="scss">

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
