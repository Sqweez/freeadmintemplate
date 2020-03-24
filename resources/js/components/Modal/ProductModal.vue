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
                    <froala v-model="product.product_description" :config="froalaConfig" v-if="state"/>
                   <!-- <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                        Загрузить фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>-->
                  <!--  <input type="file" class="d-none" ref="fileInput">-->
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
                    <v-select
                        label="Производитель"
                        :items="manufacturers"
                        item-text="manufacturer_name"
                        item-value="id"
                        v-model="product.manufacturer"
                    />
                 <!--   <v-select
                        label="Склад"
                        :items="stores"
                        item-text="city"
                        item-value="id"
                        v-model="product.store"
                    />-->
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
                <v-btn color="success" text @click="onSubmit">{{ id === -1 ? 'Создать' : 'Редактировать' }} <v-icon>mdi-check</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {VSelect} from 'vuetify/lib'
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import '@ckeditor/ckeditor5-build-classic/build/translations/ru';
    import ThemeLark from '@ckeditor/ckeditor5-theme-lark';
    import showToast from "../../utils/toast";
    import ACTIONS from "../../store/actions";

    export default {
        components: {VSelect},
        watch: {
            state() {
                this.attributesSelect = [];
                this.groupProduct = false;
                this.product = {
                    categories: [],
                    subcategories: [],
                    product_description: '',
                    attributes: [
                        {
                            attribute_id: null,
                            attribute_value: ''
                        }
                    ],
                };
                if (this.id !== -1) {
                    this.product = {...this.$store.getters.product(this.id)};
                    this.product.categories = this.product.categories.map(c => c.id);
                    this.product.subcategories = this.product.subcategories.map(c => c.id);
                    this.product.manufacturer = this.product.manufacturer_id;
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
            groupProduct: false,
        }),
        methods: {
            addAttributesSelect() {
                this.product.attributes.push({
                    attribute_id: null,
                    attribute_value: ''
                });
                this.attributesSelect.push(VSelect);
            },
            removeAttributeSelect(idx) {
                this.attributesSelect.splice(idx, 1);
                this.product.attributes.splice(idx + 1, 1);
            },
            async createProduct() {
                const product = {...this.product};
                await this.$store.dispatch(ACTIONS.CREATE_PRODUCT, product);
                showToast('Товар успешно добавлен')
            },
            async editProduct() {
                const product = {...this.product};
                await this.$store.dispatch(ACTIONS.EDIT_PRODUCT, product);
                showToast('Товар успешно отредактирован')
            },
            async addRange() {
                this.product.id = this.product.group_id;
                this.product.groupProduct = this.groupProduct;
                await this.$store.dispatch(ACTIONS.ADD_PRODUCT_RANGE, this.product);
                showToast('Ассортимент добавлен ')
            },
            onSubmit() {
                if (this.id === -1) {
                    this.createProduct();
                } else if (!this.rangeMode){
                    this.editProduct();
                } else {
                    this.addRange();
                }
                this.$emit('cancel');
            }
        }
    }
</script>

<style scoped>
</style>
