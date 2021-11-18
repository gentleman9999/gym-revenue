<template>
    <div class="flex flex-col w-full lg:w-64 bg-base-300 dark-mode:text-gray-200 dark-mode:bg-gray-800 flex-shrink-0 lg:border-r">
        <div class="flex-shrink-0 px-4 lg:px-8 py-4 flex flex-row items-center justify-between">
            <button class="rounded-lg lg:hidden rounded-lg focus:outline-none focus:shadow-outline" @click="showingNavigationDropdown = !showingNavigationDropdown">
                <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                    <path v-show="!showingNavigationDropdown" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    <path v-show="showingNavigationDropdown" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <button @click="toggle()"><font-awesome-icon :icon="['fas', 'bars']" size="16"/></button>
        </div>
        <div v-show="showingNavigationDropdown" @click="showingNavigationDropdown = false" class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>

        <!-- Sidebar Links -->
        <nav :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="flex-grow lg:block px-4 pb-4 lg:pb-0 lg:overflow-y-auto">
            <!-- Searchbar
            <jet-bar-sidebar-search />
             End Searchbar -->

            <!-- @todo - Not Ready Yet -->
            <!-- Message Center (working title) -->
            <div :class="route().current('data.conversions') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#" @click="comingSoon()">
                    <p><font-awesome-icon :icon="['fad', 'comments-alt']" size="16"/> Notifications </p>
                </jet-nav-link>
            </div>

            <!-- Analytics (Working Title) -->
            <div :class="route().current('data.conversions') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#" @click="comingSoon()">
                    <p><font-awesome-icon :icon="['fad', 'paste']" size="16"/> ToDo List </p>
                </jet-nav-link>
            </div>
            <!-- End Not Ready Yet -->

            <!-- Clubs -->
            <div :class="route().current('locations') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">
                <!-- <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" :href="route('sales-slideshow')">Sales Slideshow</jet-nav-link> -->
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" :href="route('locations')">
                    <p><font-awesome-icon :icon="['fad', 'dumbbell']" size="16"/> Clubs</p>
                </jet-nav-link>
            </div>

            <!-- Leads -->
            <div :class="route().current('data.leads') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" :href="route('data.leads')">
                    <p><font-awesome-icon :icon="['fad', 'chart-line']" size="16"/> Leads</p>
                </jet-nav-link>
            </div>

            <!--
            <div :class="route().current('data.conversions') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">

                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#" @click="comingSoon()">Conversions</jet-nav-link>
            </div>
            -->

            <!-- Workout Generator Preview -->
            <div :class="route().current('workout-generator') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2">
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" :href="route('workout-generator')">
                    <p><font-awesome-icon :icon="['fad', 'calendar-alt']" size="16"/> Workout Generator</p>
                </jet-nav-link>
            </div>

            <!-- Mass Communicator -->
            <div :class="route().current('comms.dashboard') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2">
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" :href="route('comms.dashboard')">
                    <p><font-awesome-icon :icon="['fad', 'satellite-dish']" size="16"/> Mass Communicator</p>
                </jet-nav-link>
            </div>


            <!-- <div :class="route().current('sales-slideshow') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2">
                 <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" :href="route('sales-slideshow')">Sales Slideshow</jet-nav-link>
            </div> -->

            <div :class="route().current('data.conversions') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">
                <jet-nav-link class="text-sm font-semibold rounded-lg dark-mode:bg-gray-700 dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline" href="#" @click="comingSoon()">
                    <p><font-awesome-icon :icon="['fas', 'cog']" size="16"/> Settings </p>
                </jet-nav-link>
            </div>

            <jet-bar-responsive-links />

        </nav>
        <div v-show="showingSidebarNavigationDropdown" @click="showingSidebarNavigationDropdown = false" class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>
        <!-- End Sidebar Links -->
    </div>
</template>

<script>
import JetBarResponsiveLinks from "@/Components/JetBarResponsiveLinks";
import JetBarSidebarSearch from "./JetBarSidebarSearch";
import JetNavLink from '@/Jetstream/NavLink'

import { library } from '@fortawesome/fontawesome-svg-core';
import { faBars, faCog } from '@fortawesome/pro-solid-svg-icons'
import { faDumbbell, faChartLine, faCalendarAlt, faPaste, faSatelliteDish, faCommentsAlt } from '@fortawesome/pro-duotone-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(faBars, faDumbbell, faChartLine, faCalendarAlt, faPaste, faSatelliteDish, faCommentsAlt, faCog)


export default {
    name: "JetBarSidebar",
    components: {
        JetNavLink,
        JetBarResponsiveLinks,
        JetBarSidebarSearch,
        FontAwesomeIcon
    },
    data() {
        return {
            showingNavigationDropdown: false,
            showingSidebarNavigationDropdown: false,
        }
    },
    methods: {
        toggle() {
            this.$emit('toggle');
        },
        comingSoon() {
            new Noty({
                type: 'warning',
                theme: 'sunset',
                text: 'Feature Coming Soon!',
                timeout: 7500
            }).show();
        },
    }
}
</script>
