<template>
    <component
        v-if="link.isAdmin !== true || (link.isAdmin === true && is_admin)"
        :key="link.url"
        :is="link.hasDropdown ? 'div' : 'router-link'"
        style="cursor: pointer;"
        :to="link.url"
        :class="link.hasDropdown ? 'sub-navigation' : 'mdl-navigation__link'"
        @click="openSubMenu"
        active-class="mdl-navigation__link--current"
        exact
    >
                            <span v-if="!link.hasDropdown">
                                  <i class="material-icons" role="presentation">{{ link.icon }}</i>
                                    {{ link.title }}
                            </span>
        <a href="#" v-else class="mdl-navigation__link">
            <i class="material-icons" role="presentation">{{ link.icon }}</i>
            {{ link.title }}
            <i class="material-icons" v-if="link.hasDropdown"
               style="float: right">keyboard_arrow_down</i>
        </a>
        <div class="mdl-navigation" v-if="link.hasDropdown">
            <router-link
                v-if="_link.isAdmin !== true || (_link.isAdmin === true && is_admin)"
                v-for="(_link) of link.children"
                :key="_link.url"
                :to="_link.url"
                class="mdl-navigation__link"
                active-class="mdl-navigation__link--current"
                exact
            >
                {{ _link.title }}
            </router-link>
        </div>
    </component>
</template>

<script>
    export default {
        props: {
            link: {
                type: Object,
                required: true
            }
        },
        methods: {
            openSubMenu(e) {
                if (e.target.parentElement.classList.contains('sub-navigation')) {
                    e.target.parentElement.classList.toggle('sub-navigation--show');
                }
            }
        },
        computed: {
            is_admin() {
                return this.$store.getters.IS_ADMIN;
            },
            is_observer() {
                return this.$store.getters.IS_OBSERVER;
            }
        }
    }
</script>

<style scoped>

</style>
