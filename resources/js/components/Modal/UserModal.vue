<template>
    <v-dialog max-width="900" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ id !== null ? 'Редактирование' : 'Добавление' }} пользователя</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-2">
                    <v-text-field label="Имя" v-model.trim="user.name" autofocus/>
                    <v-text-field label="Логин" v-model.trim="user.login"/>
                    <v-text-field
                        :label="id === null ? 'Пароль' : 'Новый пароль'"
                        type="password"
                        v-model="user.password"
                        v-if="changePass || id === null"
                    />
                    <v-checkbox label="Сменить пароль?" v-model="changePass" v-if="id !== null"/>
                    <v-select
                        class="mt-3"
                        label="Роль"
                        item-value="id"
                        item-text="role_name"
                        :items="roles"
                        v-model="user.role_id"
                    />
                    <v-select
                        class="mt-3"
                        label="Город"
                        :items="stores"
                        item-text="name"
                        item-value="id"
                        v-model="user.store_id"
                    />
                </v-form>
            </v-card-text>
            <v-card-actions class="p-2" v-if="!loading">
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    type="submit"
                    color="success"
                    @click="submit"
                >
                    <b>Создать</b>
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
            <v-progress-linear
                indeterminate
                :active="loading"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
        </v-card>
    </v-dialog>
</template>

<script>
    import ACTIONS from "@/store/actions";

    export default {
        watch: {
            state() {
                this.user = {};
                if (this.id !== null) {
                    this.user = {...this.$store.getters.user(this.id)};
                }
            }
        },
        data: () => ({
            loading: false,
            user: {},
            changePass: false,
        }),
        props: {
            state: {
                type: Boolean,
                default: true,
            },
            id: {
                type: Number,
                default: null,
            }
        },
        computed: {
            roles() {
                return this.IS_BOSS ? this.$store.getters.user_roles : this.$store.getters.user_roles.filter(r => r.id !== 8);
            },
            stores() {
                return this.$store.getters.stores;
            },
            shops() {
                return this.$store.getters.shops;
            }
        },
        methods: {
            async createUser() {
                const user = {
                    name: this.user.name,
                    login: this.user.login,
                    password: this.user.password,
                    role_id: this.user.role_id,
                    store_id: this.user.store_id
                };

                await this.$store.dispatch(ACTIONS.CREATE_USER, user);

                this.$toast.success('пользователь создан')
            },
            async editUser() {
                const user = {
                    id: this.id,
                    name: this.user.name,
                    login: this.user.login,
                    role_id: this.user.role_id,
                    store_id: this.user.store_id
                };

                if (this.changePass) {
                    user.password = this.user.password;
                }

                await this.$store.dispatch(ACTIONS.EDIT_USER, user);

                this.$toast.success('пользователь отредактирован')

            },
            async submit() {
                if (this.id !== null) {
                    await this.editUser();
                } else {
                    await this.createUser();
                }
                this.$emit('onSubmit')
            }
        }
    }
</script>

