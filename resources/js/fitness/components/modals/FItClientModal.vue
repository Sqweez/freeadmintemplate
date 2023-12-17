<template>
    <base-modal
        :state="state"
        :max-width="700"
        persistent
        @cancel="$emit('cancel')"
        :title="id ? 'Редактирование клиента' : 'Создание клиента'"
    >
        <template #default>
            <v-form>
                <v-text-field
                    label="Имя"
                    v-model="client.name"
                />
                <v-text-field
                    label="Телефон"
                    v-model="client.phone"
                    id="phone_input"
                />
                <v-text-field
                    label="Карта"
                    v-model="client.pass"
                    hint="Нажмите сюда и поднесите карту к устройству считывания"
                    persistent-hint
                />
                <v-text-field
                    label="Дата рождения"
                    v-model="client.birth_date"
                    type="date"
                />
                <div v-if="avatarPreview" class="d-flex mt-2 mb-4">
                    <div style="width: 200px; height: 200px;">
                        <img :src="avatarPreview" style="width: 100%; height: 100%; object-fit: cover; object-position: top;" alt="Аватар">
                    </div>
                    <v-btn icon color="error" class="ml-3" @click="onAvatarDelete">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </div>
                <input type="file" accept="image/*" ref="fileInput" @change="onFileInputChange" class="d-none">
                <v-btn v-if="false" color="primary" @click="$refs.fileInput.click()">
                    Загрузить фото <v-icon>mdi-upload</v-icon>
                </v-btn>
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
import {__deepClone, createObjectURL, toFormData} from '@/utils/helpers';

export default {
    data: () => ({
        client: {
            name: '',
            phone: '',
            avatar: null,
            pass: '',
        },
        avatarPreview: null,
        loading: false,
        photo: null,
    }),
    mounted() {

    },
    methods: {
        appendMask () {
            const phoneInput = document.getElementById('phone_input');
            if (phoneInput) {
                const inputMask = new InputMask("+7(999)999-99-99");
                inputMask.mask(phoneInput);
            }
        },
        async onSubmit() {
            this.loading = true;
            if(this.client.hasOwnProperty('id')) {
                await this.editClient();
            } else {
                await this.createClient();
            }
        },
        async createClient() {
            if (!this.client.name) {
                this.$toast.error('Заполните поле имя!');
                return false;
            }
            if (!this.client.phone) {
                this.$toast.error('Заполните поле телефон!');
                return false;
            }

            if (!this.client.pass) {
                this.$toast.error('Заполните поле карта!');
                return false;
            }

            if (!this.client.avatar) {
                delete this.client.avatar;
            }
            try {
                await this.$store.dispatch('createClient', toFormData(this.client));
                this.$toast.success('Клиент успешно добавлен');
                this.$emit('cancel');
            } catch (e) {
                this.$toast.error('Произошла ошибка!');
            }
        },
        async editClient() {
            if (!this.client.name) {
                this.$toast.error('Заполните поле имя!');
                return false;
            }
            if (!this.client.phone) {
                this.$toast.error('Заполните поле телефон!');
                return false;
            }

            if (!this.client.pass) {
                this.$toast.error('Заполните поле карта!');
                return false;
            }

            if (this.client.avatar === this.avatarPreview || !this.client.avatar) {
                delete this.client.avatar;
            }

            try {
                await this.$store.dispatch('editClient', { payload: toFormData(this.client), id: this.client.id });
                this.$toast.success('Клиент успешно отредактирован');
                this.$emit('cancel');
            } catch (e) {
                this.$toast.error('Произошла ошибка!');
            }
        },
        onFileInputChange (e) {
            const file = e.target.files[0];
            if (!file) { return; }
            this.client.avatar = file;
            this.avatarPreview = createObjectURL(file);
            this.$refs.fileInput.value = null;
        },
        onAvatarDelete () {
            this.client.avatar = null;
            this.avatarPreview = null;
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
        state(value) {
            this.client = {
                name: '',
                phone: '',
                avatar: null,
            };

            this.avatarPreview = null;

            if (this.id) {
                this.client = __deepClone(this.$store.getters.client(this.id));
                if (this.client.avatar) {
                    this.avatarPreview = this.client.avatar;
                }
            }
            if (value) {
                setTimeout(() => {
                    this.appendMask();
                }, 500);
            }

        },
    },
}
</script>

<style>
.theme--dark input[type="date"]::-webkit-calendar-picker-indicator {
    background-color: #ccc;
}
</style>
