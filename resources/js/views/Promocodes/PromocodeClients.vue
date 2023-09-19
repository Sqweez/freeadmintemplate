<template>
    <div>
        <i-card-page title="Привязка промокодов к клиентам">
            <p>
                Количество карт: {{ codesCount }}
            </p>
            <v-text-field
                label="Код с карты"
                v-model="currentCode"
                @keydown.enter="_createCardCode"
                ref="element"
            />
            <v-btn text color="success" @click="_onSubmit">
                Сохранить <v-icon>mdi-check</v-icon>
            </v-btn>
            <p>
                Привязанные карты:
            </p>
            <ul>
                <li v-for="code of codes">
                    {{ code }}
                </li>
            </ul>
        </i-card-page>
    </div>
</template>

<script>
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        codes: [],
        currentCode: '',
    }),
    computed: {
        codesCount () {
            return this.codes.length;
        }
    },
    mounted() {
        this.$refs.element.focus();
    },
    methods: {
        _createCardCode () {
            if (this.codes.includes(this.currentCode)) {
                this.currentCode = '';
                return this.$toast.warning('Данная карта уже привязана');
            }
            this.codes.push(this.currentCode);
            this.currentCode = '';
            this.$refs.element.focus();
        },
        async _onSubmit () {
            try {
                this.$loading.enable();
                await axiosClient.post(`/promocode/${this.$route.params.id}/clients`, {
                    codes: this.codes,
                });
                this.$router.back();
            } catch (e) {

            } finally {
                this.$loading.disable();
            }
        }
    }
}
</script>

<style scoped lang="scss">

</style>
