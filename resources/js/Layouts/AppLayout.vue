<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"
            rel="stylesheet"
        />
    </Head>
    <div class="w-100vw">
        <jet-banner />
        <div class="font-sans antialiased">
            <top-nav
                @toggle-side-nav="toggleSideNav"
                :showingSidebar="showingSidebar"
            />
            <div class="font-sans antialiased">
                <jet-banner />

                <div class="page-body">
                    <side-nav
                        @toggle="showingSidebar = !showingSidebar"
                        ref="sideNav"
                    />

                    <div class="w-full relative">
                        <div id="premaincontent"></div>
                        <main
                            class="flex-1 relative z-0 pt-6 focus:outline-none"
                            tabindex="0"
                        >
                            <div id="layout-header"></div>

                            <!-- Content -->
                            <transition name="page">
                                <div
                                    v-if="animate"
                                    class="min-h-full lg:min-h-96 px-4 sm:px-0"
                                >
                                    <template v-if="isApolloReady">
                                        <slot></slot>
                                    </template>
                                </div>
                            </transition>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <twilio-ui></twilio-ui>
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
.page-body {
    @apply lg:flex flex-col lg:flex-row w-full overflow-y-auto;
    height: calc(100vh - 128px);
}
</style>

<script setup>
import { defineComponent, ref, onMounted } from "vue";
import { useFlashAlertEmitter, useNotificationAlertEmitter } from "@/utils";

import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Head } from "@inertiajs/inertia-vue3";

import JetBanner from "@/Jetstream/Banner.vue";
import TopNav from "@/Components/Navigation/TopNav.vue";
import SideNav from "@/Components/Navigation/SideNav.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import TwilioUi from "@/Components/TwilioUi/index.vue";

const props = defineProps({
    title: {
        type: String,
    },
});

useFlashAlertEmitter();
useNotificationAlertEmitter();

const animate = ref(false);
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
const isApolloReady = ref(true);//TODO: set to false, make computed on csrf
const csrf = usePage().props.value.user.csrf_token;
console.log({csrf})
onMounted(() => (animate.value = true));
</script>
