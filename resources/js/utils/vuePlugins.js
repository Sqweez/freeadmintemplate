import ToastService from '@/utils/toastService';
import LoadingService from '@/utils/loadingService';
import ColorService from '@/utils/colorService';
import FileService from '@/utils/fileService';
import DatePlugin from '@/utils/datePlugin';
import BarcodeService from '@/utils/barcodeService';
import store from '@/store';
import { mapGetters } from 'vuex';
import EconomyService from '@/utils/economyService';

export default {
    install(Vue, options) {
        Vue.mixin({
            methods: {
                $evaluate: (param) => eval('this.' + param),
            },
            computed: {
                ...mapGetters({
                    $stores: 'stores',
                    $shops: 'shops',
                    $storeFilters: 'store_filters',
                    $users: 'users',
                    $userFilters: 'user_filters',
                }),
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
                /* $confirm(
                    text = 'Подтвердите действие',
                    title = 'Подтвердите действие',
                    options = {},
                ) {
                    return $confirm(text, title, options);
                },*/
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
                isSeller() {
                    return this.$store.getters.IS_SELLER;
                },
                isAdmin() {
                    return this.$store.getters.IS_ADMIN;
                },
                IS_OBSERVER() {
                    return this.$store.getters.IS_OBSERVER;
                },
                IS_BOSS() {
                    return this.$store.getters.IS_BOSS;
                },
                IS_SUPERUSER() {
                    return this.IS_BOSS || this.isAdmin || this.IS_MARKETOLOG;
                },
                IS_MARKETOLOG() {
                    return this.$store.getters.IS_MARKETOLOG;
                },
                IS_STOREKEEPER() {
                    return this.$store.getters.IS_STOREKEEPER;
                },
                IS_SENIOR_SELLER() {
                    return this.$store.getters.IS_SENIOR_SELLER;
                },
                IS_LOADING_STATE() {
                    return this.$store.getters.isLoading;
                },
                IS_LOADING() {
                    return this.$store.getters.IS_LOADING;
                },
                IS_MODERATOR() {
                    return this.$store.getters.IS_MODERATOR;
                },
                IS_FRANCHISE() {
                    return this.$store.getters.IS_FRANCHISE;
                },
                HAS_SHIFT_LIST_ACCESS() {
                    return [
                        'shym',
                        'vladimir',
                        'boss',
                        'kamercel',
                        'ilya',
                    ].includes(this.$user.login);
                },
            },
        });
    },
};
