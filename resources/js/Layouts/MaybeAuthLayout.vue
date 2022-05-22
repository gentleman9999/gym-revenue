<template>
    <app-layout v-if="user">
        <slot />
    </app-layout>
    <template v-else>
        <Head :title="title" />
        <div
            class="lg:flex flex-col lg:flex-row lg:min-h-screen w-full items-center"
        >
            <slot />
        </div>
    </template>
</template>

<style>
.page-enter-active,
.page-leave-active {
    transition: all 0.6s ease;
}

.page-enter-from,
.page-leave-to {
    opacity: 0;
}
</style>

<script>
import { defineComponent, ref, onMounted, computed } from "vue";
import JetApplicationMark from "@/Jetstream/ApplicationMark";
import JetBanner from "@/Jetstream/Banner";
import JetNavLink from "@/Jetstream/NavLink";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink";
import JetBarNavigationMenu from "@/Components/JetBarNavigationMenu";
import TopNav from "@/Components/Navigation/TopNav";
import SideNav from "@/Components/Navigation/SideNav";
import { Head, Link, usePage } from "@inertiajs/inertia-vue3";
import NotyBell from "@/Components/NotyBell";
// import tailwindConfig from '../../../tailwind.config.js'
// import {
//     setBreakpointTailwindCSS,
//     useBreakpointTailwindCSS
// } from "vue-composable";
import AppLayout from "@/Layouts/AppLayout";
import { useUser } from "@/utils/useUser";

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
        const user = useUser();
        return { user };
    },
});
</script>
