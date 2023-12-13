import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import ru from 'vuetify/lib/locale/ru';

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
            dark: {
                error: '#E53935',
                dark: '#202020',
                success: '#43A047',
                warning: '#F57C00',
            },
        },
        dark: false,
    },
    lang: {
        current: 'ru',
        locales: { ru },
    },
});
