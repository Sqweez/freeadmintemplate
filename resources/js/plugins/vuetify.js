import Vue from 'vue';
import Vuetify from "vuetify";
import 'vuetify/dist/vuetify.min.css'
import colors from 'vuetify/lib/util/colors'

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
            dark: {
                error: '#E53935',
                dark: '#202020',
                success: '#43A047',
                warning: '#F57C00'
            },
        },
        dark: true,
    },
})

