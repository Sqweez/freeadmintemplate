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
Vue.use(Vuex);

const store = new Store({
    state: {},
    mutations: {},
    actions: {
        async [ACTIONS.INIT] ({dispatch}) {
            dispatch(ACTIONS.GET_PRODUCT);
            dispatch(ACTIONS.GET_CLIENTS);
            dispatch(ACTIONS.GET_MANUFACTURERS);
            dispatch(ACTIONS.GET_ATTRIBUTES);
            dispatch(ACTIONS.GET_CATEGORIES);
            dispatch(ACTIONS.GET_STORES);
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
        transferModule
    }
});

export default store;
