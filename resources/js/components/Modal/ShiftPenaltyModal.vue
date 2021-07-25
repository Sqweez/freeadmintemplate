<template>
    <v-dialog
        v-model="state"
        persistent
        max-width="800"
    >
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Добавление количества товара</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onCancel">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-select
                        label="Продавец"
                        :items="USERS_SELLERS"
                        item-text="name"
                        item-value="id"
                        v-model="user_id"
                    />
                    <v-text-field
                        label="Комментарий"
                        v-model="comment"
                    />
                    <v-text-field
                        label="Сумма"
                        hint="Отрицательные значений для штрафов, положительные для премий"
                        v-model.number="amount"
                    />
                </v-form>
            </v-card-text>
            <v-divider />
            <v-card-actions>
                <v-btn text @click="onCancel">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">
                    Добавить
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import modal from "@/mixins/modal";
    import { mapGetters } from 'vuex';
    export default {
        data: () => ({
            user_id: null,
            comment: '',
            amount: 0,
        }),
        mixins: [modal],
        methods: {
            async onSubmit() {
                const validate = this.validate();
                if (validate) {
                    return this.$toast.error(validate);
                }

                try {
                    await this.$store.dispatch('CREATE_SHIFT_PENALTY', {
                        user_id: this.user_id,
                        comment: this.comment,
                        amount: this.amount,
                        author_id: this.author_id
                    })
                    this.$toast.success(
                        `${this.amount < 0 ? 'Штраф' : 'Премия'} успешно добавлены`
                    );
                    this.onCancel();
                } catch (e) {
                    this.$toast.error('Произошла ошибка')
                }
            },
            resetState() {
                this.user_id = null;
                this.comment = '';
                this.amount = 0;
            },
            validate() {
                if (!this.user_id) {
                    return 'Выберите пользователя';
                }

                if (this.amount === 0) {
                    return 'Введите значение штрафа/премии';
                }

                return false;
            }
        },
        computed: {
            ...mapGetters(['USERS_SELLERS']),
            author_id() {
                return this.$store.getters.USER.id;
            }
        },
        watch: {
            state() {
                this.resetState();
            }
        }
    }
</script>

<style scoped>

</style>
