import toastTypes from "@/common/enums/toastTypes";
import BaseToast from "@/utils/toastService/baseToast";

export default class ToastService extends BaseToast{
    constructor() {
        super();
    }
    success(text) {
        this.show(text, toastTypes.SUCCESS);
    }

    error(text) {
        this.show(text, toastTypes.ERROR);
    }

    warning(text) {
        this.show(text, toastTypes.WARNING);
    }

    info(text) {
        this.show(text, toastTypes.INFO);
    }
}
