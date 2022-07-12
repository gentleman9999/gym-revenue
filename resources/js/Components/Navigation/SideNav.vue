<template>
    <div
        class="fixed inset-0 lg:position-unset flex flex-col bg-gradient-to-b from-gray-700 to-base-300 flex-shrink-0 transform transition transition-transform z-10"
        style="transition-property: width, transform"
        :class="{
            'w-full lg:w-72 translate-x-0': expanded,
            ' translate-x-full lg:translate-x-0 lg:w-20': !expanded,
        }"
    >
        <!-- Sidebar Links -->
        <nav class="flex-grow lg:block pb-4 lg:pb-0 overflow-hidden">
            <div
                class="flex-shrink-0 px-4 py-4 flex flex-row items-center justify-between"
                v-if="$page.props.user.current_client_id"
            >
                <button @click="toggle()" class="btn btn-ghost">
                    <burger-icon class="m-auto h-full mr-6" />
                    Collapse Menu
                </button>
            </div>
            <div
                v-for="navItem in navigation"
                :key="navItem.key"
                :class="{
                    'bg-primary': route().current(navItem.route),
                    'bg-transparent': route().current(navItem.route),
                }"
                class="nav-link-container"
            >
                <jet-nav-link class="jet-nav-link" :href="route(navItem.route)">
                    <p class="flex items-center">
                        <span
                            class="text-base-content inline-block w-8 h-8 mr-4"
                        >
                            <component
                                :is="navItem.icon"
                                class="m-auto h-full"
                            />
                        </span>
                        {{ navItem.label }}
                    </p>
                </jet-nav-link>
            </div>
            <jet-bar-responsive-links />
        </nav>
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
import { comingSoon } from "@/utils/comingSoon";
import JetBarResponsiveLinks from "@/Components/JetBarResponsiveLinks.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import { useLockScroll } from "vue-composable";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import { useBreakpointTailwindCSS } from "vue-composable";
import {
    BarsIcon,
    MassComIcon,
    UserIcon,
    TeamIcon,
    ReportIcon,
    CalendarIcon,
    ToDoIcon,
    FileIcon,
    NotesIcon,
    LocationIcon,
    SettingIcon,
    LeadsIcon,
    MembersIcon,
    BurgerIcon,
} from "@/Components/Icons";
import { usePage } from "@inertiajs/inertia-vue3";

export default defineComponent({
    name: "SideNav",
    emits: ["toggle"],
    components: {
        JetNavLink,
        JetBarResponsiveLinks,
        BarsIcon,
        MassComIcon,
        UserIcon,
        TeamIcon,
        ReportIcon,
        CalendarIcon,
        NotesIcon,
        ToDoIcon,
        FileIcon,
        LocationIcon,
        SettingIcon,
        BurgerIcon,
        FontAwesomeIcon,
    },
    props: ["page"],
    computed: {
        navigation() {
            const page = usePage();
            const user = page.props.value.user;
            const client_services = page.props.value.client_services;
            let default_permission = user.current_client_id;
            let nav = [
                {
                    key: "nav-mass",
                    icon: MassComIcon,
                    route: "comms.dashboard",
                    label: "Mass Communicator",
                    permission:
                        default_permission &&
                        client_services.includes("MASS_COMMS"),
                },
                {
                    key: "nav-employee",
                    icon: UserIcon,
                    route: "users",
                    label: "Employee Management",
                    permission:
                        user.abilities.includes("users.read") ||
                        user.abilities.includes("*"),
                },
                {
                    key: "nav-team",
                    icon: TeamIcon,
                    route: "teams",
                    label: "Team Management",
                    permission:
                        user.abilities.includes("users.read") ||
                        user.abilities.includes("*") ||
                        default_permission,
                },
                {
                    key: "nav-repoting",
                    icon: ReportIcon,
                    route: "reports.dashboard",
                    label: "Reporting",
                    permission: default_permission,
                },
                {
                    key: "nav-calendar",
                    icon: CalendarIcon,
                    route: "calendar",
                    label: "Calendar",
                    permission:
                        default_permission &&
                        client_services.includes("CALENDAR"),
                },
                {
                    key: "nav-notes",
                    icon: NotesIcon,
                    route: "notes",
                    label: "Notes",
                    permission: default_permission,
                },
                {
                    key: "nav-todo",
                    icon: ToDoIcon,
                    route: "tasks",
                    label: "To Do's",
                    permission: default_permission,
                },
                {
                    key: "nav-leads",
                    icon: LeadsIcon,
                    route: "data.leads",
                    label: "Leads",
                    permission:
                        default_permission && client_services.includes("LEADS"),
                },
                {
                    key: "nav-members",
                    icon: MembersIcon,
                    route: "data.members",
                    label: "Members",
                    permission:
                        default_permission &&
                        client_services.includes("MEMBERS"),
                },
                {
                    key: "nav-documents",
                    icon: FileIcon,
                    route: "files",
                    label: "Documents",
                    permission: default_permission,
                },
                {
                    key: "nav-locations",
                    icon: LocationIcon,
                    route: "locations",
                    label: "Locations",
                    permission: default_permission,
                },
                {
                    key: "nav-settings",
                    icon: SettingIcon,
                    route: "data.conversions",
                    label: "Settings",
                    permission: default_permission,
                },
            ];

            return nav.filter((item) => item.permission);
        },
    },
    setup(props, { emit }) {
        const media = useBreakpointTailwindCSS();
        let expanded = ref(false);
        const { lock, unlock } = useLockScroll("body");
        onMounted(unlock); //this shouldnt be necessary. y

        watchEffect(() => {
            if (!media.lg.value) {
                if (expanded.value) {
                    lock();
                } else {
                    unlock();
                }
            } else {
                unlock();
            }
        });

        const toggle = () => {
            expanded.value = !expanded.value;
            emit("toggle");
        };
        return { comingSoon, toggle, expanded, media };
    },
});
</script>
