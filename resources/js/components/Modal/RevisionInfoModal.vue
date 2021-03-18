<template>
    <v-dialog persistent max-width="1100" v-model="state">
        <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card v-if="!loading">
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Результаты ревизии</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
               <v-simple-table v-slot:default>
                   <thead>
                   <tr>
                       <th>Наименование товара</th>
                       <th>Производитель</th>
                       <th>Атрибуты</th>
                       <th>Стоимость</th>
                       <th>Количество склад</th>
                       <th>Количество факт</th>
                   </tr>
                   </thead>
                   <tbody>
                   <tr v-for="(product, index) of products">
                       <td>
                           {{ product.product.product_name }}
                       </td>
                       <td>
                           {{ product.product.manufacturer }}
                       </td>
                       <td>
                           {{ product.product.attributes.map(a => a.attribute_value).join(' | ') }}
                       </td>
                       <td>
                           {{ product.product.product_price }}
                       </td>
                       <td>
                           {{ product.stock_quantity }}
                       </td>
                       <td>
                           {{ product.fact_quantity }}
                           <span :class="(product.fact_quantity - product.stock_quantity) > 0 ? 'text--green' : 'text--red'">
                               <b>({{ product.fact_quantity - product.stock_quantity}})</b>
                           </span>
                       </td>
                   </tr>
                   </tbody>
               </v-simple-table>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    import axios from 'axios';


    export default {
        data: () => ({
            loading: true,
            products: [],
        }),
        methods: {},
        computed: {},
        watch: {
            async state() {
                if (this.state) {
                    const { data } = await axios.get('/api/revision/' + this.id);
                    this.loading = false;
                    this.products = data.data;
                } else {
                    this.products = [];
                }
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false
            },
            id: {
                type: Number,
                default: -1,
            }
        }
    }
</script>

<style scoped>
    .text--red {
        color: #f00;
    }

    .text-green {
        color: #0f0;
    }
</style>
