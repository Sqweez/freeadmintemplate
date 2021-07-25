import iziToast from "izitoast";
import "izitoast/dist/css/iziToast.css";
import {TOAST_TYPE} from "@/config/consts";

const showToast = (message, type = TOAST_TYPE.SUCCESS, opts = {}) => {
    iziToast[type]({
        message, duration: 3000, ...opts
    })
};

export default showToast;
