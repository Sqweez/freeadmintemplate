<template>
    <v-card elevation="5">
        <v-responsive :max-height="400">
            <v-card-title class="d-flex justify-space-between">
                <span class="display-1">Погода</span>
                <span class="body-2 text-color--gray">
                          <v-icon class="text-color--gray">
                              mdi-map-marker
                          </v-icon>
                          Павлодар, Казахстан
                      </span>
            </v-card-title>
            <v-card-text style="padding: 0;">
                <v-img src="../../images/weather_bck.png" contain class="d-flex">
                    <div class="text-right mr-5 mt-10">
                        <span class="display-4 font-weight-medium text-shadow">-11&deg</span><br><br>
                        <span class="body-1 text-shadow">
                                        <v-icon class="pr-2">
                                            mdi-apple-icloud
                                        </v-icon>Ветер и снег</span>
                    </div>
                </v-img>
            </v-card-text>
        </v-responsive>
    </v-card>
</template>

<script>
    export default {

    }
</script>

<style scoped>
    .text-shadow {
        text-shadow: 4px 4px 4px rgba(0, 0, 0, 0.4);
    }
</style>
