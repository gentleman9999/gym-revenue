<template>
    <div
        class="flex flex-col w-full lg:w-64 text-gray-700 bg-white dark:text-gray-200 dark:bg-gray-800 flex-shrink-0 lg:border-r">
        <div class="flex-shrink-0 px-4 lg:px-8 py-4 flex flex-row items-center justify-between">
            <button class="rounded-lg lg:hidden rounded-lg focus:outline-none focus:ring"
                    @click="showingNavigationDropdown = !showingNavigationDropdown">
                <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                    <path v-show="!showingNavigationDropdown" fill-rule="evenodd"
                          d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"
                          clip-rule="evenodd"></path>
                    <path v-show="showingNavigationDropdown" fill-rule="evenodd"
                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                          clip-rule="evenodd"></path>
                </svg>
            </button>
            <button @click="toggle()">x</button>
        </div>
        <div v-show="showingNavigationDropdown" @click="showingNavigationDropdown = false"
             class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>

        <!-- Sidebar Links -->
        <nav :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}"
             class="flex-grow lg:block px-4 pb-4 lg:pb-0 lg:overflow-y-auto">
            <!-- Searchbar -->
            <jet-bar-sidebar-search/>
            <!-- End Searchbar -->

            <div :class="{active: route().current('locations')}" class="block px-4 py-2 mt-2"
                 v-if="$page.props.user.current_client_id !== null">
                <!-- <jet-nav-link class="nav-link" :href="route('sales-slideshow')">Sales Slideshow</jet-nav-link> -->
                <jet-nav-link
                    class="nav-link"
                    :href="route('locations')">Locations
                </jet-nav-link>
            </div>

            <div :class="{active: route().current('data.leads')}" class="block px-4 py-2 mt-2"
                v-if="$page.props.user.current_client_id !== null">
                <jet-nav-link
                    class="nav-link"
                    :href="route('data.leads')">Leads
                </jet-nav-link>
            </div>

            <div :class="{active: route().current('data.conversions')}"
                 class="block px-4 py-2 mt-2" v-if="$page.props.user.current_client_id !== null">
                <!-- <jet-nav-link class="nav-link" :href="route('data.conversions')">Conversions</jet-nav-link> -->
                <jet-nav-link
                    class="nav-link"
                    href="#" @click="comingSoon()">Conversions
                </jet-nav-link>
            </div>

            <div
                :class="{active: route().current('workout-generator')}"
                class="block px-4 py-2 mt-2">
                <jet-nav-link class="nav-link" :href="route('workout-generator')">Workout Generator</jet-nav-link>
            </div>


            <!-- <div :class="route().current('sales-slideshow') ? 'bg-gray-200' : 'bg-transparent'" class="block px-4 py-2 mt-2">
                 <jet-nav-link class="nav-link" :href="route('sales-slideshow')">Sales Slideshow</jet-nav-link>
            </div> -->

            <jet-bar-responsive-links/>

        </nav>
        <div v-show="showingSidebarNavigationDropdown" @click="showingSidebarNavigationDropdown = false"
             class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>
        <!-- End Sidebar Links -->
    </div>
</template>
<style scoped>
.nav-link {
    @apply text-sm font-semibold text-gray-900 rounded-lg dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:bg-gray-600 dark:focus:text-white dark:hover:text-white dark:text-gray-200 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:ring;
}

.active {
    @apply bg-gray-200;
}
</style>

<script>
import JetBarResponsiveLinks from "@/Components/JetBarResponsiveLinks";
import JetBarSidebarSearch from "./JetBarSidebarSearch";
import JetNavLink from '@/Jetstream/NavLink'


export default {
    name: "JetBarSidebar",
    components: {
        JetNavLink,
        JetBarResponsiveLinks,
        JetBarSidebarSearch
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
