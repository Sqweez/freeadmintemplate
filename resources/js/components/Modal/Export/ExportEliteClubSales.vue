<template>
    <base-modal
        v-bind="$attrs"
        title="Экспорт покупок клиентов Elite Club"
        :state="state"
        @close="$emit('close')"
        @cancel="$emit('close')"
    >
        <template #default>
            <IDatePicker v-model="dates" custom-dates-only/>
        </template>
        <template #actions>
            <v-btn text @click="$emit('close')">
                Отмена
            </v-btn>
            <v-spacer />
            <v-btn color="success" text @click="onSubmit">
                Экспорт
            </v-btn>
        </template>
    </base-modal>
</template>

<script>
import IDatePicker from '@/components/DatePicker/DatePicker.vue';
import ReportRepository from '@/repositories/ReportRepository';
import { fileDownload } from '@/utils/helpers';

const DATE_FORMAT = 'YYYY-MM-DD';

export default {
    components: { IDatePicker },
    props: ['state'],
    data: () => ({
        dates: null,
    }),
    methods: {
        async onSubmit () {
            const params = {
                start: this.dates[0],
                finish: this.dates[1]
            };
            this.$loading.enable();
            const { data } = await ReportRepository.getExcelEliteClientReport(params);
            fileDownload(data);
            this.$loading.disable();
            //this.$emit('close');
        }
    },
    computed: {}
};
</script>

<style scoped>

</style>
