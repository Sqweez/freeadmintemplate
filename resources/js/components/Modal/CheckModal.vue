<template>
    <v-dialog
        persistent
        max-width="1000"
        width="1000"
        v-model="state"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Товарный чек</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
           <!-- <v-card-text class="d-flex justify-center">
                <div class="check-wrapper" id="print">
                    <div class="check-header">
                        <img src="../../../assets/images/logo-check.png" alt="">
                        <h1>
                            Сеть магазинов спортивного питания "ironaddicts"
                        </h1>
                        <div class="check-divider">
                            <div class="divider-one"></div>
                            <div class="divider-two"></div>
                        </div>
                    </div>
                    <div class="check-body">
                        <ol class="products-list">
                            <li>
                                <span class="product-name">perfect protein 900г клубника что-то еще допишу</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">180000</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                            <li>
                                <span class="product-name">OMEGA 3 SIBERIAN 120 КАПСУЛ</span>
                                <span class="product-footer">
                                    <span class="product-count">
                                        3 х 599..........................................................................................................................................................................................................
                                    </span>
                                     <span class="product-cost">1800</span>
                                </span>
                            </li>
                        </ol>
                        <div class="check-divider">
                            <div class="divider-one"></div>
                            <div class="divider-two"></div>
                        </div>
                        <div class="client-info">
                            <div class="client-name">
                <span class="client-label">
                    Ф.И.О......................................................................................................................
                </span>
                                <span class="name">
                    Катеринин Александр Андреевич
                </span>
                            </div>
                            <div class="client-discount">
                <span class="client-label">
                    Скидка..........................................................................................................................................................................................................................................
                </span>
                                <span class="discount">
                    25%
                </span>
                            </div>
                        </div>
                    </div>
                    <div class="check-divider">
                        <div class="divider-one"></div>
                        <div class="divider-two"></div>
                    </div>
                    <div class="check-footer">
                        <div class="total">
            <span class="footer-label">
                <span class="label-red">итого</span>
                к оплате..........................................................................................................................................................................................................................
            </span>
                            <span class="span-total">18 990</span>
                        </div>
                        <h5>www.iron-addicts.kz</h5>
                        <h4>Спасибо за покупку!</h4>
                    </div>
                    <div class="empty-space"></div>
                </div>
            </v-card-text>-->
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-progress-circular
                    v-if="loading"
                    indeterminate
                    color="primary"
                ></v-progress-circular>
                <v-btn text color="success" @click="onSubmit" v-else>
                    Печать
                    <v-icon>mdi-print</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import axios from 'axios';
    export default {
        data: () => ({
            loading: false,
            contentData: '',
        }),
        methods: {
            onSubmit() {
                const printContents = document.getElementById('print').innerHTML;
                const originalContents = document.body.innerHTML;
                document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
                window.print();
                document.body.innerHTML = originalContents;
            }
        },
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            sale_id: {
                type: Number,
                default: -1
            }
        },
        watch: {
            state: async function (newValue, oldValue) {
                if (newValue === true) {
                    const {data} = await axios.get(`/check/${this.sale_id}`);
                    this.contentData = data;
                    const originalContents = document.body.innerHTML;
                    document.body.innerHTML = this.contentData;
                    window.print();
                    document.body.innerHTML = originalContents;
                }
            }
        }
    }
</script>

<style lang="scss">
</style>
