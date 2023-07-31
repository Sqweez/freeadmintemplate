<template>
    <div>
        <i-card-page title="Кто сегодня на смене?">
            <div
                style="height: 300px;"
                v-if="loading"
                class="text-center d-flex justify-center align-center">
                <v-progress-circular
                    :size="50"
                    color="primary"
                    indeterminate
                />
            </div>
            <div v-if="!loading">
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>Магазин</th>
                        <th>Кто на смене</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item) of list" :key="item.id">
                        <td>
                            {{ item.name }}
                        </td>
                        <td>
                            <div v-if="item.schedule.length">
                                <v-chip
                                    small
                                    v-for="(user) of item.schedule"
                                    :key="`${item}-${user.id}`"
                                    class="ma-2"
                                    color="primary"
                                    label
                                >
                                    <v-icon left>
                                        mdi-account-circle-outline
                                    </v-icon>
                                    {{ user.user.name }}
                                </v-chip>
                            </div>
                            <div v-else>
                                <v-chip
                                    small
                                    class="ma-2"
                                    color="warning"
                                    label
                                >
                                    Нет данных
                                </v-chip>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
            </div>
        </i-card-page>
    </div>
</template>

<script>
import axiosClient from '@/utils/axiosClient';

export default {
    data: () => ({
        loading: true,
        list: [],
    }),
    computed: {},
    methods: {
        async _getData() {
            try {
                this.loading = true;
                const {data} = await axiosClient.get('/v3/working-schedule/list');
                this.list = data.list;
            } catch (e) {
                //
            } finally {
                this.loading = false;
            }
        }
    },
    async mounted() {
        await this._getData();
    }
}
</script>

<style scoped lang="scss">

</style>
