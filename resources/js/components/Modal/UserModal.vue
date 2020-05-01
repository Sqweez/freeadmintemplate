<template>
    <v-dialog max-width="900" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ id !== null ? 'Редактирование' : 'Добавление' }} продавца</span>
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
                    <v-text-field label="Пароль" type="password" v-model="user.password" />
                    <v-select
                        class="mt-3"
                        label="Роль"
                        :items="roles"
                        v-model="user.role"
                    />
                    <v-select
                        class="mt-3"
                        label="Город"
                        :items="cities"
                        v-model="user.city"
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
    import ACTIONS from "../../store/actions";

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
            roles: ['Суперадмин', "Продавец", "Наблюдатель", "Склад"],
            cities: ["Павлодар", "Усть-Каменогорск", "Экибастуз"]
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
        methods: {
            async createUser() {
                const user = {
                    name: this.user.name,
                    login: this.user.login,
                    password: this.user.password,
                    role: this.user.role,
                    city: this.user.city
                };

                await this.$store.dispatch(ACTIONS.CREATE_USER, user);

                alert('пользователь создан')
            },
            editUser() {
                const user = {
                    id: this.id,
                    name: this.user.name,
                    login: this.user.login,
                    password: this.user.password,
                    role: this.user.role,
                    city: this.user.city
                };

                alert('пользователь отредактирован')

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

