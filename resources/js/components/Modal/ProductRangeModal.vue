<template>
    <v-dialog persistent v-model="state" max-width="800">
        <v-card>
            <v-card-title class="headline justify-space-between">
                <span class="white--text">Добавление ассортимента</span>
                <v-btn icon text class="float-right" @click="$emit('cancel')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field
                        label="Штрих-код" />
                    <v-text-field
                        type="number"
                        label="Стоимость"
                    />
                    <v-divider></v-divider>
                    <h5>Атрибуты:</h5>
                    <div class="d-flex">
                        <v-select
                            style="max-width: 300px;"
                            :items="attributes"
                            label="Атрибут"
                        ></v-select>
                        <v-spacer />
                        <v-text-field
                            label="Значение"
                        ></v-text-field>
                        <v-btn icon @click="addAttributesSelect">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <div class="d-flex" v-for="(attrs, idx) of attributesSelect" :key="idx">
                        <component
                            style="max-width: 300px;"
                            :is="attrs"
                            :items="attributes"
                            label="Атрибут"
                        />
                        <v-spacer/>
                        <v-text-field
                            label="Значение"
                        ></v-text-field>
                        <v-btn icon @click="removeAttributeSelect(idx)">
                            <v-icon>mdi-minus</v-icon>
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer />
                <v-btn text @click="createProductRange" color="success">
                    Добавить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {VSelect} from 'vuetify/lib';
    export default {
        components: {VSelect},
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                type: Number,
                default: null
            }
        },
        data: () => ({
            attributes: ["Вес", "Вкус", "Цвет"],
            attributesSelect: [],
        }),
        methods: {
            addAttributesSelect() {
                this.attributesSelect.push(VSelect);
            },
            removeAttributeSelect(idx) {
                this.attributesSelect.splice(idx, 1);
            },
        }
    }
</script>

<style scoped>

</style>
