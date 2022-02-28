<template>
    <Head :title="title"/>
    <div class="w-100vw">
        <jet-banner/>
        <div class="font-sans antialiased">
            <top-nav @toggle-side-nav="toggleSideNav" />
            <div class="font-sans antialiased">
                <jet-banner/>

                <div class="lg:flex flex-col lg:flex-row lg:min-h-screen w-full">

                    <!-- Sidebar -->
                    <side-nav @toggle="showingSidebar = !showingSidebar" ref="sideNav"/>
                    <!-- End Sidebar -->

                    <div class="w-full relative">
                        <!-- Content Container -->
                        <main class="flex-1 relative z-0 py-6 focus:outline-none" tabindex="0">
                            <div v-if="this.$slots.header" class="max-w-7xl mx-auto pt-3 px-4 sm:px-6 lg:px-8">
                                <!-- Title -->
                                <h1 class="text-lg font-semibold tracking-widest  uppercase dark-mode:text-white">
                                    <slot name="header"></slot>
                                </h1>
                                <!-- End Title -->
                            </div>
                            <!-- Replace with your content -->
                            <div>
                                <!-- Content -->
                                <transition name="page">
                                    <div v-if="animate" class="min-h-full lg:min-h-96 px-4 sm:px-0">
                                        <slot></slot>
                                    </div>
                                </transition>
                                <!-- End Content -->
                            </div>
                            <!-- /End replace -->
                        </main>
                        <!-- Content Container -->
                    </div>
                </div>
            </div>
        </div>
    </div>
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
import {defineComponent, ref, onMounted} from 'vue'
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetBanner from '@/Jetstream/Banner'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import JetBarNavigationMenu from "@/Components/JetBarNavigationMenu";
import TopNav from "@/Components/Navigation/TopNav";
import SideNav from "@/Components/Navigation/SideNav";
import {Head, Link} from '@inertiajs/inertia-vue3';
import NotyBell from "@/Components/NotyBell";
import {useAlertEmitter} from "@/utils";
import tailwindConfig from '../../../tailwind.config.js'
import {
    setBreakpointTailwindCSS,
    useBreakpointTailwindCSS
} from "vue-composable";

export default defineComponent({
    components: {
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
        // setBreakpointTailwindCSS(tailwindConfig);
        useAlertEmitter();

        const animate = ref(false);
        onMounted(()=>animate.value=true);
        const showingSidebar = ref(true);
        const showingNavigationDropdown = ref(false);
        const showingNotificationDropdown = ref(false);

        const sideNav = ref(null);

        const toggleSideNav = () => {
            // console.log('Toggle Size Nav', sideNav);
           sideNav.value.toggle();
        };

        return {showingSidebar, showingNavigationDropdown, showingNotificationDropdown, toggleSideNav, sideNav, animate};
    },
    methods: {
        beforeLeave(el) {
            console.log("before leave");
        },
        beforeEnter(el) {
            console.log("before enter");
        },
        enter(el, done) {
            console.log("entered");
            done();
        },
        afterEnter(el) {
            console.log("after entered");
        },
        // burgerIsClicked(menuIsOpen) {
        //     this.showMobileMenu = menuIsOpen;
        // },
        // footerLoaded() {
        //     //console.log("Footer is loaded");
        //     this.loading = false;
        // },
    }
})
</script>
