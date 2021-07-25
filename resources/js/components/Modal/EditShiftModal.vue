<template>
    <v-dialog
        v-model="state"
        persistent
        width="800">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Редактирование смены</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onCancel">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <v-select
                    label="Продавец"
                    :items="sellers"
                    item-value="id"
                    item-text="name"
                    v-model="sellerId"
                />
                <v-radio-group v-model="editMode">
                    <v-radio
                        :value="0"
                        label="Заменить имеющуюся"
                    />
                    <v-radio
                        :value="1"
                        label="Создать еще одну"
                    />
                    <v-radio
                        :value="2"
                        label="Удалить текущую"
                    />
                </v-radio-group>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="onCancel">
                    Отмена
                </v-btn>
                <v-spacer/>
                <v-btn color="success" text @click="onCreate">
                    Сохранить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import modal from "@/mixins/modal";

    export default {
        data: () => ({
            sellerId: null,
            editMode: 0,
        }),
        methods: {
            async onCreate() {
                this.$emit('create', {
                    sellerId: this.sellerId,
                    editMode: this.editMode
                });
            }
        },
        computed: {
            sellers() {
                return this.$store.getters.USERS_SELLERS;
            }
        },
        mixins: [modal],
        props: {
            shift: {
                type: Object,
                default: () => ({})
            }
        },
        watch: {
            state(val) {
                this.sellerId = null;
                this.editMode = 0;
                if (val) {
                    this.sellerId = this.shift.user_id;
                }
            }
        }
    }
</script>

<style scoped>

</style>
