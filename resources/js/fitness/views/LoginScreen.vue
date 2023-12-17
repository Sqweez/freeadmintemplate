<template>
    <div>
        <LoadingSpinner />
        <div class="login-container">
            <v-card class="login">
                <v-toolbar
                    dark
                    flat
                    color="primary"
                >
                    <v-toolbar-title>Fit | Вход</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-form>
                        <v-text-field
                            label="Логин"
                            v-model="login"
                            prepend-icon="mdi-account"
                            type="text">
                        </v-text-field>
                        <v-text-field
                            @keypress.enter="doLogin"
                            label="Пароль"
                            v-model="password"
                            prepend-icon="mdi-lock"
                            type="password"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions class="d-flex justify-center">
                    <v-btn v-if="!loading" color="primary" dark block @click="doLogin">Войти</v-btn>
                    <v-progress-circular indeterminate size="40" color="red" v-else></v-progress-circular>
                </v-card-actions>
            </v-card>
        </div>
    </div>
</template>

<script>
import LoadingSpinner from '../../components/Loaders/LoadingSpinner';

export default {
    components: {LoadingSpinner},
    data: () => ({
        login: '',
        password: '',
        loading: false,
    }),
    methods: {
        async doLogin() {
            try {
                await this.$store.dispatch('LOGIN', {
                    login: this.login,
                    password: this.password,
                });
                await this.$router.push('/fit');
            } catch (e) {
                console.log(e.response);
                this.$toast.error(e);
            }
        }
    },
}
</script>

<style scoped lang="scss">
.login-container {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login {
    width: 500px;
}
</style>
