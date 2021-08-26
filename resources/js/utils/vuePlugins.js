import ToastService from "@/utils/toastService";
import LoadingService from "@/utils/loadingService";
import ColorService from "@/utils/colorService";
import store from "@/store";

export default {
    install(Vue, options) {
        Vue.mixin({
            methods: {
                $evaluate: param => eval('this.'+param)
            },
            computed: {
                $toast() {
                    return new ToastService();
                },
                $loading() {
                    return new LoadingService(store)
                },
                $color() {
                    return new ColorService();
                },
                isSeller() {
                    return this.$store.getters.IS_SELLER;
                },
                isAdmin() {
                    return this.$store.getters.IS_ADMIN;
                },
                IS_BOSS() {
                    return this.$store.getters.IS_BOSS;
                },
                IS_SUPERUSER() {
                    return this.IS_BOSS || this.isAdmin;
                }
            }
        })
    }
}
