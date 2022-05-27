<template>
    <div
        class="fixed inset-0 lg:position-unset flex flex-col bg-gradient-to-b from-gray-700 to-base-300 flex-shrink-0 transform transition transition-transform z-10"
        style="transition-property: width, transform"
        :class="{
            'w-full lg:w-72 translate-x-0': expanded,
            ' translate-x-full lg:translate-x-0 lg:w-20': !expanded,
        }"
    >
        <div
            class="flex-shrink-0 px-4 py-4 flex flex-row items-center justify-between"
        >
            <!--            <button class="rounded-lg lg:hidden rounded-lg focus:outline-none focus:shadow-outline"-->
            <!--                    @click="expanded = !expanded">-->
            <!--                <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">-->
            <!--                    <path fill-rule="evenodd"-->
            <!--                          d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"-->
            <!--                          clip-rule="evenodd"></path>-->
            <!--                    <path fill-rule="evenodd"-->
            <!--                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"-->
            <!--                          clip-rule="evenodd"></path>-->
            <!--                </svg>-->
            <!--            </button>-->
            <button @click="toggle()" class="btn btn-ghost">
                <font-awesome-icon :icon="['fas', 'bars']" size="lg" />
            </button>
        </div>
        <!--        <div v-show="expanded" @click="expanded = false"-->
        <!--             class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>-->

        <!-- Sidebar Links -->
        <nav class="flex-grow lg:block pb-4 lg:pb-0 overflow-y-auto">
            <!-- Searchbar
            <jet-bar-sidebar-search />
             End Searchbar -->

            <!-- @todo - Not Ready Yet -->
            <!-- Message Center (working title) -->
            <div
                :class="
                    route().current('data.conversions')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    href="#"
                    @click="comingSoon()"
                >
                    <p>
                        <font-awesome-icon
                            :icon="['fad', 'comments-alt']"
                            size="lg"
                        />
                        Notifications
                    </p>
                </jet-nav-link>
            </div>

            <!-- Analytics (Working Title) -->
            <div
                :class="
                    route().current('tasks') ? 'bg-primary' : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link class="jet-nav-link" :href="route('tasks')">
                    <p>
                        <font-awesome-icon :icon="['fad', 'paste']" size="lg" />
                        Tasks
                    </p>
                </jet-nav-link>
            </div>
            <!-- End Not Ready Yet -->

            <!-- Reporting -->
            <div
                :class="
                    route().current('reports.dashboard')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    :href="route('reports.dashboard')"
                >
                    <p>
                        <font-awesome-icon
                            :icon="['fad', 'chart-line']"
                            size="lg"
                        />
                        Reporting
                    </p>
                </jet-nav-link>
            </div>

            <!-- Calendar -->
            <div
                :class="
                    route().current('calendar')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link class="jet-nav-link" :href="route('calendar')">
                    <p>
                        <font-awesome-icon
                            :icon="['fad', 'calendar-alt']"
                            size="lg"
                        />
                        Calendar
                    </p>
                </jet-nav-link>
            </div>

            <!-- Clubs -->
            <div
                :class="
                    route().current('locations')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <!-- <jet-nav-link class="jet-nav-link" :href="route('sales-slideshow')">Sales Slideshow</jet-nav-link> -->
                <jet-nav-link class="jet-nav-link" :href="route('locations')">
                    <p>
                        <font-awesome-icon
                            :icon="['fad', 'dumbbell']"
                            size="lg"
                        />
                        Clubs
                    </p>
                </jet-nav-link>
            </div>

            <!-- Leads -->
            <div
                :class="
                    route().current('data.leads')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link class="jet-nav-link" :href="route('data.leads')">
                    <p>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="28.246"
                            height="27.014"
                            viewBox="0 0 28.246 27.014"
                            class="svg-inline--fa fa-dumbbell fa-w-20 fa-lg"
                        >
                            <g id="leads_icon" transform="translate(0)">
                                <path
                                    id="Path_3340"
                                    data-name="Path 3340"
                                    d="M134.885,119.3a3.656,3.656,0,0,1,.905-3.063,4.429,4.429,0,0,1,1.829-1.18,8.024,8.024,0,0,1,5.5,0,4.037,4.037,0,0,1,2.447,2.078,3.77,3.77,0,0,1,.3,2.167Z"
                                    transform="translate(-126.244 -107.259)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3341"
                                    data-name="Path 3341"
                                    d="M174.242,6.41a3.205,3.205,0,1,1,3.234-3.164,3.191,3.191,0,0,1-3.234,3.164"
                                    transform="translate(-160.133 0.001)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3342"
                                    data-name="Path 3342"
                                    d="M168,374.212H160.3a2.506,2.506,0,0,1,.69-2.231,3.4,3.4,0,0,1,1.651-.906,5.4,5.4,0,0,1,4.091.429A2.472,2.472,0,0,1,168,374.212"
                                    transform="translate(-150.035 -347.199)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3343"
                                    data-name="Path 3343"
                                    d="M328.581,275.189h-7.657a2.4,2.4,0,0,1,1.048-2.569,4.6,4.6,0,0,1,2.459-.749,5.281,5.281,0,0,1,2.749.546,2.542,2.542,0,0,1,1.4,2.771"
                                    transform="translate(-300.385 -254.515)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3344"
                                    data-name="Path 3344"
                                    d="M.017,275.135a2.591,2.591,0,0,1,1.1-2.538,4.682,4.682,0,0,1,2.8-.781,5.081,5.081,0,0,1,2.457.565,2.471,2.471,0,0,1,1.358,2.753Z"
                                    transform="translate(0 -254.472)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3345"
                                    data-name="Path 3345"
                                    d="M30.2,193.179a2.237,2.237,0,0,1-2.223,2.263,2.279,2.279,0,0,1-2.293-2.271,2.251,2.251,0,0,1,2.242-2.253,2.208,2.208,0,0,1,2.275,2.261"
                                    transform="translate(-24.047 -178.736)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3346"
                                    data-name="Path 3346"
                                    d="M346.394,193.155a2.219,2.219,0,0,1,2.28-2.259,2.262,2.262,0,1,1-.059,4.523,2.235,2.235,0,0,1-2.222-2.265"
                                    transform="translate(-324.289 -178.714)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3347"
                                    data-name="Path 3347"
                                    d="M186.1,292a2.255,2.255,0,1,1,2.27,2.282A2.264,2.264,0,0,1,186.1,292"
                                    transform="translate(-174.227 -271.251)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3348"
                                    data-name="Path 3348"
                                    d="M285.948,75.552l-2.935-1.116,1.251-.706a10.579,10.579,0,0,0-4.8-3.764l.452-1.073a10.338,10.338,0,0,1,3.037,1.709,11.91,11.91,0,0,1,2.4,2.556l1.321-.679-.719,3.074"
                                    transform="translate(-261.627 -64.495)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3349"
                                    data-name="Path 3349"
                                    d="M57.545,68.838l.469,1.059a10.68,10.68,0,0,0-4.787,3.784l1.245.7L51.547,75.5l-.727-3.07,1.308.667a11.732,11.732,0,0,1,5.417-4.26"
                                    transform="translate(-47.576 -64.445)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3350"
                                    data-name="Path 3350"
                                    d="M77.483,343.972a10.451,10.451,0,0,1-4.62-3.942c.426,0,.822-.009,1.217.006a.453.453,0,0,1,.3.131,11,11,0,0,0,3.561,2.755l-.458,1.05"
                                    transform="translate(-68.213 -318.329)"
                                    fill="currentColor"
                                />
                                <path
                                    id="Path_3351"
                                    data-name="Path 3351"
                                    d="M290.39,342.665a11.2,11.2,0,0,0,2.707-1.855,3.3,3.3,0,0,0,.524-.536,1.115,1.115,0,0,1,1.18-.518,6.993,6.993,0,0,0,.751,0,11.5,11.5,0,0,1-4.672,3.983c-.1-.217-.171-.39-.249-.561s-.145-.311-.242-.518"
                                    transform="translate(-271.86 -318.064)"
                                    fill="currentColor"
                                />
                            </g>
                        </svg>
                        Leads
                    </p>
                </jet-nav-link>
            </div>

            <!-- MEMBERS -->
            <div
                :class="
                    route().current('data.members')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    :href="route('data.members')"
                >
                    <p>
                        <font-awesome-icon
                            :icon="['fas', 'ticket']"
                            size="lg"
                        />
                        Members
                    </p>
                </jet-nav-link>
            </div>

            <!--
            <div :class="route().current('data.conversions') ? 'bg-primary' : 'bg-transparent'" class="nav-link-container" v-if="$page.props.user.current_client_id !== null">

                <jet-nav-link class="jet-nav-link" href="#" @click="comingSoon()">Conversions</jet-nav-link>
            </div>
            -->
            <!-- File Manager -->
            <div
                :class="
                    route().current('files') ? 'bg-primary' : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link class="jet-nav-link" :href="route('files')">
                    <p>
                        <font-awesome-icon
                            :icon="['fas', 'file-upload']"
                            size="lg"
                        />
                        File Manager
                    </p>
                </jet-nav-link>
            </div>

            <!-- Mass Communicator -->
            <div
                :class="
                    route().current('comms.dashboard')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    :href="route('comms.dashboard')"
                >
                    <p>
                        <font-awesome-icon
                            :icon="['fad', 'satellite-dish']"
                            size="lg"
                        />
                        Mass Communicator
                    </p>
                </jet-nav-link>
            </div>

            <!-- <div :class="route().current('sales-slideshow') ? 'bg-primary' : 'bg-transparent'" class="nav-link-container">
                 <jet-nav-link class="jet-nav-link" :href="route('sales-slideshow')">Sales Slideshow</jet-nav-link>
            </div> -->

            <div
                :class="
                    route().current('users') ? 'bg-primary' : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="
                    $page.props.user.abilities.includes('users.read') ||
                    $page.props.user.abilities.includes('*')
                "
            >
                <jet-nav-link class="jet-nav-link" :href="route('users')">
                    <p>
                        <font-awesome-icon :icon="['fad', 'user']" size="lg" />
                        <span>User Management</span>
                    </p>
                </jet-nav-link>
            </div>

            <div
                :class="
                    route().current('data.conversions')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
            >
                <jet-nav-link class="jet-nav-link" :href="route('teams')">
                    <p>
                        <font-awesome-icon :icon="['fad', 'users']" size="lg" />
                        <span>Team Management</span>
                    </p>
                </jet-nav-link>
            </div>

            <div
                :class="
                    route().current('data.conversions')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.current_client_id !== null"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    href="#"
                    @click="comingSoon()"
                >
                    <p>
                        <font-awesome-icon :icon="['fas', 'cog']" size="lg" />
                        <span>Settings</span>
                    </p>
                </jet-nav-link>
            </div>

            <jet-bar-responsive-links />
        </nav>
        <!--        <div v-show="showingSidebarNavigationDropdown" @click="showingSidebarNavigationDropdown = false"-->
        <!--             class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>-->
        <!-- End Sidebar Links -->
    </div>
</template>

<style scoped>
.jet-nav-link {
    @apply text-sm font-semibold rounded-lg btn  btn-ghost hover:bg-primary hover:text-white rounded-none pl-8;
    & p > svg {
        @apply mr-8;
    }
}
.nav-link-container {
    @apply block py-2 mt-2 whitespace-nowrap;
}
</style>

<script>
import { defineComponent, ref, watchEffect, onMounted } from "vue";
import JetBarResponsiveLinks from "@/Components/JetBarResponsiveLinks";
import JetNavLink from "@/Jetstream/NavLink";
import { useLockScroll } from "vue-composable";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faBars,
    faCog,
    faFileUpload,
    faTicket,
} from "@fortawesome/pro-solid-svg-icons";
import {
    faCalendarAlt,
    faChartLine,
    faCommentsAlt,
    faDumbbell,
    faPaste,
    faSatelliteDish,
    faUsers,
    faUser,
} from "@fortawesome/pro-duotone-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { useBreakpointTailwindCSS } from "vue-composable";

library.add(
    faBars,
    faDumbbell,
    faChartLine,
    faCalendarAlt,
    faPaste,
    faSatelliteDish,
    faCommentsAlt,
    faCog,
    faFileUpload,
    faUsers,
    faUser,
    faTicket
);

export default defineComponent({
    name: "SideNav",
    emits: ["toggle"],
    components: {
        JetNavLink,
        JetBarResponsiveLinks,
        // JetBarSidebarSearch,
        FontAwesomeIcon,
    },

    setup(props, { emit }) {
        const media = useBreakpointTailwindCSS();
        let expanded = ref(false);
        const { lock, unlock } = useLockScroll("body");

        onMounted(unlock); //this shouldnt be necessary. y

        watchEffect(() => {
            if (!media.lg.value) {
                // console.log('not desktop!');
                if (expanded.value) {
                    lock();
                } else {
                    unlock();
                }
            } else {
                // console.log('desktop!');
                unlock();
            }
        });

        const comingSoon = () => {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        };
        const toggle = () => {
            expanded.value = !expanded.value;
            console.log({ expanded: expanded.value });
            emit("toggle");
        };

        return { comingSoon, toggle, expanded, media };
    },
});
</script>
