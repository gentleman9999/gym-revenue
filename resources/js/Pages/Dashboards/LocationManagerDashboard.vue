<template>
    <LayoutHeader title="Dashboard">
        <dashboard-header :team-name="teamName" :account-name="accountName" />
    </LayoutHeader>

    <jet-bar-container>
        <!-- @todo - leave the jet-bar-alert here and make it contextual, dynamic, pusher-enabled? -->
        <!-- <jet-bar-alert text="This is an alert message" /> -->

        <dashboard-stats :widgets="widgets" />

        <div class="container max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="grid xl:grid-cols-2 gap-4">
                <div class="card bg-base-100 shadow-2xl">
                    <div class="card-body">
                        <h2 class="card-title">To Do List</h2>
                        <div class="divider mt-0"></div>
                        <div
                            id="teamSelectTable"
                            class="h-80 overflow-auto mt-6"
                        >
                            <table class="table-compact">
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-2xl">
                    <div class="card-body">
                        <h2 class="card-title">Club Employees</h2>
                        <div class="divider mt-0"></div>
                        <div
                            class="alert alert-info xl:grid-cols-4 flex flex-col"
                        >
                            <div class="flex flex-row"></div>
                            <div
                                id="announcementsTable"
                                class="h-80 overflow-auto"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </jet-bar-container>
</template>

<script>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import JetBarAlert from "@/Components/JetBarAlert.vue";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable.vue";
import JetBarBadge from "@/Components/JetBarBadge.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import { Inertia } from "@inertiajs/inertia";
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
    faUser,
} from "@fortawesome/pro-duotone-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DashboardStats from "@/Pages/Dashboards/Partials/DashboardStats.vue";
import DashboardHeader from "@/Pages/Dashboards/Partials/DashboardHeader.vue";

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

export default {
    components: {
        LayoutHeader,
        JetBarContainer,
        JetBarAlert,
        GymRevenueTable,
        JetBarBadge,
        JetBarIcon,
        FontAwesomeIcon,
        DashboardStats,
        DashboardHeader,
    },
    props: [
        "teamName",
        "teams",
        "clients",
        "accountName",
        "widgets",
        "announcements",
    ],
    watch: {
        announcementModal(flag) {
            if (flag) {
                this.$refs.anModal.open();
            } else {
                this.$refs.anModal.close();
            }
        },
    },
    data() {
        return {
            showAnnouncement: false,
            announcementModal: false,
        };
    },
    methods: {
        switchToTeam(teamId) {
            Inertia.put(
                route("current-team.update"),
                {
                    team_id: teamId,
                },
                {
                    preserveState: false,
                }
            );
        },
    },
    computed: {},
    mounted() {
        if (this.announcements.length > 0) {
            this.showAnnouncement = true;
        }
        console.log("GymRevenue Dashboard");
    },
};
</script>

<style scoped></style>
