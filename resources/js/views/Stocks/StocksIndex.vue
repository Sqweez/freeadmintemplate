<template>
    <div>
        <v-card>
            <v-card-title>
                Акции
            </v-card-title>
            <v-card-text>
                <router-link tag="v-btn" to="/stocks/create">
                    Создать акцию
                </router-link>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Название</th>
                        <th>Скидка, %</th>
                        <th>Состав</th>
                        <th>Дата начала</th>
                        <th>Дата окончания</th>
                        <th>Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(stock, key) of stocks" :key="`stock-${key}`">
                        <td>{{ key + 1 }}</td>
                        <td>{{ stock.title }}</td>
                        <td>{{ stock.discount }}%</td>
                        <td>
                            <v-expansion-panels accordion>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>Товары</v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <ul>
                                            <li v-for="product of stock.products" :key="`product-${product.id}`">
                                                <b>Товар: </b>{{ product.product_name }} | {{ product.attributes.join(', ') }} | {{ product.manufacturer }}
                                                <br><b>Изначальная цена: {{ product.initial_price | priceFilters }}</b><br>
                                                <b>Финальная цена: {{ product.product_price | priceFilters }}</b>
                                            </li>
                                        </ul>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </td>
                        <td>{{ stock.started_at }}</td>
                        <td>{{ stock.finished_at }}</td>
                        <td>
                            <v-btn text color="error" @click="stockId = stock.id; deleteModal = true;">
                                Удалить
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранную акцию?"
            :on-confirm="deleteStock"
            @cancel="stockId = null; deleteModal = false;"
        />
    </div>
</template>

<script>
    import axios from 'axios';
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";
    export default {
        components: {ConfirmationModal},
        data: () => ({
            stocks: [],
            deleteModal: false,
            stockId: null,
        }),
        methods: {
            async deleteStock() {
                this.$loading.enable();
                await axios.delete(`/api/v2/stocks/${this.stockId}`);
                this.stocks = this.stocks.filter(s => s.id !== this.stockId);
                this.deleteModal = false;
                this.stockId = null;
                this.$toast.success('Акция удалена!');
                this.$loading.disable();
            }
        },
        computed: {},
        async created() {
            this.$loading.enable();
            const { data } = await axios.get(`/api/v2/stocks`);
            this.stocks = data.data;
            this.$loading.disable();
        }
    }
</script>

<style scoped>

</style>
