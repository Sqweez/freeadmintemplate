export default {
    data: () => ({
        start: null,
        startMenu: null,
        finish: null,
        finishMenu: null
    }),
    methods: {
        changeCustomDate () {
            this.$refs.startMenu.save(this.start);
            this.$refs.finishMenu.save(this.finish);
        }
    }
}
