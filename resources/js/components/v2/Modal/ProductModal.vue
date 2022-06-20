<template>
    <v-dialog v-model="SHOW_MODAL" max-width="1200" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ getModalTitle }} товара v2</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form v-if="SHOW_MODAL">
                    <v-text-field
                        label="Наименование"
                        v-model="product_name"
                        :disabled="!IS_SUPERUSER"
                    />

                    <v-text-field
                        v-if="IS_SUPERUSER"
                        label="Наименование интернет-магазина"
                        v-model="product_name_web"
                    />

                    <vue-editor
                        use-custom-image-handler
                        @image-added="handleImageAdded"
                        :editor-options="editorSettings"
                        v-model="product_description" v-if="IS_SUPERUSER || IS_MODERATOR"
                    ></vue-editor>
                    <div v-if="IS_SUPERUSER">
                        <div v-if="product_images.length">
                            <h4>Изображения для общих товаров:</h4>
                            <div class="d-flex">
                                <div
                                    class="image-container"
                                    v-for="(image, idx) of product_images"
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
                        <div v-if="product_thumbs.length">
                            <h4>Миниатюры:</h4>
                            <div class="d-flex">
                                <div
                                    class="image-container"
                                    v-for="(image, idx) of product_thumbs"
                                    :key="idx">
                                    <img
                                        :src="'../storage/' + image.image"
                                        width="150"
                                        height="150"
                                        alt="Изображение">
                                </div>
                            </div>
                        </div>
                        <v-btn text class="mt-3" @click="uploadingImageFor = 'product'; $refs.fileInput.click()">
                            Загрузить фото общее
                            <v-icon>mdi-photo</v-icon>
                        </v-btn>
                        <p>
                            Общая фотография для группы товаров, будет отображаться в каталоге по умолчания, и внутри
                            товара, в случае, если не загружены вариативные
                        </p>
                    </div>
                    <div v-if="!isEditing">
                        <div v-if="product_sku_images.length">
                            <h4>Изображения для общих товаров:</h4>
                            <div class="d-flex">
                                <div
                                    class="image-container"
                                    v-for="(image, idx) of product_sku_images"
                                    :key="idx">
                                    <button class="delete-image" @click.prevent="deleteSkuImage(idx)">&times;</button>
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
                            <div class="d-flex">
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
                        <v-btn text class="mt-3" @click="uploadingImageFor = 'sku'; $refs.fileInput.click()">
                            Загрузить фото для этого товара
                            <v-icon>mdi-photo</v-icon>
                        </v-btn>
                        <p>
                            Вариативные фотографии для ассортимента товара, например разные цвета упаковки для
                            батончиков разных вкусов
                        </p>
                    </div>
                    <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                    <v-autocomplete
                        :items="categories"
                        item-text="name"
                        item-value="id"
                        v-model="category"
                        no-data-text="Нет данных"
                        label="Категория"
                        :disabled="!IS_SUPERUSER"
                    />
                    <div class="d-flex align-center">
                        <div style="flex-grow: 1">
                            <v-autocomplete
                                style="flex: 1;"
                                :items="subcategories"
                                item-text="subcategory_name"
                                item-value="id"
                                v-model="subcategory"
                                label="Подкатегория"
                                no-data-text="Нет данных"
                                :disabled="!IS_SUPERUSER"
                            />
                        </div>
                    </div>
                    <!-- доп подкатегории -->
                    <div v-if="category">
                        <v-autocomplete
                            multiple
                            style="flex: 1;"
                            :items="subcategories"
                            item-text="subcategory_name"
                            item-value="id"
                            v-model="additionalSubcategories"
                            label="Доп подкатегории"
                            no-data-text="Нет данных"
                        />
                    </div>
                    <v-text-field
                        label="Стоимость"
                        :disabled="!IS_SUPERUSER"
                        v-model.number="product_price"
                        type="number"/>
                    <v-text-field
                        label="Стоимость в Kaspi Магазине"
                        :disabled="!IS_SUPERUSER"
                        v-model.number="kaspi_product_price"
                        type="number"/>
                    <v-text-field
                        v-if="!isEditing || (grouping_attribute_id === 0 || grouping_attribute_id === null)"
                        label="Штрихкод"
                        v-model.number="product_barcode"
                        type="text"/>
                    <v-autocomplete
                        label="Производитель"
                        :disabled="!IS_SUPERUSER"
                        :items="manufacturers"
                        item-text="manufacturer_name"
                        item-value="id"
                        v-model="manufacturer"
                        no-data-text="Нет данных"
                        :append-outer-icon="'mdi-plus'"
                        @click:append-outer="manufacturerModal = true"
                    />
                    <div v-if="IS_SUPERUSER || IS_MODERATOR">
                        <h5>Теги:</h5>
                        <div class="d-flex">
                            <div>
                                <v-chip
                                    v-for="(tag, key) of tags"
                                    :key="key"
                                    class="mr-2 mb-2"
                                    close
                                    link
                                    pill
                                    @click:close="removeTag(key)"
                                >{{ tag.name }}
                                </v-chip>
                                <v-text-field
                                    label="Новый тег"
                                    v-model="newTag"
                                    :append-outer-icon="'mdi-plus'"
                                    @click:append-outer="createTag"
                                />
                            </div>
                        </div>
                    </div>
                    <div v-if="IS_SUPERUSER">
                        <v-checkbox
                            label="Хит продаж"
                            v-model="is_hit"
                        />
                        <v-checkbox
                            label="IHerb"
                            v-model="is_iherb"
                        />
                        <v-checkbox
                            label="Виден на сайте"
                            v-model="is_site_visible"
                        />
                        <v-checkbox
                            label="Виден в Kaspi магазине"
                            v-model="is_kaspi_visible"
                        />
                        <p>
                            Отвечает за видимость на сайте iron-addicts.kz
                            <br>
                            Выбирайте если это товар для внутреннего закупа или которым торгуем только оффлайн
                        </p>
                        <v-divider></v-divider>
                        <h5>Поставщик:</h5>
                        <v-select
                            label="Поставщик"
                            :items="suppliers"
                            item-text="supplier_name"
                            item-value="id"
                            v-model="supplier_id"
                        />
                        <v-divider></v-divider>
                        <v-divider></v-divider>
                        <h5>Мета-теги</h5>
                        <v-text-field
                            label="Title"
                            v-model="meta_title"
                        />
                        <v-text-field
                            label="Description"
                            v-model="meta_description"
                        />
                        <v-divider></v-divider>
                        <h5>Цены по городам:</h5>
                        <div class="d-flex">
                            <v-select
                                style="max-width: 300px;"
                                :items="getPriceStores(0)"
                                item-text="name"
                                item-value="id"
                                label="Магазин"
                                v-model="prices[0].store_id"
                            ></v-select>
                            <v-spacer/>
                            <v-text-field
                                label="Стоимость"
                                v-model.number="prices[0].price"
                            ></v-text-field>
                            <v-btn icon @click="addPricesSelect">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </div>
                        <div class="d-flex" v-for="(attrs, idx) of pricesSelect" :key="idx * 1500"
                             v-if="pricesSelect.length !== 0">
                            <component
                                v-if="pricesSelect.length !== 0"
                                style="max-width: 300px;"
                                :is="attrs"
                                :items="getPriceStores(idx + 1)"
                                item-text="name"
                                item-value="id"
                                label="Магазин"
                                v-model="prices[idx + 1].store_id"
                            />
                            <v-spacer/>
                            <v-text-field
                                label="Стоимость"
                                v-model.number="prices[idx + 1].price"
                            ></v-text-field>
                            <v-btn icon @click="removePriceSelect(idx)">
                                <v-icon>mdi-minus</v-icon>
                            </v-btn>
                        </div>
                        <v-divider></v-divider>
                        <h5>Атрибуты:</h5>
                        <div v-if="!isEditing">
                            <v-checkbox
                                label="Без ассортимента"
                                v-model="withoutAnotherSku"
                            />
                            <p>Выбирайте в случае, если товар не будет иметь ассортимента, например Samyum wan slim <br>
                                В остальных случае, если есть ассортимент по вкусу, цвету оставляйте галочку не нажатой
                            </p>
                        </div>

                        <div class="d-flex">
                            <v-btn text @click="addAttributesSelect">
                                Добавить атрибут
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                            <!--<v-select
                                style="max-width: 300px;"
                                :items="getAttributes(0)"
                                item-text="attribute_name"
                                item-value="id"
                                label="Атрибут"
                                v-model="product_attributes[0].attribute_id"
                            ></v-select>
                            <v-spacer />
                            <v-text-field
                                label="Значение"
                                v-model="product_attributes[0].attribute_value"
                            ></v-text-field>
                            <v-btn icon @click="addAttributesSelect">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>-->
                        </div>
                        <div class="d-flex" v-for="(attrs, idx) of attributesSelect" :key="`attribute-select-${idx}`"
                             v-if="attributesSelect.length !== 0">
                            <component
                                v-if="attributesSelect.length !== 0"
                                style="max-width: 300px;"
                                :is="attrs"
                                :items="getAttributes(idx)"
                                item-text="attribute_name"
                                item-value="id"
                                label="Атрибут"
                                v-model="product_attributes[idx].attribute_id"
                            />
                            <v-spacer/>
                            <v-text-field
                                label="Значение"
                                v-model="product_attributes[idx].attribute_value"
                            ></v-text-field>
                            <v-btn icon @click="removeAttributeSelect(idx)">
                                <v-icon>mdi-minus</v-icon>
                            </v-btn>
                        </div>
                        <div v-if="!withoutAnotherSku && !isEditing">
                            <v-radio-group v-model="grouping_attribute_id">
                                <v-radio
                                    v-for="(attr, key) of product_attributes.filter(pa => pa.attribute_id)"
                                    :label="attributes.find(a => a.id === attr.attribute_id).attribute_name"
                                    :value="attr.attribute_id"
                                    :key="`product_attribute-radio-${key}`"/>
                            </v-radio-group>
                            <p>Выберите один из параметров по которому будет группироваться товар<br>
                                Например, вкус для протеина, размер для одежды, цвет для шейкера</p>
                        </div>

                        <v-divider></v-divider>
                        <h5>Комментарии</h5>
                        <v-list>
                            <v-list-item v-for="item of comments">
                                <v-list-item-content>
                                    <v-list-item-title class="font-weight-black">Автор: {{ item.name }}
                                        ({{ item.is_employee ? 'Сотрудник' : 'Клиент' }}) | {{ item.date }}
                                        <v-btn icon color="error" @click="deleteComment(item.id)">
                                            <v-icon>mdi-close</v-icon>
                                        </v-btn>
                                        <v-btn icon color="primary" @click="commentId = commentId == item.id ? null : item.id">
                                            <v-icon>mdi-reply</v-icon>
                                        </v-btn>
                                    </v-list-item-title>
                                    <v-list-item-title>{{ item.comment }}</v-list-item-title>
                                    <v-list>
                                        <v-list-item v-for="_item of item.children">
                                            <v-list-item-content>
                                                <v-list-item-title class="font-weight-black">Автор: {{ _item.name }}
                                                    ({{ _item.is_employee ? 'Сотрудник' : 'Клиент' }}) | {{
                                                        _item.date
                                                    }}
                                                    <v-btn icon color="error" @click="deleteComment(_item.id)">
                                                        <v-icon>mdi-close</v-icon>
                                                    </v-btn>
                                                    <v-btn icon color="primary" @click="commentId = commentId == _item.id ? null : _item.id">
                                                        <v-icon>mdi-reply</v-icon>
                                                    </v-btn>
                                                </v-list-item-title>
                                                <v-list-item-title>{{ _item.comment }}</v-list-item-title>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                        <v-textarea
                            :placeholder="currentComment ? `В ответ: ${currentComment.name}` : 'Комментарий'"
                            label="Комментарий"
                            v-model="commentText" />
                        <v-btn color="primary" @click="createComment">
                            Отправить комментарий <v-icon>mdi-send</v-icon>
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
            <v-divider/>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer/>
                <v-progress-circular
                    v-if="loading"
                    indeterminate
                    color="primary"
                ></v-progress-circular>
                <v-btn color="success" text @click="onSubmit" v-else>{{ getButtonText }}
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
        <ManufacturerModal
            :state="manufacturerModal"
            v-on:cancel="manufacturerModal = false; manufacturer = manufacturers[manufacturers.length - 1].id"
        />
    </v-dialog>
</template>

<script>
import {VSelect} from 'vuetify/lib'
import ManufacturerModal from "@/components/Modal/ManufacturerModal";
import uploadFile, {deleteFile} from "@/api/upload";
import {VueEditor, Quill} from "vue2-editor";
import {PRODUCT_MODAL_EVENTS} from "@/config/consts";
import {generateThumb} from "@/api/image";
import axios from 'axios';
import ImageResize from 'quill-image-resize-vue';

Quill.register('modules/imageResize', ImageResize);

export default {
    components: {ManufacturerModal, VSelect, VueEditor},
    watch: {
        SHOW_MODAL(value) {
            if (!value) {
                this.resetFields();
            } else {
                if (this.id !== null && this.product) {
                    this.assignFields();
                }
            }
        },
        category(value, oldValue) {
            if (oldValue !== null) {
                this.subcategory = null;
            }
        },
        withoutAnotherSku(value) {
            if (value) {
                this.grouping_attribute_id = null;
            }
        },
        product_attributes: {
            handler(value, oldValue) {
                if (!this.isEditing) {
                    if (!value.find(v => v.attribute_id === this.grouping_attribute_id) && this.grouping_attribute_id !== null) {
                        this.grouping_attribute_id = null;
                    }
                }
            },
            deep: true
        },
    },
    computed: {
        currentComment() {
            let comment = this.comments.find(c => c.id == this.commentId);
            if (!comment) {
                this.comments.forEach(c => {
                    const find = c.children.find(_c => _c.id == this.commentId);
                    if (find) {
                        comment = find;
                    }
                })
            }
            return comment;
        },
        user() {
            return this.$store.getters.USER;
        },
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
            const category = this.categories.find(c => c.id === this.category);
            return category ? category.subcategories : [];
        },
        manufacturers() {
            return this.$store.getters.manufacturers;
        },
        allPricesSelected() {
            return this.stores.length === this.prices.length;
        },
        allAttributesSelected() {
            return this.attributes.length === this.product_attributes.length;
        },
        SHOW_MODAL() {
            return this.$store.getters['modals/PRODUCT_MODAL'];
        },
        getModalTitle() {
            switch (this.action) {
                case PRODUCT_MODAL_EVENTS.ADD_PRODUCT:
                    return 'Добавление';
                case PRODUCT_MODAL_EVENTS.EDIT_PRODUCT:
                    return 'Редактирование'
                case PRODUCT_MODAL_EVENTS.ADD_PRODUCT_RANGE:
                    return 'Создание';
            }
        },
        getButtonText() {
            switch (this.action) {
                case PRODUCT_MODAL_EVENTS.ADD_PRODUCT:
                    return 'Добавить';
                case PRODUCT_MODAL_EVENTS.EDIT_PRODUCT:
                    return 'Редактировать';
                case PRODUCT_MODAL_EVENTS.ADD_PRODUCT_RANGE:
                    return 'Создать';
            }
        },
        id() {
            return this.$store.getters['modals/PRODUCT_MODAL_ID'];
        },
        action() {
            return this.$store.getters['modals/PRODUCT_MODAL_ACTION'];
        },
        product() {
            return this.$store.getters.PRODUCT_v2;
        },
        isEditing() {
            return this.action === PRODUCT_MODAL_EVENTS.EDIT_PRODUCT;
        },
        current_product_attributes() {
            return this.product_attributes.filter(a => a.attribute_id);
        },
        suppliers() {
            return this.$store.getters.SUPPLIERS;
        }
    },
    data: () => ({
        editorSettings: {
            modules: {
                imageResize: {},
            },
            preserveWhiteSpace: true
        },
        froalaConfig: {
            placeholderText: 'Введите описание',
            charCounterCount: false,
        },
        product_name: '',
        product_name_web: '',
        product_description: '',
        category: null,
        subcategory: null,
        manufacturer: null,
        product_barcode: null,
        product_price: null,
        kaspi_product_price: 0,
        product_attributes: [
            {
                attribute_id: null,
                attribute_value: ''
            }
        ],
        prices: [
            {
                store_id: null,
                price: null
            }
        ],
        tags: [],
        product_images: [],
        product_thumbs: [],
        is_hit: false,
        is_iherb: false,
        is_site_visible: true,
        is_kaspi_visible: false,
        supplier_id: null,
        attributesSelect: [],
        pricesSelect: [],
        groupProduct: false,
        manufacturerModal: false,
        loading: false,
        newTag: '',
        withoutAnotherSku: false,
        grouping_attribute_id: null,
        product_sku_images: [],
        product_sku_thumbs: [],
        uploadingImageFor: 'product',
        meta_title: '',
        meta_description: '',
        comments: [],
        commentText: '',
        commentId: null,
        additionalSubcategories: [],
    }),
    methods: {
        addSubcategoriesSelect () {},
        async handleImageAdded(file, Editor, cursorLocation, resetUploader) {
            const response = await this.$file.upload(file, 'uploads', 'file');
            const photo = `${window.location.protocol}//${window.location.hostname}/storage/${response.data}`;
            Editor.insertEmbed(cursorLocation, "image", photo);
            resetUploader();
        },
        async createComment () {
            if (this.commentText.trim().length === 0) {
                return this.$toast.error('Введите комментарий!');
            }
            this.$loading.enable();
            const response = await axios.post('/api/v2/comment', {
                comment: this.commentText,
                user_id: this.user.id,
                product_id: this.product.product_id,
                parent_id: this.commentId
            })

            this.comments = response.data;
            this.$toast.success('Комментарий добавлен');
            this.commentId = null;
            this.commentText = '';
            this.$loading.disable();
        },
        async deleteComment(id) {
            this.$loading.enable();
            await axios.delete(`/api/v2/comment/${id}`);
            this.comments = this.comments.filter(c => c.id != id);
            this.comments = this.comments.map(c => {
                c.children = c.children.filter(_c => _c.id != id);
                return c;
            })
            this.$loading.disable();
            this.$toast.success('Комментарий удален!');
        },
        removeTag(idx) {
            this.tags.splice(idx, 1);
        },
        resetFields() {
            this.product_name = this.product_description = '';
            this.product_price = this.product_barcode = this.category = this.subcategory = this.manufacturer = null;
            this.is_hit = false;
            this.is_site_visible = true;
            this.tags = [];
            this.product_images = [];
            this.product_thumbs = [];
            this.prices = [
                {
                    store_id: null,
                    price: null
                }
            ];
            this.product_attributes = [];
            this.attributesSelect = [];
            this.kaspi_product_price = 0;
            this.is_kaspi_visible = false;
            this.supplier_id = null;
            this.meta_title = '';
            this.meta_description = '';
            this.additionalSubcategories = [];
        },
        assignFields() {
            this.product_name = this.product.product_name;
            this.product_name_web = this.product.product_name_web;
            this.product_description = this.product.product_description;
            this.product_barcode = this.product.product_barcode;
            this.category = this.product.category;
            this.subcategory = this.product.subcategory;
            this.manufacturer = this.product.manufacturer;
            this.is_hit = this.product.is_hit;
            this.is_iherb = this.product.is_iherb;
            this.is_site_visible = this.product.is_site_visible;
            this.tags = this.product.tags;
            this.product_images = this.product.product_images;
            this.product_thumbs = this.product.product_thumbs;
            this.product_price = this.product.product_price;
            this.withoutAnotherSku = this.product.grouping_attribute_id === null;
            this.grouping_attribute_id = this.product.grouping_attribute_id;
            this.kaspi_product_price = this.product.kaspi_product_price;
            this.is_kaspi_visible = this.product.is_kaspi_visible;
            this.supplier_id = this.product.supplier_id;
            this.comments = this.product.comments;
            this.additionalSubcategories = this.product.additional_subcategories ? this.product.additional_subcategories : [];
            if (this.grouping_attribute_id === null) {
                this.withoutAnotherSku = true;
            }
            this.prices = this.product.prices.length ? this.product.prices : [{
                store_id: null,
                price: null
            }];

            this.product_attributes = this.product.attributes.length ? this.product.attributes : [
                {
                    attribute_id: null,
                    attribute_value: ''
                }
            ];


            if (this.isEditing) {
                this.product_attributes = this.product_attributes.filter(a => {
                    return a.attribute_id !== this.product.grouping_attribute_id;
                })
            }


            this.attributesSelect = this.product_attributes.length >= 1 ? new Array(this.product_attributes.length).fill(VSelect) : [];
            this.pricesSelect = new Array(this.prices.length - 1).fill(VSelect);

            this.meta_title = this.product.meta_title;
            this.meta_description = this.product.meta_description;


        },
        getPriceStores(idx) {
            return this.stores.filter(store => {
                return !this.prices.map(p => p.store_id).slice(0, idx).includes(store.id);
            });
        },
        getAttributes(idx) {
            let attributes = this.attributes.filter(attribute => {
                return !this.product_attributes.map(a => a.attribute_id).slice(0, idx).includes(attribute.id);
            });

            if (this.isEditing) {
                attributes = attributes.filter(attribute => {
                    return attribute.id !== this.product.grouping_attribute_id;
                });
            }

            return attributes;
        },
        createTag() {
            if (this.newTag.length) {
                this.tags.push({
                    name: this.newTag.trim().toLowerCase()
                });

                this.newTag = '';
            }
        },
        hasDuplicates(arr) {
            return arr.filter(x => {
                return arr.filter(a => x.store_id === a.store_id).length > 1;
            }).length > 0;
        },
        addAttributesSelect() {
            if (this.allAttributesSelected) {
                this.$toast.error('Выбраны все доступные атрибуты');
                return;
            }
            this.product_attributes.push({
                attribute_id: null,
                attribute_value: ''
            });
            this.attributesSelect.push(VSelect);
        },
        addPricesSelect() {
            if (this.allPricesSelected) {
                this.$toast.error('Выбраны все доступные магазины');
                return;
            }
            this.prices.push({
                store_id: null,
                price: 0
            });
            this.pricesSelect.push(VSelect);
        },
        removePriceSelect(idx) {
            this.pricesSelect.splice(idx, 1);
            this.prices.splice(idx + 1, 1);
        },
        removeAttributeSelect(idx) {
            this.attributesSelect.splice(idx, 1);
            this.product_attributes.splice(idx, 1);
        },
        async createProduct() {
            const product = this.getProductObject();
            if (!this.validate(product)) {
                throw new Error();
            }

            await this.$store.dispatch('CREATE_PRODUCT_v2', product);
            this.$toast.success('Товар успешно добавлен');
        },
        async editProduct() {
            const product = this.getProductObject();
            if (!this.validate(product)) {
                throw new Error();
            }
            console.log(product);
            await this.$store.dispatch('EDIT_PRODUCT_v2', {
                product,
                id: this.product.product_id,
            });
            this.$toast.success('Товар успешно обновлен!');

        },
        async addRange() {
            const product = this.getProductObject();
            if (!this.validate(product)) {
                throw new Error();
            }
            await this.$store.dispatch('CREATE_PRODUCT_v2', product);
            this.$toast.success('Товар успешно добавлен');
        },
        getProductObject() {
            return {
                additional_subcategories: this.additionalSubcategories,
                product_name: this.product_name,
                product_name_web: this.product_name_web,
                product_description: this.product_description,
                product_price: this.product_price,
                kaspi_product_price: this.kaspi_product_price,
                product_barcode: this.product_barcode,
                is_iherb: this.is_iherb,
                is_hit: this.is_hit,
                is_site_visible: this.is_site_visible,
                is_kaspi_visible: this.is_kaspi_visible,
                category: this.category,
                subcategory: this.subcategory,
                manufacturer: this.manufacturer,
                tags: this.tags.filter(t => t.name),
                product_images: this.product_images.filter(p => p.image),
                product_thumbs: this.product_thumbs.filter(p => p.image),
                product_sku_images: this.product_sku_images.filter(p => p.image),
                product_sku_thumbs: this.product_sku_thumbs.filter(p => p.image),
                price: this.prices.filter(p => p.store_id && p.price),
                attributes: this.product_attributes.filter(p => p.attribute_value),
                grouping_attribute_id: this.grouping_attribute_id,
                supplier_id: this.supplier_id,
                meta_title: this.meta_title,
                meta_description: this.meta_description,
            };
        },
        validate(product) {
            const showErrorToast = (field_name) => {
                this.$toast.error(`Заполните поле ${field_name}!`);
            };
            if (!product.product_name) {
                showErrorToast('Наименование');
                return false;
            }
            if (!product.product_description) {
                showErrorToast('Описание');
                return false;
            }
            if (!product.category) {
                showErrorToast('Категория');
                return false;
            }
            if (!product.subcategory) {
                showErrorToast('Подкатегория');
                return false;
            }
            if (!product.product_price) {
                showErrorToast('Cтоимость');
                return false;
            }

            if (!product.kaspi_product_price && product.is_kaspi_visible === true) {
                showErrorToast('Cтоимость в Kaspi Магазине');
                return false;
            }

            if (!product.product_barcode) {
                showErrorToast('Штрих-код');
                return false;
            }
            if (!product.manufacturer) {
                showErrorToast('Производитель');
                return false;
            }


            if (product.grouping_attribute_id === null && !this.withoutAnotherSku) {
                this.$toast.error('Необходимо выбрать группирующий атрибут или поставить галочку "без ассортимента"');
                return false;
            }
            return true;
        },
        async onSubmit() {
            try {
                switch (this.action) {
                    case PRODUCT_MODAL_EVENTS.ADD_PRODUCT:
                        await this.createProduct();
                        break;
                    case PRODUCT_MODAL_EVENTS.EDIT_PRODUCT:
                        await this.editProduct();
                        break;
                    case PRODUCT_MODAL_EVENTS.ADD_PRODUCT_RANGE:
                        await this.addRange();
                        break;
                    default:
                        break;
                }
                await this.$emit('cancel', this.action);
            } catch (e) {
                this.$toast.error('При добавлении товара произошла ошибка');
            }
        },
        async uploadPhoto(e) {
            try {
                const file = e.target.files[0];
                const response = await uploadFile(file, 'file', 'products');
                if (this.uploadingImageFor === 'product') {
                    this.product_images.push({image: response.data});
                }
                if (this.uploadingImageFor === 'sku') {
                    this.product_sku_images.push({image: response.data});
                }
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
                if (this.uploadingImageFor === 'product') {
                    this.product_thumbs.push({image: response.data});
                }
                if (this.uploadingImageFor === 'sku') {
                    this.product_sku_thumbs.push({image: response.data});
                }
            } catch (e) {
                console.log(e);
            }
        },
        async deleteImage(key) {
            await deleteFile(this.product_images[key]);
            this.product_images.splice(key, 1);
            await deleteFile(this.product_thumbs[key]);
            this.product_thumbs.splice(key, 1);
        },
        async deleteSkuImage(key) {
            await deleteFile(this.product_sku_images[key]);
            this.product_sku_images.splice(key, 1);
            await deleteFile(this.product_sku_thumbs[key]);
            this.product_sku_thumbs.splice(key, 1);
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
