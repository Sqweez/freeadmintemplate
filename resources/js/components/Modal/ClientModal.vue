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

                   <v-autocomplete
                       class="mt-3"
                       label="Город"
                       :items="cities"
                       item-text="name"
                       item-value="id"
                       v-model="client.client_city"
                   />
                   <v-select
                       v-model="client.gender"
                       class="mt-3"
                       label="Пол"
                       :items="genders"
                       item-text="value"
                       item-value="id"
                   />
                   <v-text-field
                       label="Дата рождения"
                       v-model="client.birth_date"
                       type="date"
                   />
                   <v-checkbox
                       v-model="client.is_partner"
                       :label="`Тренер`"
                   ></v-checkbox>
                   <v-select
                       v-model="client.loyalty_id"
                       label="Тип лояльности"
                       :items="loyalty"
                       item-value="id"
                       item-text="name"
                   />
                   <v-text-field
                       v-model="client.job"
                       label="Место работы"
                       v-if="client.is_partner"
                   />
                   <v-text-field
                       v-model="client.instagram"
                       label="Ссылка на instagram"
                       v-if="client.is_partner"
                   />
                   <img
                       v-if="photo"
                       :src="'../storage/' + photo"
                       width="150"
                       alt="Изображение"><br>
                   <input
                       type="file"
                       ref="fileInput"
                       class="d-none"
                       @change="uploadPhoto">
                   <v-btn text class="mt-3" @click="$refs.fileInput.click()" v-if="client.is_partner">
                       Загрузить фото
                       <v-icon>mdi-photo</v-icon>
                   </v-btn>
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
    import GENDERS from "@/common/enums/genders";
    import InputMask from 'inputmask';
    import ACTIONS from '@/store/actions/index';
    import uploadFile from "@/api/upload";
    export default {
        data: () => ({
            client: {},
            loading: false,
            photo: null,
            genders: GENDERS,
        }),
        mounted() {
            const phoneInput = document.getElementById('client_phone');
            if (phoneInput) {
                const inputMask = new InputMask("+7(999)999-99-99");
                inputMask.mask(phoneInput);
            }
        },
        async created() {
            if (this.cities.length === 1) {
                await this.$store.dispatch(ACTIONS.GET_CITIES);
            }
        },
        methods: {
            async onSubmit() {
                this.loading = true;
                if (this.client.client_city === -1 || !this.client.client_city) {
                    this.$toast.error('Выберите город!');
                    this.loading = false;
                    return null;
                }
                if (!this.client.gender) {
                    this.$toast.error('Выберите пол!');
                    this.loading = false;
                    return null;
                }
                this.client.client_phone = this.modifyPhone(this.client.client_phone);
                this.client.client_discount = Math.min(Math.max(this.client.client_discount, 0), 100) || 0;
                this.client.photo = this.photo ? this.photo : '';
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
                this.$toast.success('Клиент успешно добавлен');
                return this.client;
            },
            async editClient() {
                await this.$store.dispatch(ACTIONS.EDIT_CLIENT, this.client);
                this.$toast.success('Клиент успешно отредактирован');
                this.$emit('cancel')
            },
            modifyPhone(phone) {
                return phone.replace(/[-()]/gi, '');
            },
            async uploadPhoto(e) {
                try {
                    const file = e.target.files[0];
                    const { data } = await uploadFile(file, 'file', 'partners');
                    this.photo = data;
                } catch (e) {
                    this.$toast.error('Во время загрузки файла произошла ошибка, попробуйте загрузить другую фотографию');
                } finally {
                    this.$refs.fileInput.value = null;
                }
            },
        },
        computed: {
            cities() {
                return [{id: -1, name: 'Город не указан'}, ...this.$store.getters.cities];
            },
            loyalty() {
                return this.$store.getters.LOYALTY;
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
                    const client = JSON.parse(JSON.stringify(this.$store.getters.client(this.id)))
                    this.client = {...client};
                    this.client.client_discount = client.client_initial_discount;
                    this.photo = this.client.photo;
                }/* else {
                    this.client.loyalty_id = 1;
                }*/
                if (this.state === true) {
                    setTimeout(() => {
                        const phoneInput = document.getElementById('client_phone');
                        if (phoneInput) {
                            const inputMask = new InputMask("+7(999)999-99-99");
                            inputMask.mask(phoneInput);
                        }
                    }, 500);
                }

            },
            client: {
                deep: true,
                handler: function (value) {
                    if (!value.is_partner) {
                        this.client.job = '';
                        this.client.instagram = '';
                        this.photo = null;
                    }
                }
            }
        },
    }
</script>

<style>
.theme--dark input[type="date"]::-webkit-calendar-picker-indicator {
    background-color: #ccc;
}
</style>
