<template>
    <div>
       <v-card>
           <v-card-title>
               Настройки
           </v-card-title>
           <v-card-text>
               <terminal-index />
           </v-card-text>
       </v-card>
    </div>
</template>

<script>
import TerminalIndex from "@/views/Terminal/TerminalIndex";
export default {
    components: {TerminalIndex},
    data: () => ({}),
    computed: {},
    methods: {}
}
</script>

<style scoped lang="scss">

</style>
