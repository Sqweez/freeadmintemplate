<template>
    <div>

    </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';

    export default {
        data: () => ({}),
        methods: {},
        computed: {},
        async created() {
            const date = moment().subtract(7, 'days');
            let config = {
                headers: {
                    'Content-Type': 'application/vnd.api+json',
                    'X-Auth-Token': 'ULDaKPxr8fZzzxHBSj8HLc9YZ0x+VKhYdAd6vQ1NgnI=',
                    'Access-Control-Allow-Origin': '*'
                },
            };

            const response = await axios.get(`https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=1000&filter[orders][creationDate][$ge]=${date.valueOf()}&filter[orders][signatureRequired]=false&filter[orders][state]=NEW`, {}, config);


            //console.log(date.valueOf());
            //console.log(moment.now());
        }
    }
</script>

<style scoped>

</style>
