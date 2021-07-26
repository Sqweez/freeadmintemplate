<template>
    <v-dialog max-width="900" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">{{ id !== null ? 'Редактирование' : 'Добавление' }} склада</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-2">
                    <v-text-field
                        class="mt-3"
                        label="Название"
                        v-model="store.name"
                    />
                    <v-select
                        class="mt-3"
                        label="Тип"
                        :items="store_types"
                        v-model="store.type_id"
                        item-text="type"
                        item-value="id"
                    />
                    <v-autocomplete
                        class="mt-3"
                        label="Город"
                        v-model="store.city_id"
                        item-text="name"
                        item-value="id"
                        no-data-text="Нет данных"
                        :items="cities"
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
                    <b>{{ this.id !== null ? 'Сохранить' : 'Создать' }}</b>
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
                this.store = {};
                if (this.id !== null) {
                    this.store = {...this.$store.getters.store(this.id)};
                }
            }
        },
        computed: {
            store_types() {
                return this.$store.getters.store_types;
            },
            cities() {
                return this.$store.getters.cities;
            }
        },
        data: () => ({
            loading: false,
            store: {},
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
            async createStore() {
                await this.$store.dispatch(ACTIONS.CREATE_STORE, this.store);
                this.$toast.success('Склад создан')
            },
            async editStore() {
                delete this.store.type;
                await this.$store.dispatch(ACTIONS.EDIT_STORE, this.store);
                this.$toast.success('Склад отредактирован')

            },
            async submit() {
                if (this.id !== null) {
                    await this.editStore();
                } else {
                    await this.createStore();
                }
                this.$emit('onSubmit')
            }
        }
    }
</script>

