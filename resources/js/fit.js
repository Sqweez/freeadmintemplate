import Vue from 'vue';
import router from './fitness/router/router';
import vuetify from './fitness/utils/vuetify';
import store from './store';
import GlobalComponents from '@/components/GlobalComponents';
import 'froala-editor/js/plugins.pkgd.min.js';
import 'froala-editor/js/third_party/embedly.min';
import 'froala-editor/js/third_party/font_awesome.min';
import 'froala-editor/js/third_party/spell_checker.min';
import 'froala-editor/js/third_party/image_tui.min';
import 'froala-editor/css/froala_editor.pkgd.min.css';
import axios from 'axios';
import './filters/filters';
import loadingPlugin from './utils/loadingPlugin';
import VueFroala from 'vue-froala-wysiwyg';
import { VueEditor } from 'vue2-editor';
import vuePlugins from '@/utils/vuePlugins';
import confirmationPlugin from '@/plugins/confirmationPlugin';
import FitnessApp from '@/fitness/FitnessApp.vue';

axios.defaults.withCredentials = true;

Vue.use(VueFroala);
Vue.use(VueEditor);
Vue.use(loadingPlugin);
Vue.use(vuePlugins);
Vue.use(confirmationPlugin);

Vue.config.productionTip = false;

GlobalComponents.connect();

const app = new Vue({
    el: '#app',
    router,
    store,
    vuetify,
    components: { FitnessApp },
});
