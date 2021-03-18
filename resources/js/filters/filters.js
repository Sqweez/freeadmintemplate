import Vue from 'vue';

Vue.filter('priceFilters', value => {
    return `${new Intl.NumberFormat('ru-RU').format(Math.ceil(value))} ₸`;
});
