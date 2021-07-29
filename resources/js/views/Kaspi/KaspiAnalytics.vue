<template>
    <div>
        <v-card>
            <v-card-title>
                Аналитика по товарам
            </v-card-title>
            <v-card-text>
                <v-col>
                    <label>Произвольная дата</label>
                    <v-menu
                        ref="startMenu"
                        v-model="startMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="start"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y
                        full-width
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="start"
                                label="Дата начала"
                                prepend-icon="event"
                                readonly
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="start"
                            locale="ru"
                            no-title
                            scrollable
                        >
                            <div class="flex-grow-1"></div>
                            <v-btn
                                text
                                outlined
                                color="primary"
                                @click="startMenu = false"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                text
                                outlined
                                color="primary"
                                @click="changeCustomDate(startMenu, start)"
                            >
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                    <v-menu
                        ref="finishMenu"
                        v-model="finishMenu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="finish"
                        transition="scale-transition"
                        min-width="290px"
                        offset-y
                        full-width
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="finish"
                                label="Дата окончания"
                                prepend-icon="event"
                                readonly
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="finish"
                            locale="ru"
                            no-title
                            scrollable
                        >
                            <div class="flex-grow-1"></div>
                            <v-btn
                                text
                                outline
                                color="primary"
                                @click="finishMenu = false"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                text
                                outline
                                color="primary"
                                @click="changeCustomDate(finishMenu, finish) "
                            >
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-col>
                <v-btn color="error" block @click="getData">
                    Получить данные
                </v-btn>
                <div v-if="kaspiAnalytics.length">
                    <h5>Топ-40 проданных каспи товаров</h5>
                    <v-list>
                        <v-list-item v-for="(product, idx) of top40Products" :key="`top-products-${idx}`">
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-black">{{ idx + 1 }}.{{ product.product_name }} | {{ product.count }} шт.</v-list-item-title>
                                <v-list-item-title>{{ product.manufacturer }}, {{ product.category }}, {{ product.attributes }}</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </div>
                <div v-if="kaspiAnalytics.length">
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Категория</th>
                                <th>Товары</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="category of categories" :key="`category-row-${category.id}`">
                                <td>
                                    {{ category.name }}
                                </td>
                                <td></td>
                                <td>
                                    <v-list>
                                        <v-list-item v-for="(product, idx) of getProductCategory(category.id)" :key="`top-product-${product.product_id}`">
                                            <v-list-item-content>
                                                <v-list-item-title class="font-weight-black">{{ idx + 1 }}.{{ product.product_name }} | {{ product.count }} шт.</v-list-item-title>
                                                <v-list-item-title>{{ product.manufacturer }}, {{ product.category }}, {{ product.attributes }}</v-list-item-title>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import moment from "moment";
    import {getKaspiProductAnalytics} from "@/api/products";
    import ACTIONS from "@/store/actions";

    export default {
        data: () => ({
            start: null,
            startMenu: null,
            finish: null,
            finishMenu: null,
            kaspiAnalytics: [],
        }),
        async created() {
            this.$loading.enable();
            this.start = moment().startOf('month').format('yyyy-MM-DD');
            this.finish = moment().endOf('month').format('yyyy-MM-DD');
            await this.$store.dispatch(ACTIONS.GET_CATEGORIES);
            const { data } = await getKaspiProductAnalytics(this.start, this.finish);
            this.kaspiAnalytics = data;
            this.$loading.disable();
        },
        methods: {
            async getData() {
                this.$loading.enable();
                this.kaspiAnalytics = [];
                const { data } = await getKaspiProductAnalytics(this.start, this.finish);
                this.kaspiAnalytics = data;
                this.$loading.disable();
            },
            async changeCustomDate() {
                this.$refs.startMenu.save(this.start);
                this.$refs.finishMenu.save(this.finish);
            },
            getProductCategory(id) {
                return this.kaspiAnalytics
                    .filter(p => +p.category_id === +id)
                    .slice(0, 10);
            }
        },
        computed: {
            top40Products() {
                return this.kaspiAnalytics.slice(0, 40);
            },
            categories() {
                return this.$store.getters.categories;
            }
        }
    }
</script>

<style scoped>

</style>
