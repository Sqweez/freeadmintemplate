<template>
    <v-dialog
        persistent
        max-width="1200"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Цель</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form @submit.prevent="onSubmit" ref="goalForm">
                    <v-text-field
                        label="Название"
                        v-model="goal.name"
                    ></v-text-field>
                    <div class="d-flex" v-if="goal.image">
                        <div
                            class="image-container">
                            <button class="delete-image" @click.prevent="deleteImage">&times;</button>
                            <img
                                :src="'../storage/' + goal.image"
                                width="150"
                                alt="Изображение">
                        </div>
                    </div>
                    <v-btn text class="mt-3" @click="$refs.fileInput.click()">
                        Загрузить фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>
                    <input type="file" class="d-none" ref="fileInput" @change="uploadPhoto">
                    <div class="d-flex" v-if="goal.mobile_image">
                        <div
                            class="image-container">
                            <button class="delete-image" @click.prevent="deleteMobileImage">&times;</button>
                            <img
                                :src="'../storage/' + goal.mobile_image"
                                width="150"
                                alt="Изображение">
                        </div>
                    </div>
                    <v-btn text class="mt-3" @click="$refs.fileInput2.click()">
                        Загрузить мобильное фото
                        <v-icon>mdi-photo</v-icon>
                    </v-btn>
                    <input type="file" class="d-none" ref="fileInput2" @change="uploadMobilePhoto">
                    <h4 class="mt-4 mb-4 text-center">
                        Разделы цели:
                    </h4>
                    <div class="mt-5" v-for="(part, key) of goal.parts" :key="key">
                        <v-text-field label="Название раздела" v-model="part.name"></v-text-field>
                        <div class="flex-1">
                            <div class="d-flex">
                                <v-autocomplete
                                    v-model="goal.parts[key].category_id"
                                    label="Категория"
                                    item-text="name"
                                    item-value="id"
                                    :items="categories"
                                    class="mr-3"></v-autocomplete>
                                <v-autocomplete
                                    v-if="goal.parts[key].category_id"
                                    v-model="goal.parts[key].subcategory_id"
                                    :items="getSubcategories(goal.parts[key].category_id)"
                                    item-value="id"
                                    item-text="subcategory_name"
                                    label="Подкатегория"
                                    class="ml-3"></v-autocomplete>
                            </div>
                            <div class="d-flex align-center" v-for="(product, idx) of goal.parts[key].products">
                                <v-autocomplete
                                    v-if="goal.parts[key].category_id"
                                    label="Товар"
                                    class="flex-1"
                                    :item-text="function(item) {
                                        return `${item.product_name} | ${item.manufacturer.manufacturer_name} | ${item.product_price}`;
                                    }"
                                    item-value="product_id"
                                    :items="getProducts(goal.parts[key].category_id, goal.parts[key].subcategory_id)"
                                    v-model="goal.parts[key].products[idx]"
                                ></v-autocomplete>
                                <v-btn icon color="success" @click="onClickProductSelect(idx, key)">
                                    <v-icon v-if="idx === 0">mdi-plus</v-icon>
                                    <v-icon v-else>mdi-minus</v-icon>
                                </v-btn>
                            </div>
                            <vue-editor v-if="state" v-model="goal.parts[key].description"></vue-editor>
                        </div>
                    </div>
                    <v-btn color="success" class="mt-5" @click="addPart">
                        Добавить раздел <v-icon>mdi-plus</v-icon>
                    </v-btn>
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
    import uploadFile, {deleteFile} from "../../api/upload";
    import { VueEditor } from "vue2-editor";
    import ACTIONS from "../../store/actions";

    export default {
        components: {
            VueEditor
        },
        data: () => ({
            goal: {
                name: '',
                image: null,
                parts: [],
                mobile_image: null,
            }
        }),
        watch: {
            state() {
                if (this.state) {
                    if (this.id === -1) {
                        this.goal = {
                            name: '',
                            image: null,
                            parts: [],
                            mobile_image: null,
                        };
                    }
                    else {
                        this.goal = this.$store.getters.GOALS.find(g => g.id == this.id);
                    }
                }
            }
        },
        methods: {
            async onSubmit() {
                if (this.id == -1) {
                    await this.$store.dispatch(ACTIONS.CREATE_GOAL, this.goal);
                    this.$toast.success('Цель успешно создана!');
                }
                else {
                    await this.$store.dispatch(ACTIONS.EDIT_GOAL, this.goal);
                    this.$toast.success('Цель успешно отредактирована!');
                }
                this.$emit('cancel');
            },
            getSubcategories(category_id) {
                return this.categories.find(c => c.id === category_id).subcategories;
            },
            getProducts(category_id, subcategory_id) {
                return this.products.filter(products => {
                    if (!subcategory_id) {
                        return products.category.id === category_id;
                    }
                    return products.category.id === category_id && products.subcategory_id === subcategory_id;
                });
            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'goals');
                this.goal.image = result.data;
            },
            async uploadMobilePhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'goals');
                this.goal.mobile_image = result.data;
            },
            async deleteImage() {
                await deleteFile(this.goal.image);
                this.goal.image = null;
            },
            async deleteMobileImage() {
                await deleteFile(this.goal.mobile_image);
                this.goal.mobile_image = null;
            },
            addProductSelect(idx, key) {
                this.goal.parts[key].products.push({
                    category_id: null,
                    subcategory_id: null,
                    products: [null],
                    description: ""
                })
            },
            removeProductSelect(idx, key) {
                this.goal.parts[key].products.splice(idx, 1);
            },
            addPart() {
                this.goal.parts.push({
                    category_id: null,
                    subcategory_id: null,
                    products: [null],
                    description: ""
                })
            },
            onClickProductSelect(idx, key) {
                if (idx === 0) {
                    this.addProductSelect(idx, key);
                } else {
                    this.removeProductSelect(idx, key);
                }
            }
        },
        computed: {
            categories() {
                return this.$store.getters.categories;
            },
            products() {
                return this.$store.getters.MAIN_PRODUCTS_v2;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                type: Number,
                default: -1,
            }
        }
    }
</script>

<style scoped>
    .flex-1 {
        flex: 1;
    }
    .quillWrapper {
        background-color: #fff;
        color: #000;
    }
</style>
