<template>
   <v-row v-bind="$attrs" dense>
       <FItClientModal
           :state="showClientModal"
           @cancel="showClientModal = false; clientId = null;"
       />
       <v-col cols="12" sm="12" md="9">
           <v-card >
               <v-card-title style="background-color: #5C6BC0; color: white;">
                   Клиент
               </v-card-title>
               <v-card-text>
                   <div class="pt-5 pb-0">
                       <p v-if="!client" class="body-1">
                           Для начала работы поднесите карту к устройству
                       </p>
                   </div>
               </v-card-text>
           </v-card>
       </v-col>
       <v-col cols="12" sm="12" md="3">
           <v-card>
               <v-card-title style="background-color: #8E44AD; color: white;">
                   Добавление новых клиентов
               </v-card-title>
               <v-card-text>
                   <v-btn class="mt-6" color="error" depressed block @click="showClientModal = true">
                       Добавить клиента
                   </v-btn>
               </v-card-text>
           </v-card>
       </v-col>
   </v-row>
</template>

<script>
import FItClientModal from '@/fitness/components/modals/FItClientModal.vue';

export default {
    components: {FItClientModal},
    data: () => ({
        showClientModal: false,
        clientId: null,
    }),
    methods: {},
    computed: {
        client () {
            return this.$store.getters.searchedClient;
        },
    },
};
</script>

<style scoped>

</style>
