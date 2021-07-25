import LoadingService from "@/utils/loadingService";

export default function (store) {
    store.$loading = new LoadingService(store);
}
