import ToastService from '@/utils/toastService';
import LoadingService from '@/utils/loadingService';
import ColorService from '@/utils/colorService';
import FileService from '@/utils/fileService';
import DatePlugin from '@/utils/datePlugin';
import BarcodeService from '@/utils/barcodeService';
import store from '@/fitness/store';
import EconomyService from '@/utils/economyService';

export default {
    install(Vue, options) {
        Vue.mixin({
            methods: {
                $evaluate: (param) => eval('this.' + param),
            },
            computed: {
                $barcode() {
                    return new BarcodeService();
                },
                $toast() {
                    return new ToastService();
                },
                $date() {
                    return new DatePlugin();
                },
                $loading() {
                    return new LoadingService(store);
                },
                $economy() {
                    return new EconomyService();
                },
                $color() {
                    return new ColorService();
                },
                $file() {
                    return new FileService();
                },
                $user() {
                    return this.$store.getters.USER;
                },
                IS_LOADING_STATE() {
                    return this.$store.getters.isLoading;
                },
                IS_LOADING() {
                    return this.$store.getters.IS_LOADING;
                },
            },
        });
    },
};
