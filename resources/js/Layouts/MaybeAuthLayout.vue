<template>
    <app-layout v-if="isAuthenticated">
        <slot/>
    </app-layout>
    <template v-else>
        <Head :title="title"/>
        <slot/>
    </template>
</template>

<style>
.page-enter-active,
.page-leave-active {
    transition: all .6s ease;
}

.page-enter-from,
.page-leave-to {
    opacity: 0;
}

</style>

<script>
import {defineComponent, ref, onMounted, computed} from 'vue'
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetBanner from '@/Jetstream/Banner'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import JetBarNavigationMenu from "@/Components/JetBarNavigationMenu";
import TopNav from "@/Components/Navigation/TopNav";
import SideNav from "@/Components/Navigation/SideNav";
import {Head, Link, usePage} from '@inertiajs/inertia-vue3';
import NotyBell from "@/Components/NotyBell";
import {useAlertEmitter} from "@/utils";
import tailwindConfig from '../../../tailwind.config.js'
import {
    setBreakpointTailwindCSS,
    useBreakpointTailwindCSS
} from "vue-composable";
import AppLayout from "@/Layouts/AppLayout";

export default defineComponent({
    components: {
        AppLayout,
        Head,
        Link,
        JetBarNavigationMenu,
        JetApplicationMark,
        JetBanner,
        JetNavLink,
        JetResponsiveNavLink,
        NotyBell,
        TopNav,
        SideNav,
    },
    props: {
        title: String,
    },
    setup() {
        // useAlertEmitter();
        //TODO: swap this out with useUser once merged with my branch
        const page = usePage();
        const isAuthenticated = computed(() => page.props.value.user != null);
        return {isAuthenticated};
    },
})
</script>
