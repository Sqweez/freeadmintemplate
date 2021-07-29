<template>
    <div>
        <v-card>
            <v-card-title>
                Партнеры
            </v-card-title>
            <v-card-text>
                <v-text-field
                    class="mt-2"
                    v-model="search"
                    solo
                    clearable
                    label="Поиск"
                    single-line
                    hide-details
                ></v-text-field>
                <v-data-table
                    :search="search"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :headers="headers"
                    :items="partners_stats"
                    :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                    <template v-slot:item.balance="{ item }">
                        {{ item.balance }}₸
                    </template>
                    <template v-slot:item.actions="{ item }">
                       <v-btn icon @click="currentPartner = item; informationModal = true;">
                           <v-icon>
                               mdi-information-outline
                           </v-icon>
                       </v-btn>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <PartnerStatModal
            :state="informationModal"
            :partner="currentPartner"
            @close="informationModal = false; currentPartner = {}"
        />
    </div>
</template>

<script>
    import PartnerStatModal from "../../components/Modal/PartnerStatModal";
    export default {
        components: {PartnerStatModal},
        data: () => ({
            search: '',
            currentPartner: {},
            informationModal: false,
            headers: [
                {
                    value: 'client_name',
                    text: 'Имя'
                },
                {
                    value: 'client_phone',
                    text: 'Телефон'
                },
                {
                    value: 'city',
                    text: 'Город'
                },
                {
                    value: 'balance',
                    text: 'Баланс'
                },
                {
                    value: 'actions',
                    text: 'Действие'
                }
            ],
        }),
        methods: {},
        computed: {
            partners_stats() {
                return this.$store.getters.PARTNERS_STATS;
            }
        },
        async created() {
            this.$loading.enable();
            await this.$store.dispatch('getPartnersStats');
            this.$loading.disable();
        }
    }
</script>

<style scoped>

</style>
