import Vue from 'vue'
import Vuex, {Store} from "vuex"
import navigationModule from './modules/navigation'
import userModule from './modules/users'
import storeModule from "./modules/stores";
import productsModule from "./modules/products";
import categoryModule from "./modules/categories";
import manufacturerModule from "./modules/manufactures";
import attributeModule from "./modules/attributes";
import clientModule from "./modules/clients";
import ACTIONS from './actions';
import transferModule from "./modules/transfers";
import reportsModule from "./modules/reports";
import authModule from "./modules/auth";
import goalModule from "./modules/goals";
import sportsmenModule from "./modules/sportsmen";
import plansModule from "./modules/plans";
import statsModule from "./modules/stats";
import ratingModule from "./modules/rating";
import promocodeModule from "./modules/promocode";
import frontEndModule from "./modules/frontend";
import cartModule from "./modules/cart";
import productsModule_v2 from "./modules/v2/products";
import modalModule from "@/store/modules/modals";
import createPersistedState from "vuex-persistedstate";
import orderModule from "@/store/modules/orders";
import newsModule from "@/store/modules/news";
import suppliersModule from "@/store/modules/suppliers";
import tasksModule from "@/store/modules/tasks";
import educationModule from "@/store/modules/education";
import arrivalModule from "@/store/modules/arrivals";
import preordersModule from "@/store/modules/preorders";
import shiftModule from "@/store/modules/shifts";
import motivationModule from "@/store/modules/motivation";
import vuexPlugins from "@/store/plugins/vuexPlugins";
import analyticsModule from "@/store/modules/v2/analytics";
import bookingModule from "@/store/modules/booking";

Vue.use(Vuex);


const store = new Store({
    state: {},
    mutations: {},
    actions: {
        async INIT({commit, dispatch}) {
            this.$loading.enable();
            await dispatch(ACTIONS.GET_STORES);
            await dispatch(ACTIONS.GET_STORE_TYPES);
            await dispatch(ACTIONS.GET_USERS);
            await dispatch(ACTIONS.GET_USER_ROLES);
            await dispatch(ACTIONS.GET_CITIES);
            await dispatch(ACTIONS.GET_LOYALTY);
            await dispatch(ACTIONS.GET_SALE_TYPES);
            await dispatch(ACTIONS.OPEN_SHIFT);
            await dispatch(ACTIONS.GET_MARGIN_TYPES);
            this.$loading.disable();
        }
    },
    modules: {
        navigationModule,
        userModule,
        storeModule,
        productsModule,
        categoryModule,
        manufacturerModule,
        attributeModule,
        clientModule,
        transferModule,
        reportsModule,
        authModule,
        goalModule,
        sportsmenModule,
        plansModule,
        statsModule,
        ratingModule,
        promocodeModule,
        frontEndModule,
        productsModule_v2,
        modals: modalModule,
        cartModule,
        orderModule,
        newsModule,
        suppliersModule,
        tasksModule,
        educationModule,
        arrivalModule,
        preordersModule,
        shiftModule,
        motivationModule,
        analyticsModule,
        bookingModule
    },
    plugins: [
        createPersistedState({
            paths: [
                'reportsModule.storesReports',
                'reportsModule.planReports',
                'arrivalModule.currentArrival'
            ]
        }),
        vuexPlugins
    ],
});


export default store;
