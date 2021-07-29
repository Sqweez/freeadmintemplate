<template>
    <div class="mt-5">
        <v-btn color="error" class="float-right d-block" @click="createMode = true" v-if="!createMode">
            Добавить атрибут
            <v-icon>mdi-plus</v-icon>
        </v-btn>
        <br><br>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(attr, index) of attributes" :key="index">
                    <td style="max-width: 100px;">
                        <span v-if="!editMode || attr.id !== attribute.id">
                            {{ attr.attribute_name }}
                        </span>
                        <v-text-field
                            v-else
                            label="Наименование"
                            v-model="attribute.attribute_name"
                        />
                    </td>
                    <td>
                        <div v-if="!editMode || attribute.id !== attr.id">
                            <v-btn icon @click="attribute = {...attr}; editMode = true;">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="attributeId = attr.id; deleteModal = true">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                        <div v-else>
                            <v-btn icon @click="cancelEditing">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                            <v-btn icon @click="editAttribute">
                                <v-icon>mdi-check</v-icon>
                            </v-btn>
                        </div>
                    </td>
                </tr>
                <tr v-if="createMode">
                    <td>
                        <v-text-field
                            label="Наименование"
                            v-model="attribute.attribute_name"
                        />
                    </td>
                    <td>
                        <v-btn icon @click="cancelCreation">
                            <v-icon>mdi-cancel</v-icon>
                        </v-btn>
                        <v-btn icon @click="createAttribute">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранный атрибут?"
            v-on:cancel="attributeId = null; deleteModal = false"
            :on-confirm="deleteAttribute"
        />
    </div>
</template>

<script>
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {
            ConfirmationModal
        },
        data: () => ({
            createMode: false,
            attribute: {},
            attributeId: null,
            deleteModal: false,
            editMode: false,
        }),
        methods: {
            cancelEditing() {
                this.attribute = {};
                this.editMode = false;
            },
            cancelCreation() {
                this.attribute = {};
                this.createMode = false;
            },
            async createAttribute() {
                console.log(this.attribute);
                await this.$store.dispatch(ACTIONS.CREATE_ATTRIBUTE, this.attribute);
                this.cancelCreation();
                this.$toast.success('Атрибут успешно добавлен')
            },
            async editAttribute() {
                await this.$store.dispatch(ACTIONS.EDIT_ATTRIBUTE, this.attribute);
                this.cancelEditing();
                this.$toast.success('Атрибут успешно отредактирован')
            },
            async deleteAttribute() {
                await this.$store.dispatch(ACTIONS.DELETE_ATTRIBUTE, this.attributeId);
                this.attributeId = null;
                this.deleteModal = false;
                this.$toast.success('Атрибут удален')
            }
        },
        computed: {
            attributes() {
                return this.$store.getters.attributes;
            }
        }
    }
</script>

<style scoped>

</style>
