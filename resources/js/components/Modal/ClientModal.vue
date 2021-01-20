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
                       ref="client_phone"
                       id="client_phone"
                   />
                   <v-text-field
                       label="Номер карты"
                       solo
                       v-model="client.client_card"
                   />
                   <v-text-field
                       label="Процент скидки"
                       solo
                       type="number"
                       v-model="client.client_discount"
                   />

                   <v-select
                       class="mt-3"
                       label="Город"
                       :items="cities"
                       item-text="name"
                       item-value="id"
                       v-model="client.client_city"
                   />
                   <v-checkbox
                       v-model="client.is_partner"
                       :label="`Партнер`"
                   ></v-checkbox>
               </v-form>
           </v-card-text>
           <v-card-actions>
               <v-btn text @click="$emit('cancel')">Отмена</v-btn>
               <v-spacer></v-spacer>
               <v-progress-circular
                   v-if="loading"
                   indeterminate
                   color="primary"
               ></v-progress-circular>
               <v-btn text color="success" @click="onSubmit" v-else>
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
    import InputMask from 'inputmask';

    export default {
        data: () => ({
            client: {},
            loading: false,
        }),
        mounted() {
            const phoneInput = document.getElementById('client_phone');
            if (phoneInput) {
                const inputMask = new InputMask("+7(999)999-99-99");
                inputMask.mask(phoneInput);
            }
        },
        methods: {
            async onSubmit() {
                this.loading = true;
                this.client.client_phone = this.modifyPhone(this.client.client_phone);
                this.client.client_discount = Math.min(Math.max(this.client.client_discount, 0), 100) || 0;
                if(this.id === null) {
                    await this.createClient();
                    this.$emit('cancel');
                } else {
                    await this.editClient();
                }
                this.loading = false;
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
            },
            modifyPhone(phone) {
                return phone.replace(/[-()]/gi, '');
            }
        },
        computed: {
            cities() {
                return [{id: -1, name: 'Город не указан'}, ...this.$store.getters.cities];
            }
        },
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
                    this.client = JSON.parse(JSON.stringify(this.$store.getters.client(this.id)))
                }
                if (this.state === true) {
                    setTimeout(() => {
                        const phoneInput = document.getElementById('client_phone');
                        if (phoneInput) {
                            const inputMask = new InputMask("+7(999)999-99-99");
                            inputMask.mask(phoneInput);
                        }
                    }, 500);
                }

            }
        },
    }
</script>

<style scoped>

</style>
