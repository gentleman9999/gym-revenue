<template>
    <nav
        class="navbar sticky top-0 z-20 lg:flex p-0"
        :class="
            'is_being_impersonated' in $page.props.user
                ? 'bg-error'
                : 'bg-secondary'
        "
    >
        <!-- Logo -->
        <div
            class="flex-shrink-0 flex-grow lg:flex-grow-0 flex items-center bg-primary self-stretch w-72 justify-center"
        >
            <inertia-link :href="route('dashboard')">
                <jet-application-mark class="block h-8 w-auto" />
            </inertia-link>
        </div>
        <!-- Primary Navigation Menu -->
        <div class="max-w-8xl">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                        <jet-nav-link
                            href="#"
                            :active="route().current('data.conversions')"
                            @click="comingSoon()"
                        >
                            Getting Started
                        </jet-nav-link>

                        <jet-nav-link
                            href="#"
                            :active="route().current('data.conversions')"
                            @click="comingSoon()"
                        >
                            Overview
                        </jet-nav-link>

                        <jet-nav-link
                            href="#"
                            :active="route().current('data.conversions')"
                            @click="comingSoon()"
                        >
                            Favorites
                        </jet-nav-link>

                        <jet-nav-link
                            href="#"
                            :active="route().current('data.conversions')"
                            @click="comingSoon()"
                        >
                            Alerts
                        </jet-nav-link>

                        <div
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                        >
                            <locations-dropdown
                                v-if="$page.props.user.all_locations.length > 0"
                            />
                        </div>

                        <jet-nav-link
                            href="#"
                            :active="route().current('data.conversions')"
                            @click="comingSoon()"
                        >
                            Shared
                        </jet-nav-link>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <div
                        class="ml-3 relative"
                        v-if="!('is_being_impersonated' in $page.props.user)"
                    >
                        <teams-dropdown
                            v-if="$page.props.jetstream.hasTeamFeatures"
                        />
                    </div>

                    <noty-bell></noty-bell>
                    <global-search placeholder="Search" size="xs" />

                    <!-- Settings Dropdown -->
                    <div class="ml-3 relative">
                        <settings-dropdown :logout="logout" />
                    </div>
                    <!--                    hidden until we enable themes.-->
                    <!--                    <div>-->
                    <!--                        <button @click="toggleTheme">-->
                    <!--                            <font-awesome-icon-->
                    <!--                                v-if="theme === 'light'"-->
                    <!--                                :icon="['fa', 'sun']"-->
                    <!--                                size="md"-->
                    <!--                            />-->
                    <!--                            <font-awesome-icon-->
                    <!--                                v-if="theme === 'dark'"-->
                    <!--                                :icon="['fa', 'moon']"-->
                    <!--                                size="md"-->
                    <!--                            />-->
                    <!--                        </button>-->
                    <!--                    </div>-->
                </div>
                <!-- Hamburger -->
                <div class="flex items-center lg:hidden">
                    <button
                        @click="toggleSideNav"
                        class="h-full rounded-none px-6 inline-flex items-center justify-center p-2 rounded-md focus: transition"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: !showingSidebar,
                                    'inline-flex': showingSidebar,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: showingSidebar,
                                    'inline-flex': !showingSidebar,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import { defineComponent, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { comingSoon } from "@/utils/comingSoon.js";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faBars,
    faPlusCircle,
    faQuestionCircle,
    faSearch,
    faTh,
    faUserCircle,
    faSun,
    faMoon,
} from "@fortawesome/pro-solid-svg-icons";
import { faFileMedical } from "@fortawesome/pro-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import TeamsDropdown from "@/Components/Navigation/TeamsDropdown.vue";
import LocationsDropdown from "@/Components/Navigation/LocationsDropdown.vue";
import SettingsDropdown from "@/Components/Navigation/SettingsDropdown.vue";
import { Head } from "@inertiajs/inertia-vue3";
import NotyBell from "@/Components/NotyBell.vue";
import GlobalSearch from "@/Components/GlobalSearch.vue";

library.add(
    faBars,
    faFileMedical,
    faSearch,
    faPlusCircle,
    faQuestionCircle,
    faTh,
    faUserCircle,
    faSun,
    faMoon
);

export default defineComponent({
    components: {
        Head,
        JetApplicationMark,
        JetNavLink,
        FontAwesomeIcon,
        NotyBell,
        GlobalSearch,
        TeamsDropdown,
        LocationsDropdown,
        SettingsDropdown,
    },
    props: {
        title: String,
        showingSidebar: Boolean,
    },
    setup(props, { emit }) {
        const showingNotificationDropdown = ref(false);

        const logout = () => {
            Inertia.post(route("logout"));
        };
        const toggleSideNav = () => {
            emit("toggleSideNav");
        };
        const theme = ref(localStorage.getItem("theme") || "dark");

        const toggleTheme = () => {
            theme.value = theme.value === "dark" ? "light" : "dark";
            localStorage.setItem("theme", theme.value);
            document.getElementsByTagName("html")[0].dataset.theme =
                theme.value;
        };

        return {
            showingNotificationDropdown,
            logout,
            comingSoon,
            toggleSideNav,
            theme,
            toggleTheme,
        };
    },
});
</script>
