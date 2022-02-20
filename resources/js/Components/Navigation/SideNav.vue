<template>
    <div
        class="fixed inset-0 z-10 lg:position-unset flex flex-col bg-gradient-to-b from-gray-700 to-base-300 flex-shrink-0 transform transition transition-transform"
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
                        <font-awesome-icon :icon="['fad', 'paste']" size="lg" />
                        ToDo List
                    </p>
                </jet-nav-link>
            </div>
            <!-- End Not Ready Yet -->

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
                        <font-awesome-icon
                            :icon="['fad', 'chart-line']"
                            size="lg"
                        />
                        Leads
                    </p>
                </jet-nav-link>
            </div>

            <!--
            <div :class="route().current('data.conversions') ? 'bg-primary' : 'bg-transparent'" class="nav-link-container" v-if="$page.props.user.current_client_id !== null">

                <jet-nav-link class="jet-nav-link" href="#" @click="comingSoon()">Conversions</jet-nav-link>
            </div>
            -->

            <!-- Mass Communicator -->
            <div
                :class="
                    route().current('comms.dashboard')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
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
                    route().current('users')
                        ? 'bg-primary'
                        : 'bg-transparent'
                "
                class="nav-link-container"
                v-if="$page.props.user.abilities.includes('users.read') || $page.props.user.abilities.includes('*')"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    :href="route('users')"
                >
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
                <jet-nav-link
                    class="jet-nav-link"
                    :href="route('teams')"
                >
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
import { faBars, faCog, faFileUpload } from "@fortawesome/pro-solid-svg-icons";
import {
    faCalendarAlt,
    faChartLine,
    faCommentsAlt,
    faDumbbell,
    faPaste,
    faSatelliteDish,
    faUsers,
    faUser
} from "@fortawesome/pro-duotone-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    useBreakpointTailwindCSS
} from "vue-composable";

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
    faUser
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

        watchEffect(()=>{
            if(!media.lg.value){
                // console.log('not desktop!');
                if(expanded.value){
                    lock();
                }else{
                    unlock();
                }
            }else{
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
