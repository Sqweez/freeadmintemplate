<template>
   <v-dialog
       persistent
       max-width="1000"
       v-model="state"
   >
       <v-card>
           <v-card-title class="headline d-flex justify-space-between">
               <span class="white--text">{{ id === null ? 'Создать' : 'Редактировать' }} клиента:</span>
               <v-btn icon text class="float-right">
                   <v-icon color="white" @click="$emit('cancel')">
                       mdi-close
                   </v-icon>
               </v-btn>
           </v-card-title>
           <v-card-text>
               <v-form>
                   <v-text-field
                       label="ФИО"
                       solo
                       v-model="client.client_name"
                   />
                   <v-text-field
                       label="Номер"
                       solo
                       v-model="client.client_phone"
                   />
                   <v-text-field
                       label="Номер карты"
                       solo
                       v-model="client.client_card"
                   />

                   <v-text-field
                       label="Скидка"
                       solo
                       type="number"
                       v-model="client.client_discount"
                   />
               </v-form>
           </v-card-text>
           <v-card-actions>
               <v-btn text @click="$emit('cancel')">Отмена</v-btn>
               <v-spacer></v-spacer>
               <v-btn text color="success" @click="onSubmit">
                   {{ id === null ? 'Создать' : 'Редактировать' }} клиента
                   <v-icon>mdi-check</v-icon>
               </v-btn>
           </v-card-actions>
       </v-card>
   </v-dialog>
</template>

<script>
    import ACTIONS from "../../store/actions";
    import showToast from "../../utils/toast";

    export default {
        data: () => ({
            client: {},
        }),
        methods: {
            async onSubmit() {
                if(this.id === null) {
                    await this.createClient();
                    this.$emit('cancel');
                } else {
                    await this.editClient();
                }
            },
            async createClient() {
                await this.$store.dispatch(ACTIONS.CREATE_CLIENT, this.client);
                showToast('Клиент успешно добавлен');
                return this.client;
            },
            async editClient() {
                await this.$store.dispatch(ACTIONS.EDIT_CLIENT, this.client);
                showToast('Клиент успешно отредактирован');
                this.$emit('cancel')
            }
        },
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: false
            },
            id: {
                type: Number,
                default: null
            }
        },
        watch: {
            state() {
                this.client = {};
                if (this.id !== null) {
                    this.client = {...this.$store.getters.client(this.id)}
                }
            }
        },
    }
</script>

<style scoped>

</style>
