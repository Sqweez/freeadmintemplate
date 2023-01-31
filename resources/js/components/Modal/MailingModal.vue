<template>
    <div>
        <base-modal
            v-bind="$attrs"
            title="Новая рассылка"
            :state="state"
            @close="$emit('close')"
            @cancel="$emit('close')"
        >
            <template #default>
                <p>Количество получателей: {{ clients.length }}</p>
                <v-select
                    label="Шаблон рассылки"
                    v-model="templateId"
                    :items="templates"
                    item-value="id"
                    item-text="name"
                />
                <v-text-field
                    label="Текст сообщений"
                    v-model="text"
                />
                <p>Доступные переменные:</p>
                <div class="d-flex flex-wrap" style="column-gap: 6px">
                    <v-btn small color="primary" v-for="variable of vars" @click="text += `${variable}`">
                        {{ variable }}
                    </v-btn>
                </div>
                <v-checkbox
                    label="Сохранить как шаблон"
                    v-model="saveAsTemplate"
                />
                <v-text-field
                    label="Название шаблона"
                    v-model="templateName"
                    v-if="saveAsTemplate"
                />
                <div class="my-2">
                    <p>Пример сообщения:</p>
                    <p>{{ messageExample }}</p>
                </div>
            </template>
            <template #actions>
                <v-btn text @click="$emit('close')">
                    Отмена
                </v-btn>
                <v-spacer />
                <v-btn color="success" text @click="onSubmit">
                    Начать рассылку <v-icon>mdi-mail</v-icon>
                </v-btn>
            </template>
        </base-modal>
    </div>
</template>

<script>
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        text: '',
        vars: [
            '%ИМЯ%'
        ],
        templateId: null,
        saveAsTemplate: false,
        templateName: '',
    }),
    computed: {
        templates () {
            return [];
        },
        messageExample () {
            return this.text.replaceAll('%ИМЯ%', 'Иван Иванович');
        }
    },
    methods: {
        async onSubmit () {
            try {
                this.$loading.enable();
                await axiosClient.post('v2/mailing', {
                    text: this.text,
                    clients: this.clients.map(c => ({
                        client_id: c.id
                    })),
                    template_name: this.templateName,
                    saveAsTemplate: this.saveAsTemplate
                })
                this.clients.forEach(client => {
                    const message = this.text.replaceAll('%ИМЯ%', client.client_name);
                    const url = `https://api.whatsapp.com/send?phone=${client.client_phone}&text=${message}`;
                    const a = document.createElement('a');
                    a.href = url;
                    a.target = '_blank';
                    a.click();
                });
            } catch (e) {
                this.$toast.error('Произошла ошибка!')
            } finally {
                this.$loading.disable();
            }
        }
    },
    props: {
        clients: {
            type: Array,
            required: true,
        },
        state: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        state () {
            this.text = '';
        }
    }
}
</script>

<style scoped lang="scss">

</style>
