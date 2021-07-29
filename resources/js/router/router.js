import Vue from 'vue'
import VueRouter from "vue-router";
import routes from "./routes";
import store from "@/store";
import ToastService from "@/utils/toastService";

const $toast = new ToastService();

Vue.use(VueRouter);

const Router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
    scrollBehavior(to, from, savePos) {
        return {x: 0, y: 0};
    }
});

Router.beforeEach(async (to, from, next) => {
    if (!store.getters.LOGIN_CHECKED) {
        await store.dispatch('AUTH');
    }

    const CURRENT_ROLE = `IS_${store.getters.CURRENT_ROLE.toUpperCase()}`;
    const IS_GUEST = store.getters.IS_GUEST;
    const BASE_ROUTE = IS_GUEST ? '/login' : '/';
    const GUEST_PAGES = !!(to.meta?.CAN_ENTER?.IS_GUEST);

    const HAS_CAN_ENTER = (() => {
        return !!(
            to.meta.CAN_ENTER && Object.keys(to.meta.CAN_ENTER).length > 0
        );
    })();



    if (IS_GUEST && !GUEST_PAGES) {
        next(BASE_ROUTE);
        return;
    }

    if (HAS_CAN_ENTER) {
        const CAN_ENTER_ROLES = to.meta.CAN_ENTER;
        const CAN_ENTER = !!CAN_ENTER_ROLES[CURRENT_ROLE];
        if (!CAN_ENTER) {
            next(BASE_ROUTE);
            $toast.error('Доступ запрещен!')
            return;
        }
    }


    next();
});

export default Router;
