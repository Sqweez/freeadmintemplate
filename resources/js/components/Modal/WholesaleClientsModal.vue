<template>
    <base-modal
        max-width="1000"
        :state="state"
        title="Оптовый клиент"
        @cancel="$emit('cancel')"
    >
        <template #default>
            <v-form>
                <v-text-field
                    label="ФИО"
                    solo
                    v-model="client.client_name"
                />
                <v-text-field
                    solo
                    v-model="client.client_phone"
                    ref="client_phone"
                    id="client_phone"
                    placeholder="Номер телефона"
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
                    label="Город"
                    :items="cities"
                    item-text="name"
                    item-value="id"
                    v-model="client.client_city"
                />
<!--                <v-text-field
                    label="Дата рождения"
                    v-model="client.birth_date"
                    type="date"
                />-->
                <v-select
                    v-model="client.wholesale_type_id"
                    :items="types"
                    item-text="name"
                    item-value="id"
                    label="Тип оптового клиента"
                />
                <v-radio-group column v-model="client.wholesale_status" label="Статус">
                    <v-radio
                        v-for="status of statuses"
                        :key="status.id"
                        :label="status.name"
                        :value="status.id"
                    />
                </v-radio-group>
            </v-form>
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">
                Отмена
            </v-btn>
            <v-spacer />
            <v-btn text color="success" @click="onSubmit">
                Сохранить <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>
import InputMask from 'inputmask';
import GENDERS from "@/common/enums/genders";
import ACTIONS from '@/store/actions';

export default {
    data: () => ({
        client: {
            gender: 'M',
            is_wholesale_buyer: true,
            wholesale_type_id: 1,
        },
        photo: null,
        genders: GENDERS,
    }),
    computed: {
        cities() {
            return [{id: -1, name: 'Город не указан'}, ...this.$store.getters.cities];
        },
        types () {
            return this.$store.getters.WHOLESALE_TYPES;
        },
        statuses () {
            return this.$store.getters.WHOLESALE_STATUSES;
        },
    },
    mounted() {
        const phoneInput = document.getElementById('client_phone');
        if (phoneInput) {
            const inputMask = new InputMask("+7(999)999-99-99");
            inputMask.mask(phoneInput);
        }
    },
    methods: {
        async onSubmit() {
            if (this.client.client_city === -1 || !this.client.client_city) {
                this.$toast.error('Выберите город!');
                return null;
            }
            if (!this.client.wholesale_type_id) {
                this.$toast.error('Выберите тип оптового клиента!');
                return null;
            }
            if (!this.client.gender) {
                this.client.gender = 'M';
            }
            this.client.is_wholesale_buyer = true;
            this.client.client_phone = this.modifyPhone(this.client.client_phone);
            this.client.client_discount = Math.min(Math.max(this.client.client_discount, 0), 100) || 0;
            this.client.photo = this.photo ? this.photo : '';
            if(this.id === null) {
                await this.createClient();
                this.$emit('cancel');
            } else {
                await this.editClient();
            }
        },
        async createClient() {
            try {
                this.$loading.enable();
                await this.$store.dispatch(ACTIONS.CREATE_WHOLESALE_CLIENT, this.client);
                this.$toast.success('Клиент успешно добавлен');
                return this.client;
            } catch (e) {
                console.log(e);
            } finally {
                this.$loading.disable();
            }
        },
        async editClient() {
            this.$loading.enable();
            await this.$store.dispatch(ACTIONS.EDIT_WHOLESALE_CLIENT, this.client);
            this.$toast.success('Клиент успешно отредактирован');
            this.$emit('cancel')
            this.$loading.disable();
        },
        modifyPhone(phone) {
            return phone.replace(/[-()]/gi, '');
        },
    },
    watch: {
        state() {
            this.client = {};
            if (this.id !== null) {
                const client = JSON.parse(JSON.stringify(this.$store.getters.WHOLESALE_CLIENT(this.id)))
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
    }
}
</script>

<style scoped lang="scss">

</style>
