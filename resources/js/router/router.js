import Vue from 'vue'
import VueRouter from "vue-router";
import routes from "./routes";
import store from "../store";

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

    const IS_OBSERVER = store.getters.IS_OBSERVER;

    if (IS_OBSERVER) {
        if (to.meta.isObserver) {
            next();
        } else {
            next('/observer');
        }
        return;
    }

    if (to.meta.isAdmin) {
        if (store.getters.IS_ADMIN) {
            next();
        } else {
            next('/');
        }
        return;
    }

    if (!to.meta.guest) {
        if (store.getters.LOGGED_IN) {
            next();
        } else {
            next('/login');
        }
    }

    if (to.meta.guest && store.getters.LOGGED_IN) {
        next('/');
    }

    next();
});

export default Router;
