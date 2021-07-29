import LoadingService from "@/utils/loadingService";
import ToastService from "@/utils/toastService";

export default function (store) {
    store.$loading = new LoadingService(store);
    store.$toast = new ToastService();
}
