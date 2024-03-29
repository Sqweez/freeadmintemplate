import BaseModal from '@/components/Modal/BaseModal';
import ICardPage from '@/components/utils/ICardPage';
import JsonExcel from 'vue-json-excel';
import GlobalConfirmationModal from '@/components/utils/GlobalConfirmationModal';

import Vue from 'vue';

export default {
    connect() {
        Vue.component('base-modal', BaseModal);
        Vue.component('i-card-page', ICardPage);
        Vue.component('downloadExcel', JsonExcel);
        Vue.component('global-confirmation-modal', GlobalConfirmationModal);
    },
};
