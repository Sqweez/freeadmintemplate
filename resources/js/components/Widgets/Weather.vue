<template>
    <v-card elevation="1">
        <v-responsive v-if="!loading" min-height="400">
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">Погода</span>
                <span class="body-2 text-color--gray">
                          <v-icon class="text-color--gray">
                              mdi-map-marker
                          </v-icon>
                          {{ weather.name }}
                      </span>
            </v-card-title>
            <v-card-text style="padding: 0;">
                <div class="text-right mr-5 mt-10">
                    <span class="display-4 font-weight-medium text-shadow">{{ Math.floor(weather.main.temp) }}&deg</span><br><br>
                    <span class="body-1 text-shadow">
                                        <v-icon class="pr-2">
                                            mdi-apple-icloud
                                        </v-icon>{{ weather.weather[0].description }}</span>
                </div>
            </v-card-text>
        </v-responsive>
        <v-responsive
            :min-height="400"
            v-else
            class="text-center d-flex justify-center align-center">
            <v-progress-circular
                :size="50"
                color="primary"
                indeterminate
            ></v-progress-circular>
        </v-responsive>
    </v-card>
</template>

<script>

    export default {
        data: () => ({
            weather: {},
            loading: true,
        }),
        async created() {
            const url = `https://api.openweathermap.org/data/2.5/weather?q=${this.user.store_slug}&appid=31395ab117614ed1542befb54b91d748&lang=ru&units=metric`;
            try {
                const response = await fetch(url, {
                    method: 'GET'
                });
                this.weather = await response.json();
            } catch (e) {
                console.log(e);
            }
            this.loading = false;
        },
        computed: {
            user() {
                return this.$store.getters.USER;
            }
        }
    }
</script>

<style scoped>
    .text-shadow {
        text-shadow: 4px 4px 4px rgba(0, 0, 0, 0.4);
    }
</style>
