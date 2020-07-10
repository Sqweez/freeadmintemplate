<template>
    <v-card>
        <v-card-title>
            Рейтинг товаров
        </v-card-title>
        <v-card-text>
            <v-simple-table v-slot:default>
                <thead>
                <tr class="stat-table">
                    <th>Категория</th>
                    <th>Товары</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(category, key) of MVP_CATEGORY_PRODUCTS" :key="Math.random()" class="stat-table">
                    <td>
                        {{ getCategoryById(key) }}
                    </td>
                    <td>
                        <ol v-for="item of category">
                            <li>
                                {{ item.product.product_name }}
                                {{ item.product.manufacturer }}
                                {{ item.product.attributes.map(a => a.attribute_value).join(' ') }} |
                                <b>Количество:  {{ item.count }} шт.</b>
                            </li>
                        </ol>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </v-card-text>
    </v-card>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import { mapGetters } from 'vuex';

    export default {
        data: () => ({}),
        methods: {
            getCategoryById(id) {
                return this.categories.find(c => c.id == id).name;
            },
        },
        computed: {
            ...mapGetters(['MVP_CATEGORY_PRODUCTS', 'categories'])
        },
        async created() {
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            await this.$store.dispatch(ACTIONS.GET_MVP_PRODUCTS);
        }
    }
</script>

<style lang="scss">
    .stat-table th, .stat-table td {
        font-size: 16px!important;

        li {
            font-size: 16px!important;
        }
    }

    th:first-child, td:first-child {
        max-width: 300px!important;
    }

</style>
