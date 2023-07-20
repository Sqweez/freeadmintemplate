<template>
    <div>

    </div>
</template>

<script>
import WayBillModal from '@/components/Modal/WayBillModal';
import axios from 'axios';
import {fileDownload} from '@/utils/helpers';

export default {
    components: {WayBillModal},
    data: () => ({
        showWaybillModal: false,
        showInvoiceModal: false,
        showInvoicePaymentModal: false,
        showProductCheckModal: false,
        cart: [],
        discountPercent: 0,
    }),
    computed: {},
    methods: {
        _createWaybill () {
            this.showWaybillModal = true;
        },
        _createInvoice () {
            this.showInvoiceModal = true;
        },
        _createPaymentInvoice () {
            this.showInvoicePaymentModal = true;
        },
        _createProductCheck () {
            this.showProductCheckModal = true;
        },
        async _onInvoicePaymentCreate (customer) {
            try {
                this.showInvoicePaymentModal = false;
                this.$loading.enable();
                const {data} = await axios.post(`/api/v3/documents/invoice-payment`, {
                    ...customer,
                    cart: this.cart.map(c => {
                        c.discount = Math.max(c.discount, this.discountPercent);
                        return c;
                    }),
                })
                fileDownload(data.path)
            } catch (e) {
                this.$toast.error('При создании счет-фактуры произошла ошибка!');
            } finally {
                this.$loading.disable();
            }
        },
        async _onProductCheckCreate (customer) {
            try {
                this.showProductCheckModal = false;
                this.$loading.enable();
                const { data } = await axios.post(`/api/v3/documents/products/check`, {
                    ...customer,
                    cart: this.cart.map(c => {
                        c.formatted_product_price = new Intl.NumberFormat('ru-RU').format(Math.ceil(c.product_price));
                        c.discount = Math.max(c.discount, this.discountPercent);
                        return c;
                    }),
                });
                fileDownload(data.path);
            } catch (e) {
                this.$toast.error('При создании накладной произошла ошибка!');
            } finally {
                this.$loading.disable();
            }
        },
        async _onWaybillCreate (payload) {
            try {
                this.showWaybillModal = false;
                this.$loading.enable();
                const {data} = await axios.post(`/api/v3/documents/waybill`, {
                    ...payload,
                    cart: this.cart.map(c => {
                        c.formatted_product_price = new Intl.NumberFormat('ru-RU').format(Math.ceil(c.product_price));
                        c.discount = Math.max(c.discount, this.discountPercent);
                        return c;
                    }),
                })
                fileDownload(data.path);
            } catch (e) {
                this.$toast.error('При создании накладной произошла ошибка!');
            } finally {
                this.$loading.disable();
            }
        },
        async _onInvoiceCreate (invoiceData) {
            try {
                this.showInvoiceModal = false;
                this.$loading.enable();
                const { data } = await axios.post(`/api/v3/documents/invoice`, {
                    ...invoiceData,
                    cart: this.cart.map(c => {
                        c.discount = Math.max(c.discount, this.discountPercent);
                        return c;
                    }),
                })
                fileDownload(data.path);
            } catch (e) {
                this.$toast.error('При создании счет-фактуры произошла ошибка!');
            } finally {
                this.$loading.disable();
            }
        }
    }
}
</script>

<style scoped lang="scss">

</style>
