<template>
    <div>
        <Head :title="title" />
        <jet-banner />
        <div class="font-sans antialiased min-h-screen">
            <nav class="bg-base-300 border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-8xl mx-4">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <button v-show="!showingSidebar" @click="showingSidebar = true" class="pr-2"><font-awesome-icon :icon="['fas', 'bars']" size="16"/></button>
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <jet-application-mark class="block h-8 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </jet-nav-link>

                                <!-- @todo - make these dynamic, as some users wont have access -->
                                <!-- <jet-nav-link :href="route('analytics')" :active="route().current('analytics')"> -->
                                <jet-nav-link href="#" :active="route().current('analytics')" @click="comingSoon()">
                                    Analytics
                                </jet-nav-link>
                                <!-- <jet-nav-link :href="route('payment-gateways')" :active="route().current('payment-gateways')">
                                    Payment Gateways
                                </jet-nav-link> -->
                                <jet-nav-link href="#" :active="route().current('payment-gateways')" @click="comingSoon()">
                                    Payment Gateways
                                </jet-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <!-- Locations Dropdown -->
                                <jet-dropdown align="right" width="60" v-if="$page.props.user.all_locations.length > 1">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-base-300 hover:bg-base-100 hover:text-gray-700 focus:outline-none focus:bg-base-100 active:bg-base-100 transition">
                                                {{ $page.props.user.all_locations.find(location => location.id ===$page.props.user.current_location_id )?.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <!-- Location Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Locations
                                                </div>

                                                <template v-for="location in $page.props.user.all_locations" :key="location.id">
                                                    <form @submit.prevent="switchToLocation(location)">
                                                        <jet-dropdown-link as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="location.id == $page.props.user.current_location_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <div>{{ location.name }}</div>
                                                            </div>
                                                        </jet-dropdown-link>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown -->
                                <jet-dropdown align="right" width="60" v-if="$page.props.jetstream.hasTeamFeatures">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-base-300 hover:bg-base-100 hover:text-gray-700 focus:outline-none focus:bg-base-100 active:bg-base-100 transition">
                                                {{ $page.props.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <jet-dropdown-link :href="route('teams.show', $page.props.user.current_team)">
                                                    Team Settings
                                                </jet-dropdown-link>

                                                <jet-dropdown-link :href="route('teams.create')" v-if="$page.props.jetstream.canCreateTeams">
                                                    Create New Team
                                                </jet-dropdown-link>

                                                <div class="border-t border-gray-100"></div>

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <jet-dropdown-link as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </jet-dropdown-link>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Notifications -->
                            <noty-bell></noty-bell>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-base-300 hover:text-gray-700 focus:outline-none transition">
                                                {{ $page.props.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <jet-dropdown-link :href="route('profile.show')">
                                            Profile
                                        </jet-dropdown-link>
                                        <!-- @todo - make these dynamic, as some users wont have access -->
                                        <jet-dropdown-link :href="route('profile.show')">
                                            User Management
                                        </jet-dropdown-link>
                                        <jet-dropdown-link :href="route('profile.show')">
                                            Invoices
                                        </jet-dropdown-link>

                                        <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                            API Tokens
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>
                                        <!-- Extra Features -->
                                        <!-- @todo - make these dynamic, as some users wont have access -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Extras
                                        </div>
                                        <jet-dropdown-link :href="route('workout-generator')">
                                            Workout Generator
                                        </jet-dropdown-link>
                                        <jet-dropdown-link :href="route('workout-generator')">
                                            Sales Slideshow
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button">
                                                Log Out
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <jet-responsive-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </jet-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-base-100">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 mr-3" >
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">{{ $page.props.user.name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ $page.props.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </jet-responsive-nav-link>

                            <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                API Tokens
                            </jet-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <jet-responsive-nav-link as="button">
                                    Log Out
                                </jet-responsive-nav-link>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-base-100"></div>

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <jet-responsive-nav-link :href="route('teams.show', $page.props.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </jet-responsive-nav-link>

                                <jet-responsive-nav-link :href="route('teams.create')" :active="route().current('teams.create')" v-if="$page.props.jetstream.canCreateTeams">
                                    Create New Team
                                </jet-responsive-nav-link>

                                <div class="border-t border-base-100"></div>

                                <!-- Extra Features -->
                                <!-- @todo - make these dynamic, as some users wont have access -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Extras
                                </div>
                                <jet-responsive-nav-link :href="route('workout-generator')">
                                    Workout Generator
                                </jet-responsive-nav-link>
                                <jet-responsive-nav-link :href="route('workout-generator')">
                                    Sales Slideshow
                                </jet-responsive-nav-link>

                                <div class="border-t border-gray-100"></div>

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <jet-responsive-nav-link as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </jet-responsive-nav-link>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="font-sans antialiased">
                <jet-banner />

                <div class="lg:flex flex-col lg:flex-row lg:min-h-screen w-full">

                    <!-- Sidebar -->
                    <jet-bar-sidebar v-show="showingSidebar" @toggle="showingSidebar = !showingSidebar"/>
                    <!-- End Sidebar -->

                    <div class="w-full">
                        <!-- Content Container -->
                        <main class="flex-1 relative z-0 overflow-y-auto py-6 focus:outline-none" tabindex="0">
                            <div v-if="this.$slots.header" class="max-w-7xl mx-auto pt-3 px-4 sm:px-6 lg:px-8">
                                <!-- Title -->
                                <h1 class="text-lg font-semibold tracking-widest text-gray-900 uppercase dark-mode:text-white">
                                    <slot name="header"></slot>
                                </h1>
                                <!-- End Title -->
                            </div>
                            <!-- Replace with your content -->
                            <div>
                                <!-- Content -->
                                <div class="min-h-full lg:min-h-96 px-4 sm:px-0">
                                    <slot></slot>
                                </div>
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

<script>
    import { defineComponent } from 'vue'

    import { library } from '@fortawesome/fontawesome-svg-core';
    import { faBars } from '@fortawesome/pro-solid-svg-icons'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    library.add(faBars)

    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetBanner from '@/Jetstream/Banner'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
    import JetBarNavigationMenu from "@/Components/JetBarNavigationMenu";
    import JetBarSidebar from "@/Components/JetBarSidebar";
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import NotyBell from "../Components/NotyBell";

    export default defineComponent({
        components: {
            Head, Link,
            JetBarSidebar,
            JetBarNavigationMenu,
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            FontAwesomeIcon,
            NotyBell
        },
        props: {
            title: String,
        },
        data() {
            return {
                showingSidebar: true,
                showingNavigationDropdown: false,
                showingNotificationDropdown: false,
            }
        },
        methods: {
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },
            switchToLocation(location) {
                this.$inertia.put(route('current-location.update'), {
                    'location_id': location.id
                }, {
                    preserveState: false,
                    headers:{
                        Accept: 'application/json',
                        Test:'123'
                    }
                })
            },

            comingSoon() {
                new Noty({
                    type: 'warning',
                    theme: 'sunset',
                    text: 'Feature Coming Soon!',
                    timeout: 7500
                }).show();
            },
            logout() {
                this.$inertia.post(route('logout'));
            },
        }
    })
</script>
