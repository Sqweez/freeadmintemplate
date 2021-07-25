import toastTypes from "@/common/enums/toastTypes";
import iziToast from "izitoast";
import "izitoast/dist/css/iziToast.css";

export default class BaseToast {
    show(message, type = toastTypes.SUCCESS, opts = {}) {
        iziToast[type]({
            message, duration: 3000, ...opts
        })
    }
}
