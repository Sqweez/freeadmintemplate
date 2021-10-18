<template>
    <div>
        <v-card>
            <v-card-title>
                Настройка футера
            </v-card-title>
            <v-card-text>
                <h5>Адреса магазинов</h5>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Город</th>
                        <th>Магазин</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(shop, idx) of addresses" :key="`shop-${shop.city}`">
                        <td>{{ idx + 1 }}</td>
                        <td>
                            <v-text-field
                                label="Город"
                                v-model="shop.city"
                            />
                        </td>
                        <td>
                            <v-text-field
                                label="Адрес"
                                v-model="shop.address"
                            />
                        </td>
                        <td>
                            <v-btn icon text color="error" @click="deleteAddress(idx)">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <v-btn color="success" style="float: right;" @click="addAddress">+</v-btn>
                <h5>Контакты</h5>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Город</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Удалить</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(shop, idx) of contacts" :key="`shop-${idx}`">
                        <td>{{ idx + 1 }}</td>
                        <td>
                            <v-text-field
                                label="Город"
                                v-model="shop.city"
                            />
                        </td>
                        <td>
                            <v-text-field
                                label="Имя"
                                v-model="shop.name"
                            />
                        </td>
                        <td>
                            <v-text-field
                                label="Телефон"
                                v-model="shop.phone"
                            />
                        </td>
                        <td>
                            <v-btn icon text color="error" @click="deleteContact(idx)">
                                <v-icon>mdi-cancel</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <v-btn color="success" style="float: right;" @click="addContact">+</v-btn>
                <v-btn color="success" @click="onSubmit">Сохранить</v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data: () => ({
            addresses: [],
            contacts: [],
        }),
        async created() {
            this.$loading.enable();
            const { data } = await axios.get('/api/shop/footer');
            this.addresses = data.addresses;
            this.contacts = data.contacts;
            this.$loading.disable();
        },
        methods: {
            async onSubmit() {
                this.$loading.enable();
                await axios.post(`/api/v2/site/footer`, {
                    addresses: this.addresses,
                    contacts: this.contacts
                })
                this.$loading.disable();
                this.$toast.show('Данные обновлены');
            },
            deleteAddress(idx) {
                this.addresses.splice(idx, 1);
            },
            deleteContact(idx) {
                this.contacts.splice(idx, 1);
            },
            addAddress() {
                this.addresses.push({
                    city: '',
                    address: ''
                })
            },
            addContact() {
                this.contacts.push({
                    city: '',
                    name: '',
                    phone: '',
                })
            }
        },
        computed: {}
    }
</script>

<style scoped>

</style>
