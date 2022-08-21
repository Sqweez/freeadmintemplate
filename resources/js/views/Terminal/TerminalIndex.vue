<template>
    <div>
        <v-card elevation="0">
            <v-card-title>
                Терминал
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="IP терминала"
                    v-model="kaspiTerminalIp"
                />
                <v-btn color="success" @click="onSave">
                    Сохранить <v-icon>mdi-check</v-icon>
                </v-btn>
                <v-btn color="primary" @click="checkTerminal">
                    Проверить
                </v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
export default {
    data: () => ({
        kaspiTerminalIp: localStorage.getItem('kaspi_terminal_ip'),
    }),
    computed: {},
    methods: {
        onSave () {
            localStorage.setItem('kaspi_terminal_ip', this.kaspiTerminalIp);
            this.$toast.success('Настройки терминала изменены!');
        },
        async checkTerminal () {
            const kaspiTerminalIp = localStorage.getItem('kaspi_terminal_ip');
            if (!kaspiTerminalIp) {
                return false;
            }
            const url = `http://${kaspiTerminalIp}:8080/payment`;
            const queryParams = new URLSearchParams({
                type: 'card',
                amount: 1
            });
            const link = `${url}?${queryParams}`;
            this.awaitingForKaspiPayment = true;
            try {
                let popup = await window.open(link, Date.now().toString(), 'width=300,height=300,status=no,scrollbar=no,location=no');
                window.focus();
            } catch (e) {
                console.log(e);
            }
        }
    }
}
</script>

<style scoped lang="scss">

</style>
