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
        }
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
