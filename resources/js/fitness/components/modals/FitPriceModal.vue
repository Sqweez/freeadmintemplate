<template>
    <base-modal
        persistent
        :max-width="1000"
        :state="state"
        :title="id === null ? 'Создать услугу' : 'Редактировать услугу'"
        @cancel="$emit('cancel')"
    >
        <template #default>
            <v-form>
                <v-text-field
                    label="Название"
                    v-model="service.name"
                />
                <v-text-field
                    label="Стоимость"
                    type="number"
                    v-model="service.price"
                />
                <v-text-field
                    v-if="!isUnlimited"
                    label="Количество посещений"
                    type="number"
                    v-model="service.visits_count"
                />
                <v-checkbox
                    label="Безлимит"
                    v-model="isUnlimited"
                />
                <v-text-field
                    label="Срок действия для клиентов (в днях)"
                    type="number"
                    v-model="service.validity_in_days"
                    persistent-hint
                    hint="По умолчанию: 1 день"
                />
                <v-text-field
                    label="Введите сумму начисления зарплаты тренеру за тренировку"
                    type="number"
                    v-model="service.trainer_amount_per_visit"
                />
            </v-form>
        </template>
        <template #actions>
            <v-btn text @click="$emit('cancel')">Отмена</v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="success" @click="onSubmit">
                Сохранить
                <v-icon>mdi-check</v-icon>
            </v-btn>
        </template>
    </base-modal>
</template>

<script>
import {__deepClone} from '@/utils/helpers';

export default {
    data: () => ({
        service: {},
        isUnlimited: false,
    }),
    computed: {},
    methods: {
        async onSubmit() {
            this.loading = true;
            if (this.isUnlimited) {
                this.service.visits_count = null;
            }
            if(this.service.hasOwnProperty('id')) {
                await this.edit();
            } else {
                await this.create();
            }
        },
        async create() {
            if (!this.service.name) {
                this.$toast.error('Заполните поле название!');
                return false;
            }
            if (!this.service.price) {
                this.$toast.error('Заполните поле стоимость!');
                return false;
            }
            if (!this.service.visits_count && !this.isUnlimited) {
                this.$toast.error('Заполните поле количество посещений!');
                return false;
            }
            if (!this.service.validity_in_days) {
                this.$toast.error('Заполните поле срок действия!');
                return false;
            }
            try {
                this.$loading.enable();
                await this.$store.dispatch('createService', this.service);
                this.$toast.success('Услуга успешно добавлена');
                this.$emit('cancel');
            } catch (e) {
                this.$toast.error('Произошла ошибка');
            } finally {
                this.$loading.disable();
            }
        },
        async edit() {
            if (!this.service.name) {
                this.$toast.error('Заполните поле название!');
                return false;
            }
            if (!this.service.price) {
                this.$toast.error('Заполните поле стоимость!');
                return false;
            }
            if (!this.service.visits_count && !this.isUnlimited) {
                this.$toast.error('Заполните поле количество посещений!');
                return false;
            }
            if (!this.service.validity_in_days) {
                this.$toast.error('Заполните поле срок действия!');
                return false;
            }
            try {
                this.$loading.enable();
                await this.$store.dispatch('updateService', this.service);
                this.$toast.success('Услуга успешно отредактирована');
                this.$emit('cancel')
            } catch (e) {
                this.$toast.error('Произошла ошибка');
            } finally {
                this.$loading.disable();
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
    },
    watch: {
        state() {
            this.service = {};
            this.isUnlimited = false;
            if (this.id) {
                this.service = __deepClone(this.$store.getters.service(this.id));
                this.isUnlimited = this.service.visits_count === null;
            }
        },
    },
}
</script>

<style>

</style>
