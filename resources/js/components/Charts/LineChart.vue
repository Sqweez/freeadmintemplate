<script>
import { Line } from 'vue-chartjs';
import {reactiveProp} from "vue-chartjs/es/mixins";

export default {
    data: () => ({
        gradient: null
    }),
    methods: {},
    computed: {},
    name: 'LineChart',
    extends: Line,
    mixins: [reactiveProp],
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
        // this.gradient = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450)
        // this.gradient.addColorStop(0, 'rgba(255, 0,0, 0.5)')
        // this.gradient.addColorStop(0.5, 'rgba(255, 0, 0, 0.25)');
        // this.gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');
        this.renderChart(this.chartData, this.options)
    }
}
</script>

<style scoped>

</style>
