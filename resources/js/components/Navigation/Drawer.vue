<template>
    <div class="mdl-layout__drawer">
        <header>ironaddicts</header>
        <div class="scroll__wrapper" id="scroll__wrapper">
            <div class="scroller" id="scroller">
                <div class="scroll__container" id="scroll__container">
                    <nav class="mdl-navigation">
                        <DrawerLink v-for="(link, key) of navigations"
                                    :link="link"
                                    :key="key" />
                    </nav>
                </div>
            </div>
            <div class='scroller__bar' id="scroller__bar"></div>
        </div>
    </div>
</template>

<script>
    import DrawerLink from "./DrawerLink";
    export default {
        data: () => ({}),
        components: {
          DrawerLink
        },
        computed: {
            navigations() {
                if (this.is_observer) {
                    return this.$store.getters.OBSERVER_NAVIGATIONS;
                }
                if (this.is_seller) {
                    return this.$store.getters.SELLER_NAVIGATIONS;
                }
                return this.$store.getters.navigations;
            },
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
            is_observer() {
                return this.$store.getters.IS_OBSERVER;
            },
            is_seller() {
                return this.$store.getters.IS_SELLER;
            }
        },
        methods: {
            openSubMenu(e) {
                if (e.target.parentElement.classList.contains('sub-navigation')) {
                    // e.target.parentElement.classList.toggle('sub-navigation--show');
                    // e.target.classList.toggle('mdl-navigation__link--current');
                }
            }
        }
    }
</script>

<style scoped>

</style>
