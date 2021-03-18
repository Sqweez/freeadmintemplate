<template>
    <v-dialog persistent max-width="300" v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Критерий</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field label="Название критерия" type="text" v-model="criteria"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="submit">
                    Создать <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import ACTIONS from "../../store/actions";

    export default {
        data: () => ({
            criteria: '',
        }),
        methods: {
            async submit() {
                if (this.id == null) {
                    await this.$store.dispatch(ACTIONS.CREATE_CRITERIA, {
                        criteria: this.criteria,
                    });
                } else {
                    await this.$store.dispatch(ACTIONS.EDIT_CRITERIA, {
                        criteria: this.criteria,
                        id: this.id,
                    });
                }

                this.$emit('cancel');
            }
        },
        computed: {},
        watch: {
            state() {
                if (this.id == null) {
                    this.criteria = '';
                } else {
                    this.criteria = JSON.parse(JSON.stringify(this.$store.getters.CRITERIA.find(c => c.id == this.id).criteria));
                }
            }
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                type: Number,
                default: null,
            }
        }
    }
</script>

<style scoped>

</style>
