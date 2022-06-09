<template>
    <div class="mt-5">
        <v-btn color="error" class="float-right d-block" @click="createMode = true" v-if="!createMode">
            Добавить категорию
            <v-icon>mdi-plus</v-icon>
        </v-btn>
        <br><br>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                <tr>
                    <th>Наименование категории</th>
                    <th>Список подкатегорий</th>
                    <th>Изображение</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(category, idx) of categories" :key="idx">
                    <td>
                        <span v-if="!editMode || newCategory.id !== category.id">{{ category.name }}</span>
                        <v-text-field
                            v-else
                            v-model="newCategory.name"
                            label="Наименование категории"
                        />
                    </td>
                    <td>
                        <div class="d-flex">
                            <ul style="width: 90%">
                                <li v-for="(subcategory, idx) of category.subcategories" :key="idx">
                                    <span v-if="!editMode || newCategory.id !== category.id">{{ subcategory.subcategory_name }}</span>
                                    <div class="d-flex" v-else>
                                        <v-text-field
                                            v-model="newCategory.subcategories[idx].subcategory_name"
                                            label="Подкатегория"
                                        />
                                    </div>
                                </li>
                                <li
                                    v-if="newCategory.id === category.id"
                                    v-for="(field, idx) of subcategoryFields"
                                    :key="idx"
                                    class="d-flex">
                                    <v-text-field
                                        label="Подкатегория"
                                        v-model="subcategories[idx]"
                                    />
                                    <v-btn class="mt-4" icon text @click="removeSubcategoryField(idx)">
                                        <v-icon>
                                            mdi-minus
                                        </v-icon>
                                    </v-btn>
                                </li>
                            </ul>
                            <v-btn icon text class="mt-4" @click="addSubcategoryField" v-if="editMode && newCategory.id === category.id">
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </div>

                    </td>
                    <td>
                        <div>
                            <img v-if="!editMode || newCategory.id !== category.id" :src="'../storage/' + category.category_img" alt="" width="100" height="100">
                            <img v-else :src="'../storage/' + newCategory.category_img" alt="" width="100" height="100">
                        </div>
                        <div v-if="editMode && newCategory.id === category.id">
                            <v-btn color="primary" class="mt-2" @click="editPhoto">Изменить</v-btn>
                            <input type="file" name="photo" class="d-none" ref="photoInput" @change="uploadPhoto">
                        </div>

                    </td>
                    <td>
                        <div v-if="!editMode || newCategory.id !== category.id">
                            <v-btn icon @click="newCategory = {...category}; editMode = true;">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="categoryId = category.id; deleteModal = true">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                        <div v-else>
                            <v-btn icon @click="cancelEditing">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                            <v-btn icon @click="editCategory">
                                <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </div>
                    </td>
                </tr>
                <tr v-if="createMode">
                    <td>
                        <v-text-field
                            label="Наименование категории"
                            v-model="newCategory.name"
                        />
                    </td>
                    <td>
                        <ul>
                            <li class="d-flex">
                                <v-text-field
                                    v-model="newCategory.subcategories[0]"
                                    label="Подкатегория"/>
                                <v-btn icon text class="mt-4" @click="addSubcategoryField">
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </li>
                            <li
                                v-for="(field, idx) of subcategoryFields"
                                :key="idx"
                                class="d-flex">
                                <v-text-field
                                    v-model="newCategory.subcategories[idx + 1]"
                                    label="Подкатегория"
                                />
                                <v-btn class="mt-4" icon text @click="removeSubcategoryField(idx)">
                                    <v-icon>
                                        mdi-minus
                                    </v-icon>
                                </v-btn>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <img v-if="newCategory.category_img" :src="'../storage/' + newCategory.category_img" alt="" width="100" height="100">
                        <div>
                            <v-btn color="primary" class="mt-2" @click="editPhoto">Выбрать фото</v-btn>
                            <input type="file" name="photo" class="d-none" ref="photoInput" @change="uploadPhoto">
                        </div>
                    </td>
                    <td>
                        <v-btn icon @click="cancelCreation">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn icon @click="createCategory">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>
        <ConfirmationModal
            :state="deleteModal"
            :on-confirm="deleteCategory"
            v-on:cancel="categoryId = null; deleteModal = false"
            message="Вы действительно хотите удалить выбранную категорию?"
        />
    </div>
</template>

<script>
    import ACTIONS from '@/store/actions'
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    import uploadFile from "@/api/upload";
    import {VTextField} from "vuetify/lib";

    export default {
        components: {ConfirmationModal},
        data: () => ({
            newCategory: {
                name: '',
                subcategories: []
            },
            createMode: false,
            subcategoryFields: [],
            editMode: false,
            categoryId: null,
            deleteModal: false,
            subcategories: [],
        }),
        computed: {
            categories() {
                return this.$store.getters.categories;
            }
        },
        methods: {
            cancelCreation() {
                this.newCategory = {
                    name: '',
                    subcategories: []
                };
                this.subcategoryFields = [];
                this.createMode = false;
            },
            cancelEditing() {
                this.newCategory = {
                    name: '',
                    subcategories: []
                };
                this.subcategoryFields = [];
                this.subcategories = [];
                this.editMode = false;
            },
            async editCategory() {
                this.newCategory.subcategories = [...this.newCategory.subcategories, ...this.subcategories];
                await this.$store.dispatch(ACTIONS.EDIT_CATEGORY, this.newCategory);
                this.cancelEditing();
                this.$toast.success('Категория успешно отредактирована')
            },
            addSubcategoryField() {
                this.subcategoryFields.push(VTextField);
            },
            removeSubcategoryField(idx) {
                this.subcategoryFields.splice(idx, 1);
                this.newCategory.subcategories.splice(idx + 1, 1);
            },
            async createCategory() {
                this.newCategory.category_name = this.newCategory.name;
                delete this.newCategory.name;
                this.newCategory.subcategories = this.newCategory.subcategories.filter(s => s.length !== 0);
                await this.$store.dispatch(ACTIONS.CREATE_CATEGORY, this.newCategory);
                this.cancelCreation();
                this.$toast.success('Категория успешно создана')
            },
            async deleteCategory() {
                await this.$store.dispatch(ACTIONS.DELETE_CATEGORY, this.categoryId);
                this.categoryId = null;
                this.deleteModal = false;
                this.$toast.success('Категория успешно удалена')
            },
            editPhoto() {
                try {
                    this.$refs.photoInput[0].click();
                } catch(e) {
                    this.$refs.photoInput.click();
                }

            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file, 'file', 'category');
                this.newCategory.category_img = result.data;
            }
        }
    }
</script>

<style scoped>

</style>
