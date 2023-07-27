<template>
    <div>
        <i-card-page title="Новое юр. лицо">
            <v-text-field
                v-model="entity.name"
                label="Наименование юридическое лица"
                persistent-hint
                hint="Введите полное наименование юридического лица"
            />
            <v-text-field
                label="ИИН/БИН"
                v-model="entity.iin"
            />
            <v-text-field
                v-model="entity.address"
                label="Юридический адрес"
            />

            <v-divider />

<!--            <div class="my-4">
                <h4>
                    Банковские счета:
                </h4>
                <div v-for="(_, idx) of bank_accounts" :key="idx">
                    <div class="d-flex" style="align-items: center;">
                        <h6>
                            Счет #{{ idx + 1 }}
                        </h6>
                        <v-btn icon color="error" class="ml-4 mt-1" @click="bank_accounts.splice(idx, 1)">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </div>
                    <v-text-field
                        label="Наименование счета"
                        hint="Для быстрого поиска"
                        persistent-hint
                        v-model="bank_accounts[idx].name"
                    />
                    <v-text-field
                        label="Наименование банка"
                        hint="АО 'KASPI BANK'"
                        persistent-hint
                        v-model="bank_accounts[idx].title"
                    />
                    <v-text-field
                        label="БИК"
                        v-model="bank_accounts[idx].BIK"
                    />
                    <v-text-field
                        label="ИИК"
                        v-model="bank_accounts[idx].IIK"
                    />
                </div>
                <v-btn small title color="success" @click="_addBankAccount">
                    Добавить <v-icon>mdi-check</v-icon>
                </v-btn>
            </div>-->

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
            address: '',
            iin: '',
            iik: '',
            bik: '',
        },
        bank_accounts: [],
    }),
    computed: {},
    methods: {
        async _onSubmit () {
            try {
                if (this._validate() !== true) {
                    return false;
                }
                this.$loading.enable();
                await this.$store.dispatch('createLegalEntity', this.entity);
                this.$toast.success('Юр лицо успешно создано');
                await this.$router.push('/legal-entity');
            } catch (e) {
                this.$toast.error('При создании юр лица произошла ошибка')
            } finally {
                this.$loading.disable();
            }
        },
        _validate () {
            if (!this.entity.name) {
                return this.$toast.error('Поле наименование должно быть заполнено');
            }
            if (!this.entity.iin) {
                return this.$toast.error('Поле ИИН/БИН должно быть заполнено');
            }

            return true;
        },
    }
}
</script>

<style scoped lang="scss">

</style>
