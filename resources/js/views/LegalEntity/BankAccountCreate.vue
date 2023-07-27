<template>
    <div>
        <i-card-page title="Банковский аккаунт">
            <v-text-field
                label="Наименование счета"
                hint="Для быстрого поиска"
                persistent-hint
                v-model="entity.name"
            />
            <v-text-field
                label="Наименование банка"
                hint="АО 'KASPI BANK'"
                persistent-hint
                v-model="entity.title"
            />
            <v-text-field
                label="БИК"
                v-model="entity.BIK"
            />
            <v-text-field
                label="ИИК"
                v-model="entity.IIK"
            />
            <v-btn color="primary" tile @click="_onSubmit">
                Сохранить
            </v-btn>
        </i-card-page>
    </div>
</template>

<script>
export default {
    data: () => ({
        entity: {
            name: '',
            title: '',
            BIK: '',
            IIK: '',
        }
    }),
    computed: {
        id() {
            return this.$route.params.id;
        },
        account() {
            return this.$route.params?.account;
        }
    },
    mounted() {
        if (this.account) {
            const entity = this.$store.getters.legal_entity_by_id(this.id);
            const needle = entity.bank_accounts.find(b => b.id === +this.account);
            this.entity = {...needle};
        }
    },
    methods: {
        async _onSubmit() {
            try {
                if (this._validate() !== true) {
                    return false;
                }
                this.$loading.enable();
                const payload = {...this.entity};
                payload.legal_entity_id = this.id;
                if (!this.account) {
                    await this.$store.dispatch('createBankAccount', payload);
                    this.$toast.success('Банковский счет успешно создан');
                } else {
                    payload.id = this.account;
                    await this.$store.dispatch('updateBankAccount', payload);
                    this.$toast.success('Банковский счет успешно обновлен');
                }
                await this.$router.push('/legal-entity');
            } catch (e) {
                this.$toast.error('При создании банковского счета произошла ошибка')
            } finally {
                this.$loading.disable();
            }
        },
        _validate() {
            if (!this.entity.name) {
                return this.$toast.error('Поле наименование счета должно быть заполнено');
            }
            if (!this.entity.title) {
                return this.$toast.error('Поле наименование банка должно быть заполнено');
            }

            if (!this.entity.BIK) {
                return this.$toast.error('Поле БИК должно быть заполнено');
            }

            if (!this.entity.IIK) {
                return this.$toast.error('Поле ИИК должно быть заполнено');
            }

            return true;
        },
    }
}
</script>

<style scoped lang="scss">

</style>
