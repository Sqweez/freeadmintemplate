export default {
    install(Vue, options) {
        Vue.prototype.$loading = async function () {
            await this.$store.commit('toggleLoading');
        }
    }
}
