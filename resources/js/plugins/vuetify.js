import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import vuetifyConfig from '@/plugins/config/vuetify.config';

Vue.use(Vuetify);

export default new Vuetify(vuetifyConfig);
