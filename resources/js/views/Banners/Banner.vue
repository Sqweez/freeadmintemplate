<template>
    <div>
        <v-card>
            <v-card-title>
                Баннеры
            </v-card-title>
            <v-card-text>
                <v-btn color="error" @click="bannerModal = true">
                    Добавить баннер <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-simple-table>
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Баннер</th>
                            <th>Описание</th>
                            <th>Активен?</th>
                            <th>Порядок</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(banner, key) of banners" :key="`banner-${banner.id}`">
                            <td>{{ key + 1 }}</td>
                            <td>
                                <img
                                    :src="`../storage/${banner.image}`"
                                    width="400"
                                    alt="">
                            </td>
                            <td>
                                {{ banner.description }}
                            </td>
                            <td>
                                <v-icon>
                                    {{ banner.is_active ? 'mdi-check' : 'mdi-cancel'}}
                                </v-icon>
                            </td>
                            <td>
                                {{ banner.order }}
                            </td>
                            <td>
                                <v-btn icon color="error" @click="currentBanner = banner; confirmationModal = true">
                                    <v-icon >
                                        mdi-close
                                    </v-icon>
                                </v-btn>
                                <v-btn
                                    @click="currentBanner = banner; bannerModal = true"
                                    icon
                                    color="primary">
                                    <v-icon>
                                        mdi-pencil
                                    </v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <BannerModal
            :state="bannerModal"
            :_banner="currentBanner"
            @cancel="closeModal"
            @confirm="onConfirm"
        />
        <ConfirmationModal
            :state="confirmationModal"
            message="Вы действительно хотите удалить выбранный баннер?"
            :on-confirm="deleteBanner"
            :on-cancel="closeConfirmationModal"
        />
    </div>
</template>

<script>
    import axios from 'axios';
    import BannerModal from "@/components/Modal/BannerModal";
    import ConfirmationModal from "@/components/Modal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, BannerModal},
        data: () => ({
            banners: [],
            bannerModal: false,
            currentBanner: {},
            confirmationModal: false,
        }),
        methods: {
            async getBanners() {
                const response = await axios.get('/api/shop/banners');
                this.banners = response.data.data;
            },
            onConfirm(banner) {
                if (Object.keys(this.currentBanner).length) {
                    this.banners = this.banners.map(b => {
                        if (b.id === banner.id) {
                            b = banner;
                        }
                        return b;
                    })
                } else {
                    this.banners.push(banner);
                }
                this.closeModal();
            },
            closeModal() {
                this.bannerModal = false;
                this.currentBanner = {};
            },
            async deleteBanner() {
                this.$loading.enable();
                await axios.delete(`/api/shop/banners/${this.currentBanner.id}`);
                this.banners = this.banners.filter(b => b.id !== this.currentBanner.id);
                this.$loading.disable();
                this.$toast.success('Баннер успешно удален');
                this.closeConfirmationModal();
            },
            closeConfirmationModal() {
                this.currentBanner = {};
                this.confirmationModal = false;
            }
        },
        computed: {},
        async created() {
            await this.getBanners();
        }
    }
</script>

<style scoped>

</style>
