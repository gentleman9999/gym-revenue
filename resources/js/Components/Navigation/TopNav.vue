<template>
    <nav class="navbar sticky top-0 z-20 lg:flex p-0 bg-secondary">
        <!-- Logo -->
        <div
            class="flex-shrink-0 flex items-center bg-primary self-stretch w-72 justify-center"
        >
            <inertia-link :href="route('dashboard')">
                <jet-application-mark class="block h-8 w-auto" />
            </inertia-link>
        </div>

        <!-- Primary Navigation Menu -->
        <div class="max-w-8xl mx-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- <button
                        v-show="!showingSidebar"
                        @click="showingSidebar = true"
                        class="pr-2"
                    >
                        <font-awesome-icon :icon="['fas', 'bars']" size="sm"/>
                    </button> -->
                    <!-- Logo -->
                    <!-- <div class="flex-shrink-0 flex items-center bg-primary">
                                          <inertia-link :href="route('dashboard')">
                                              <jet-application-mark class="block h-8 w-auto" />
                                          </inertia-link>
                                      </div> -->

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
                    <div class="ml-3 relative">
                        <teams-dropdown
                            v-if="$page.props.jetstream.hasTeamFeatures"
                        />
                    </div>

                    <div class="ml-3 relative">
                        <button @click="comingSoon()">
                            <font-awesome-icon
                                :icon="['fas', 'search']"
                                size="sm"
                            />
                        </button>
                    </div>
                    <div class="ml-3 relative">
                        <button @click="comingSoon()">
                            <font-awesome-icon
                                :icon="['fas', 'plus-circle']"
                                size="sm"
                            />
                        </button>
                    </div>
                    <div class="ml-3 relative">
                        <button @click="comingSoon()">
                            <font-awesome-icon
                                :icon="['fas', 'question-circle']"
                                size="sm"
                            />
                        </button>
                    </div>
                    <div class="ml-3 relative">
                        <button @click="comingSoon()">
                            <font-awesome-icon
                                :icon="['fas', 'th']"
                                size="sm"
                            />
                        </button>
                    </div>
                    <div class="ml-3 relative">
                        <button @click="comingSoon()">
                            <font-awesome-icon
                                :icon="['fas', 'user-circle']"
                                size="sm"
                            />
                        </button>
                    </div>
                    <noty-bell></noty-bell>
                    <div class="ml-3 relative">
                        <button @click="comingSoon()">
                            <font-awesome-icon
                                :icon="['far', 'file-medical']"
                                size="sm"
                            />
                        </button>
                    </div>
                    <!-- Notifications -->

                    <!-- Settings Dropdown -->
                    <div class="ml-3 relative">
                        <settings-dropdown :logout="logout" />
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center lg:hidden">
                    <button
                        @click="toggleSideNav"
                        class="p-4 inline-flex items-center justify-center p-2 rounded-md hover:bg-base-100 focus:outline-none focus:bg-base-100 focus: transition"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
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

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faBars,
    faPlusCircle,
    faQuestionCircle,
    faSearch,
    faTh,
    faUserCircle,
} from "@fortawesome/pro-solid-svg-icons";
import { faFileMedical } from "@fortawesome/pro-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import JetApplicationMark from "@/Jetstream/ApplicationMark";
import JetNavLink from "@/Jetstream/NavLink";
import TeamsDropdown from "@/Components/Navigation/TeamsDropdown";
import LocationsDropdown from "@/Components/Navigation/LocationsDropdown";
import SettingsDropdown from "@/Components/Navigation/SettingsDropdown";
import { Head } from "@inertiajs/inertia-vue3";
import NotyBell from "@/Components/NotyBell";

library.add(
    faBars,
    faFileMedical,
    faSearch,
    faPlusCircle,
    faQuestionCircle,
    faTh,
    faUserCircle
);

export default defineComponent({
    components: {
        Head,
        JetApplicationMark,
        JetNavLink,
        FontAwesomeIcon,
        NotyBell,
        TeamsDropdown,
        LocationsDropdown,
        SettingsDropdown,
    },
    props: {
        title: String,
    },
    setup(props, { emit }) {
        const showingSidebar = ref(false);
        const showingNavigationDropdown = ref(false);
        const showingNotificationDropdown = ref(false);

        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        };
        const logout = () => {
            Inertia.post(route("logout"));
        };
        const toggleSideNav = () => {
            showingNavigationDropdown.value = !showingNavigationDropdown.value;
            emit("toggleSideNav");
        };
        return {
            showingSidebar,
            showingNavigationDropdown,
            showingNotificationDropdown,
            logout,
            comingSoon,
            toggleSideNav,
        };
    },
});
</script>
