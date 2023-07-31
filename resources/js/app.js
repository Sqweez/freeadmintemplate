import Vue from 'vue';
import App from './App.vue';
import router from './router/router';
import vuetify from './plugins/vuetify';
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
import Rollbar from 'rollbar';
import VueFroala from 'vue-froala-wysiwyg';
import { VueEditor } from 'vue2-editor';
import vuePlugins from '@/utils/vuePlugins';
import confirmationPlugin from '@/plugins/confirmationPlugin';

axios.defaults.withCredentials = true;

Vue.use(VueFroala);
Vue.use(VueEditor);
Vue.use(loadingPlugin);
Vue.use(vuePlugins);
Vue.use(confirmationPlugin);

Vue.config.productionTip = false;

GlobalComponents.connect();

if (process.env.VUE_APP_IS_DEVELOPMENT === 'true') {
    Vue.prototype.$rollbar = new Rollbar({
        accessToken: 'cc9fe5c1a6814e59a5496e5263396c12',
        captureUncaught: true,
        captureUnhandledRejections: true,
        payload: {
            // Track your events to a specific version of code for better visibility into version health
            code_version: '1.0.0',
            // Add custom data to your events by adding custom key/value pairs like the one below
            custom_data: 'foo',
        },
    });

    Vue.config.errorHandler = (err, vm, info) => {
        vm.$rollbar.error(err);
        throw err; // rethrow
    };
}

const app = new Vue({
    el: '#app',
    router,
    store,
    vuetify,
    components: { App },
});
