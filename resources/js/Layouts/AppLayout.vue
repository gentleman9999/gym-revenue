<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"
            rel="stylesheet"
        />
    </Head>
    <div class="w-100vw bg-neutral-800">
        <jet-banner />
        <div class="font-sans antialiased">
            <top-nav
                @toggle-side-nav="toggleSideNav"
                :showingSidebar="showingSidebar"
            />
            <div class="font-sans antialiased">
                <jet-banner />

                <div
                    class="lg:flex flex-col lg:flex-row lg:min-h-screen w-full"
                >
                    <!-- Sidebar -->
                    <side-nav
                        @toggle="showingSidebar = !showingSidebar"
                        ref="sideNav"
                    />
                    <!-- End Sidebar -->

                    <div class="w-full relative">
                        <!-- Content Container -->
                        <main
                            class="flex-1 relative z-0 py-6 focus:outline-none"
                            tabindex="0"
                        >
                            <div id="layout-header"></div>
                            <!-- Replace with your content -->
                            <div>
                                <!-- Content -->
                                <transition name="page">
                                    <div
                                        v-if="animate"
                                        class="min-h-full lg:min-h-96 px-4 sm:px-0"
                                    >
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
    <InertiaModal>
        <template #default="{ close, props }">
            <DaisyModal
                v-bind="props /* props contains the modalProps option */"
                :open="true"
                @close="close"
            >
                <ModalSlot />
            </DaisyModal>
        </template>
    </InertiaModal>
</template>

<style>
#layout-header {
    @apply flex md:flex-row flex-col items-center max-w-fit mx-auto pt-3 px-4 sm:px-6 lg:px-8 text-lg font-semibold tracking-widest uppercase;
}
.page-enter-active,
.page-leave-active {
    transition: all 0.6s ease;
}

.page-enter-from,
.page-leave-to {
    opacity: 0;
}
</style>

<script setup>
import { defineComponent, ref, onMounted } from "vue";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import JetBanner from "@/Jetstream/Banner.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink.vue";
import JetBarNavigationMenu from "@/Components/JetBarNavigationMenu.vue";
import TopNav from "@/Components/Navigation/TopNav.vue";
import SideNav from "@/Components/Navigation/SideNav.vue";
import { Link } from "@inertiajs/inertia-vue3";
import NotyBell from "@/Components/NotyBell.vue";
import { useFlashAlertEmitter, useNotificationAlertEmitter } from "@/utils";
import DaisyModal from "@/Components/DaisyModal.vue";
import { InertiaModal, ModalSlot } from "@/Components/InertiaModal";
import { Head } from "@inertiajs/inertia-vue3";
const props = defineProps({
    title: {
        type: String,
    },
});
useFlashAlertEmitter();
useNotificationAlertEmitter();

const animate = ref(false);
onMounted(() => (animate.value = true));
const showingSidebar = ref(true);
const showingNavigationDropdown = ref(false);
const showingNotificationDropdown = ref(false);

const sideNav = ref(null);

const toggleSideNav = () => {
    sideNav.value.toggle();
};

const beforeLeave = (el) => {
    console.log("before leave");
};
const beforeEnter = (el) => {
    console.log("before enter");
};
const enter = (el, done) => {
    console.log("entered");
    done();
};
const afterEnter = (el) => {
    console.log("after entered");
};
</script>
