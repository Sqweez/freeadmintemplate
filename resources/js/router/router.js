import Vue from 'vue'
import VueRouter from "vue-router";
import routes from "./routes";

Vue.use(VueRouter);

const Router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
    scrollBehavior(to, from, savePos) {
        return {x: 0, y: 0};
    }
});

export default Router;
