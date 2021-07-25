export default {
    data: () => ({}),
    props: {
        state: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        onCancel() {
            this.$emit('cancel');
        }
    }
}
