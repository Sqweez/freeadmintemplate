<template>
    <v-card>
        <v-responsive>
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">Продажи</span>
                <v-select :items="items" style="max-width: 300px; margin-bottom: 0!important;" class="pa-0" v-model="current"/>
            </v-card-title>
            <v-card-text class="pl-0 pr-0">
                <v-list-item-group>
                    <v-list-item v-for="(city, index) of cities" :key="index" class="darken-3" :class="index % 2 ? 'grey' : 'black'">
                        <v-list-item-content>
                            <v-list-item-title>
                                <div class="d-flex justify-space-between">
                                    <span>Павлодар</span>
                                    <span>
                                        <v-icon size="30" class="pr-3 text--darken-1" :color="index % 2 ? 'error' : 'primary'">
                                            mdi-menu-{{ index % 2 ? 'down' : 'up'}}
                                        </v-icon>
                                        300 000 тнг
                                    </span>
                                </div>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    export default {
        data: () => ({
            items: ['Сегодня', 'Текущая неделя', 'Текущий месяц', 'Последние 3 месяца'],
            cities: ['Павлодар', 'Экибастуз', 'Усть-Каменогорск', 'Семей', 'Караганда'],
            current: null,
        }),
        mounted() {
            this.current = this.items[0];
        }
    }
</script>

<style scoped>

</style>
