<template>
    <v-dialog persistent max-width="600" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Ценник</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div class="price-container" id="price-tag">
                    <div class="border border-top"></div>
                    <h3>{{ priceTag.product_name }}</h3>
                    <h4>{{ weight }}</h4>
                    <h5>{{ priceTag.product_price }} тг</h5>
                    <div class="border border-bottom"></div>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="download">
                    Распечать
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {toPng, toJpeg, toBlob, toPixelData, toSvgDataURL} from 'html-to-image';

    export default {
        data: () => ({
            product: {
                product_name: '',
                product_price: 0,
                attributes: []
            },
        }),
        methods: {
            download() {
                toJpeg(document.getElementById('price-tag'), {quality: 0.95})
                    .then(function (dataUrl) {
                        const link = document.createElement('a');
                        link.download = 'my-image-name.jpeg';
                        link.href = dataUrl;
                        link.click();
                    });
            }
        },
        mounted() {
        },
        watch: {
            state() {
                if (!this.state) {
                    this.product = {
                        product_name: '',
                        product_price: 0,
                        attributes: []
                    };
                } else {
                    this.product = this.priceTag;
                }
            }
        },
        computed: {
            weight() {
                if (this.product.length === 0) {
                    return '';
                }
                const attribute = this.product.attributes.find(a => a.attribute_id == 2);
                return attribute ? attribute.attribute_value : '';
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false
            },
            priceTag: {
                type: Object,
                default: {}
            }
        }
    }
</script>

<style scoped>
    @font-face {
        font-family: EuroStileRegObl;
        src: url("../../../assets/fonts/Eurostile-Regular Oblique.otf");
        font-display: swap;
    }

    @font-face {
        font-family: EuroStileHeaIta;
        src: url("../../../assets/fonts/Eurostile-HeaIta.otf");
        font-display: swap;
    }

    @font-face {
        font-family: EuroStileBlaIta;
        src: url("../../../assets/fonts/EuroStileBlaIta.otf");
        font-display: swap;
    }

    .price-container {
        width: 5.5cm;
        height: 3.5cm;
        position: relative;
        background-color: #000;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px 0;
    }

    .border {
        width: 50%;
        height: 1.5%;
        background-color: #f00;
        position: absolute;
        transform: skew(-12deg);
    }

    .border-top {
        top: 0;
        left: 0;
    }

    .border-bottom {
        right: 0;
        bottom: 0;
    }

    h3, h4, h5 {
        color: #fff;
        margin: 0;
        text-align: center;
        padding: 0;
        line-height: inherit;
    }

    h3, h5 {
        font-family: EuroStileBlaIta, sans-serif;
        font-size: 18px;
        text-transform: uppercase;
    }

    h4 {
        font-family: EuroStileRegObl, sans-serif;
        font-size: 16px;
    }
</style>
