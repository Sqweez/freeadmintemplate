import Vue from 'vue';
import App from './App.vue';
import router from './router/router';
import vuetify from "./plugins/vuetify";
import store from "./store";
import 'froala-editor/js/plugins.pkgd.min.js';
import 'froala-editor/js/third_party/embedly.min';
import 'froala-editor/js/third_party/font_awesome.min';
import 'froala-editor/js/third_party/spell_checker.min';
import 'froala-editor/js/third_party/image_tui.min';
import 'froala-editor/css/froala_editor.pkgd.min.css';
import axios from 'axios';
import './filters/filters';
import loadingPlugin from "./utils/loadingPlugin";
import JsonExcel from "vue-json-excel";
axios.defaults.withCredentials = true;

import VueFroala from 'vue-froala-wysiwyg'
import { VueEditor } from "vue2-editor";
import vuePlugins from "@/utils/vuePlugins";

Vue.use(VueFroala);
Vue.use(VueEditor);
Vue.use(loadingPlugin);
Vue.use(vuePlugins);
Vue.component('downloadExcel', JsonExcel);


Vue.config.productionTip = false;

const app = new Vue({
    el: '#app',
    router,
    store,
    vuetify,
    components: {App}
});


