<template>
    <v-card>
        <FItClientModal
            :state="showClientModal"
            @cancel="showClientModal = false"
            :selected-client="null"
            :id="null"
        />
        <v-card-title style="background-color: #8E44AD; color: white;">
            Добавление новых клиентов
        </v-card-title>
        <v-card-text>
            <v-btn class="mt-6" color="error" depressed block @click="showClientModal = true">
                Добавить клиента
            </v-btn>
        </v-card-text>
    </v-card>
</template>

<script>
import FItClientModal from '@/fitness/components/modals/FItClientModal.vue';
import {eventBus} from '@/fitness/utils/eventBus';

export default {
    components: {FItClientModal},
    data: () => ({
        showClientModal: false,
    }),
    mounted() {
        eventBus.$on('client:modal', () => {
            this.showClientModal = true;
        })
    },
    methods: {},
    computed: {},
};
</script>

<style scoped>

</style>
