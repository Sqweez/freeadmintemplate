import Vue from 'vue';
import Vuetify from 'vuetify';
import GlobalConfirmationModal from '@/components/utils/GlobalConfirmationModal';
import vuetifyConfig from '@/plugins/config/vuetify.config';
import store from '@/store';

Vue.use(Vuetify);

const $confirm = (
    text = 'Подтвердите действие',
    title = 'Подтвердите действие',
    options = {},
) => {
    return new Promise((resolve, reject) => {
        try {
            let state = true;
            const props = {
                title,
                text,
                options,
                state,
            };
            const on = {};
            const comp = new Vue({
                store: store,
                vuetify: new Vuetify(vuetifyConfig),
                render: (h) => h(GlobalConfirmationModal, { props, on }),
            });

            on.confirmed = (val) => {
                if (val) {
                    resolve(val);
                } else {
                    reject(false);
                }
                window.setTimeout(() => comp.$destroy(), 100);
            };

            comp.$mount();
            document.getElementById('app').appendChild(comp.$el);
        } catch (err) {
            reject(err);
        }
    });
};

export default {
    install(Vue, options) {
        Vue.prototype.$confirm = $confirm;
    },
};
