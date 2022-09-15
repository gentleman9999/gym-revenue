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
        <nav
            class="flex-grow lg:block pb-4 lg:pb-0 overflow-hidden overflow-y-auto"
        >
            <div
                class="flex-shrink-0 px-4 py-4 flex flex-row items-center justify-between"
                v-if="$page.props.user.current_team.isClientUser"
            >
                <button @click="toggle()" class="btn btn-ghost">
                    <burger-icon class="m-auto h-full mr-6" />
                    Collapse Menu
                </button>
            </div>
            <div
                v-for="navItem in navigation"
                :key="navItem.key"
                class="nav-link-container"
                :class="{
                    'bg-primary': route().current(navItem.route),
                    'bg-transparent': route().current(navItem.route),
                }"
                @click="goToLink(route(navItem.route))"
            >
                <jet-nav-link
                    class="jet-nav-link"
                    :href="route(navItem.route)"
                    @click="$event.preventDefault()"
                >
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
    @apply text-sm font-semibold rounded-lg btn  btn-ghost rounded-none pl-8 hover:bg-primary hover:text-white focus:border-none;
}
.nav-link-container {
    @apply block py-2 mt-2 whitespace-nowrap hover:bg-primary hover:text-white cursor-pointer;
}
</style>

<script>
import { defineComponent, ref, watchEffect, onMounted } from "vue";
import { comingSoon } from "@/utils/comingSoon";
import JetBarResponsiveLinks from "@/Components/JetBarResponsiveLinks.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import { useLockScroll } from "vue-composable";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Inertia } from "@inertiajs/inertia";

import { useBreakpointTailwindCSS } from "vue-composable";
import {
    BarsIcon,
    SearchIcon,
    MassComIcon,
    UserIcon,
    TeamIcon,
    ReportIcon,
    CalendarIcon,
    ToDoIcon,
    FileIcon,
    NotesIcon,
    ReminderIcon,
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
        SearchIcon,
        MassComIcon,
        UserIcon,
        TeamIcon,
        ReportIcon,
        CalendarIcon,
        NotesIcon,
        ToDoIcon,
        FileIcon,
        ReminderIcon,
        LocationIcon,
        SettingIcon,
        BurgerIcon,
        FontAwesomeIcon,
    },
    computed: {
        navigation() {
            const page = usePage();
            const user = page.props.value.user;
            const client_services = page.props.value.client_services;
            const isClientUser =
                page.props.value.user?.current_team?.isClientTeam;
            let isAdmin = user?.abilities?.includes("*");

            let nav = [
                {
                    key: "nav-mass",
                    icon: MassComIcon,
                    route: "mass-comms.dashboard",
                    label: "Mass Communicator",
                    permission:
                        isClientUser &&
                        (client_services?.includes("MASS_COMMS") || isAdmin),
                },
                {
                    key: "nav-search",
                    icon: SearchIcon,
                    route: "searches",
                    label: "Global Search",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("searches.read") || isAdmin),
                },
                {
                    key: "nav-employee",
                    icon: UserIcon,
                    route: "users",
                    label: "Employee Management",
                    permission:
                        user.abilities?.includes("users.read") || isAdmin,
                },
                {
                    key: "nav-team",
                    icon: TeamIcon,
                    route: "teams",
                    label: "Team Management",
                    permission:
                        user.abilities?.includes("teams.read") || isAdmin,
                },
                {
                    key: "nav-repoting",
                    icon: ReportIcon,
                    route: "reports.dashboard",
                    label: "Reporting",
                    permission: isClientUser,
                },
                {
                    key: "nav-calendar",
                    icon: CalendarIcon,
                    route: "calendar",
                    label: "Calendar",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("calendar.read") ||
                            client_services?.includes("CALENDAR") ||
                            isAdmin),
                },
                {
                    key: "nav-notes",
                    icon: NotesIcon,
                    route: "notes",
                    label: "Notes",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("notes.read") ||
                            client_services?.includes("NOTES") ||
                            isAdmin),
                },
                {
                    key: "nav-todo",
                    icon: ToDoIcon,
                    route: "tasks",
                    label: "To Do's",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("tasks.read") || isAdmin),
                },
                {
                    key: "nav-leads",
                    icon: LeadsIcon,
                    route: "data.leads",
                    label: "Leads",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("leads.read") ||
                            client_services?.includes("LEADS") ||
                            isAdmin),
                },
                {
                    key: "nav-members",
                    icon: MembersIcon,
                    route: "data.members",
                    label: "Members",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("members.read") ||
                            client_services?.includes("MEMBERS") ||
                            isAdmin),
                },
                {
                    key: "nav-documents",
                    icon: FileIcon,
                    route: "files",
                    label: "Documents",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("files.read") || isAdmin),
                },
                {
                    key: "nav-reminders",
                    icon: ReminderIcon,
                    route: "reminders",
                    label: "Reminders",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("reminders.read") || isAdmin),
                },
                {
                    key: "nav-locations",
                    icon: LocationIcon,
                    route: "locations",
                    label: "Locations",
                    permission:
                        isClientUser &&
                        (user.abilities?.includes("locations.read") || isAdmin),
                },
                {
                    key: "nav-settings",
                    icon: SettingIcon,
                    route: "data.conversions",
                    label: "Settings",
                    permission: isClientUser && isAdmin,
                },
            ];

            return nav.filter((item) => item?.permission);
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

        const handleClickItem = () => {
            if (window.innerWidth < 1024) {
                toggle();
            }
        };
        const goToLink = (link) => {
            if (window.innerWidth < 1024) {
                toggle();
            }
            Inertia.visit(link);
        };
        return {
            comingSoon,
            toggle,
            handleClickItem,
            expanded,
            media,
            goToLink,
        };
    },
});
</script>
