<template>
    <i-card-page :title="$props.pageTitle">
        <v-row>
            <v-col cols="12" xl="10">
                <v-text-field
                    class="mt-2"
                    v-on:input="onSearchChanged"
                    solo
                    clearable
                    label="Поиск товара"
                    single-line
                    hide-details
                ></v-text-field>
            </v-col>
            <v-col cols="12" xl="2">
                <v-checkbox
                    label="Скрывать отсутствующие"
                />
            </v-col>
            <v-col cols="12" xl="4">
                <v-autocomplete
                    :items="categories"
                    item-text="name"
                    item-value="id"
                    label="Категория"
                />
            </v-col>
            <v-col cols="12" xl="4">
                <v-autocomplete
                    :items="manufacturers"
                    item-text="manufacturer_name"
                    item-value="id"
                    label="Бренд"
                />
            </v-col>
            <v-col cols="12" xl="4">
                <v-select
                    :items="stores"
                    item-text="name"
                    item-value="id"
                    label="Склад"
                    :disabled="!storeFilterIsActive"
                />
            </v-col>
        </v-row>
    </i-card-page>
</template>

<script>
import { debounce } from 'lodash';

export default {
    name: 'ProductPickerTable',
    props: {
        limitedQuantity: {
            type: Boolean,
            default: false
        },
        value: {
            type: Array,
            default: () => [],
        },
        pageTitle: {
            type: String,
            default: 'Список товаров'
        }
    },
    data: () => ({
        search: '',
        filters: {
            onlyInStock: null,
            categoryId: null,
            storeId: null,
        },
    }),
    methods: {
        onSearchChanged: debounce(function(value) {
            console.log(value);
        },500),
    },
    computed: {
        cart: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit("input", val);
            },
        },
        stores() {
            return this.$store.getters.stores;
        },
        storeFilterIsActive () {
            return this.isAdmin|| this.IS_BOSS || this.IS_STOREKEEPER
        },
        manufacturers() {
            return [
                {
                    id: -1,
                    manufacturer_name: 'Все',
                },
                ...this.$store.getters.manufacturers,
            ];
        },
        categories() {
            return [
                {
                    id: -1,
                    name: 'Все',
                },
                ...this.$store.getters.categories,
            ];
        },
        subcategories() {
            if (this.categoryId !== -1) {
                const category = this.categories.find(
                    (c) => c.id === this.categoryId,
                );
                if (category) {
                    return [
                        {
                            subcategory_name: 'Все',
                            id: -1,
                        },
                        ...category.subcategories,
                    ];
                } else {
                    return [];
                }
            } else {
                return [];
            }
        },
    }
};
</script>

<style scoped>

</style>
