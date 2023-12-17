<template>
    <base-modal
        persistent
        :max-width="1000"
        :state="state"
        :title="id === null ? 'Создать сотрудника' : 'Редактировать сотрудника'"
        @cancel="$emit('cancel')"
    >
        <template #default>
            <v-form>
                <v-text-field
                    label="Имя"
                    v-model="user.name"
                />
                <v-text-field
                    label="Логин"
                    v-model="user.login"
                />
                <v-text-field
                    label="Пароль"
                    v-model="user.password"
                    :hint="id ? 'Если пароль не нужно менять оставьте это поле пустым' : ''"
                    persistent-hint
                />
                <v-autocomplete
                    v-model="user.role_id"
                    :items="roles"
                    item-text="name"
                    item-value="id"
                    label="Должность"
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
        user: {},
    }),
    computed: {
        roles () {
            return this.$store.getters.roles;
        },
    },
    methods: {
        async onSubmit() {
            this.loading = true;
            if(this.user.hasOwnProperty('id')) {
                await this.editUser();
            } else {
                await this.createUser();
            }
        },
        async createUser() {
            if (!this.user.name) {
                this.$toast.error('Заполните поле имя!');
                return false;
            }
            if (!this.user.password) {
                this.$toast.error('Заполните поле пароль!');
                return false;
            }
            if (!this.user.login) {
                this.$toast.error('Заполните поле логин!');
                return false;
            }
            if (!this.user.role_id) {
                this.$toast.error('Заполните поле должность!');
                return false;
            }
            try {
                this.$loading.enable();
                await this.$store.dispatch('createUser', this.user);
                this.$toast.success('Сотрудник успешно добавлен');
                this.$emit('cancel');
            } catch (e) {
                this.$toast.error('Произошла ошибка');
            } finally {
                this.$loading.disable();
            }
        },
        async editUser() {
            if (!this.user.name) {
                this.$toast.error('Заполните поле имя!');
                return false;
            }
            if (!this.user.login) {
                this.$toast.error('Заполните поле логин!');
                return false;
            }
            if (!this.user.role_id) {
                this.$toast.error('Заполните поле должность!');
                return false;
            }
            try {
                this.$loading.enable();
                await this.$store.dispatch('editUser', this.user);
                this.$toast.success('Сотрудник успешно отредактирован');
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
            this.client = {};
            if (this.id) {
                this.user = __deepClone(this.$store.getters.user(this.id));
                this.user.role_id = this.user.role.id;
            }
        },
    },
}
</script>

<style>

</style>
