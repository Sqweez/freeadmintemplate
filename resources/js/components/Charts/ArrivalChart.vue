<script>
    import { Bar } from 'vue-chartjs';

    export default {
        data: () => ({
            gradient: null
        }),
        methods: {},
        computed: {},
        name: 'SaleChart',
        extends: Bar,
        props: {
            chartData: {
                type: Object,
                default: null
            },
            options: {
                type: Object,
                default: null
            }
        },
        mounted () {
            this.renderChart(this.chartData, this.options)
        }
    }
</script>

<style scoped>

</style>
