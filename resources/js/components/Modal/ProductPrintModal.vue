<template>
    <v-dialog max-width="800" persistent v-model="state">
        <v-card>
            <v-card-title class="headline d-flex justify-space-between">
                <span class="white--text">Печать этикеток</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Вид печати"
                    :items="options"
                    item-value="id"
                    item-text="name"
                    v-model="currentOption"
                />
                <v-text-field
                    label="Количество этикеток"
                    v-model.number="count"
                    type="number"
                />
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">
                    Отмена
                </v-btn>
                <v-spacer />
                <v-btn text color="success" @click="onPrint">
                    Печать <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    data: () => ({
        currentOption: 1,
        options: [
            {
                id: 1,
                name: 'Печать ценника'
            },
            {
                id: 2,
                name: 'Печать ценника Kaspi'
            },
            {
                id: 3,
                name: 'Печать штрихкода'
            }
        ],
        count: 1,
    }),
    computed: {
        currentUrl () {
            const url = '/print/';
            switch (this.currentOption) {
                case 1:
                    return `${url}price/${this.product.id}`;
                case 2:
                    return `${url}price/${this.product.id}`;
                case 3:
                    return `${url}barcode/${this.product.id}`;
            }
        },
    },
    methods: {
        onPrint () {
            const params = {
                count: this.count
            };

            if (this.currentOption === 2) {
                params.type = 'kaspi';
            }
            const url = `${this.currentUrl}?${new URLSearchParams(params)}`
            const link = document.createElement('a');
            link.target = '_blank';
            link.href = url;
            link.click();
        },
    },
    props: {
        product: {
            type: Object,
            required: true,
        },
        state: {
            type: Boolean,
            default: false,
        }
    }
}
</script>

<style scoped lang="scss">

</style>
