<template>
    <v-dialog persistent max-width="1300" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Редактор новостей</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Заголовок новости"
                    v-model="title"
                />
                <v-btn text class="my-3" @click="$refs.fileInput.click()">
                    Загрузить фото
                    <v-icon>mdi-photo</v-icon>
                </v-btn>
                <img
                    class="d-block my-3"
                    v-if="image"
                    :src="'../storage/' + image"
                    width="400"
                    alt="Изображение">
                <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                <vue-editor
                    id="editor"
                    use-custom-image-handler
                    @image-added="handleImageAdded"
                    :editor-options="editorSettings"
                    v-model="text"> </vue-editor>
                <v-text-field
                    label="Короткое описание"
                    v-model="short_text"
                />
                <h5>Связанные товары:</h5>
                <v-simple-table v-slot:default>
                    <template>
                        <thead class="fz-18">
                        <tr>
                            <th>#</th>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Удалить</th>
                        </tr>
                        </thead>
                        <tbody class="background-tooman-grey">
                        <tr v-for="(item, index) of cart">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <v-list class="product__list" flat>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ item.product_name }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </td>
                            <td>
                                {{ item.product_price | priceFilters }}
                            </td>
                            <td>
                                <v-btn icon color="error" @click="deleteList(index)">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-text-field
                    class="mt-2"
                    v-on:input="searchInput"
                    v-model="searchValue"
                    solo
                    clearable
                    label="Поиск товара"
                    single-line
                    hide-details
                ></v-text-field>
                <v-data-table
                    class="background-tooman-grey fz-18"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :search="searchQuery"
                    loading-text="Идет загрузка товаров..."
                    :items="products"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                >
                    <template v-slot:item.product_name="{item}">
                        <v-list flat>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.product_name }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ item.manufacturer.manufacturer_name }} | {{ item.category.category_name }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:item.actions="{item}">
                        <v-btn depressed icon color="success" @click="addToList(item)">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit" :disabled="loading">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import { VueEditor, Quill } from "vue2-editor";
    import ImageResize from 'quill-image-resize-vue';
    import uploadFile from "@/api/upload";
    import {TOAST_TYPE} from "@/config/consts";
    import product_search from "@/mixins/product_search";

    Quill.register('modules/imageResize', ImageResize);

    export default {
        data: () => ({
            text: '',
            title: '',
            short_text: '',
            image: null,
            editorSettings: {
                modules: {
                    imageResize: {},
                },
                preserveWhiteSpace: true
            },
            headers: [
                {
                    text: 'Наименование',
                    value: 'product_name',
                    sortable: false,
                    align: ' fz-18'
                },
                {
                    value: 'manufacturer.manufacturer_name',
                    text: 'Производитель',
                    align: ' d-none'
                },
                {
                    text: 'Стоимость',
                    value: 'product_price'
                },
                {
                    text: 'Добавить',
                    value: 'actions'
                },
                {
                    text: 'Штрих-код',
                    value: 'product_barcode',
                    align: ' d-none'
                }
            ],
            loading: false,
            cart: [],
        }),
        methods: {
            async onSubmit() {
                this.loading = true;
                const news = {
                    text: this.text.replaceAll('&nbsp;', ' '),
                    title: this.title,
                    short_text: this.short_text,
                    image: this.image,
                    products: this.cart.map(c => c.product_id)
                };
                if (Object.keys(this.currentNews).length) {
                    news.id = this.currentNews.id;
                    await this.$store.dispatch('EDIT_NEWS', news);
                } else {
                    await this.$store.dispatch('ADD_NEWS', news);
                }
                this.loading = false;
                this.$emit('cancel');
            },
            async uploadPhoto(e) {
                try {
                    const file = e.target.files[0];
                    const response = await uploadFile(file, 'file', 'news');
                    this.image = response.data;
                } catch (e) {
                    this.$toast.error('Во время загрузки файла произошла ошибка, попробуйте загрузить другую фотографию');
                } finally {
                    this.$refs.fileInput.value = null;
                }
            },
            async handleImageAdded(file, Editor, cursorLocation, resetUploader) {
                const response = await uploadFile(file, 'file', 'news');
                const photo = `${window.location.protocol}//${window.location.hostname}/storage/${response.data}`;
                Editor.insertEmbed(cursorLocation, "image", photo);
                resetUploader();
            },
            addToList(item) {
                const findIndex = this.cart.findIndex(p => p.product_id === item.product_id);
                if (findIndex === -1) {
                    this.cart.push(item);
                }
            },
            deleteList(key) {
                this.cart.splice(key, 1);
            },
        },
        mixins: [product_search],
        components: {
            VueEditor
        },
        computed: {
            products() {
                return this.$store.getters.MAIN_PRODUCTS_v2;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: true,
            },
            currentNews: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(val) {
                if (val) {
                    if (Object.keys(this.currentNews).length) {
                        this.text =  this.currentNews.text;
                        this.title =  this.currentNews.title;
                        this.short_text =  this.currentNews.short_text;
                        this.image =  this.currentNews.image;
                        this.cart = this.currentNews.product_news
                            .map(a => a.product_id)
                            .map(a => {
                                return this.products.find(p => p.product_id === a);
                            })
                    }
                } else {
                    this.text =  '';
                    this.title = '';
                    this.short_text = '';
                    this.image = null;
                    this.cart = [];
                }
            }
        }
    }
</script>

<style scoped>
    .quillWrapper {
        background-color: #fff;
        color: #000;
    }

</style>
