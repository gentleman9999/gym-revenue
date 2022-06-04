<template>
    <app-layout>
        <template #header>
            <dashboard-header
                :team-name="teamName"
                :account-name="accountName"
            />
        </template>

        <jet-bar-container>
            <!-- @todo - leave the jet-bar-alert here and make it contextual, dynamic, pusher-enabled? -->
            <!-- <jet-bar-alert text="This is an alert message" /> -->

            <dashboard-stats :widgets="widgets" />

            <div class="container max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
                <div class="grid xl:grid-cols-2 gap-4">
                    <div class="card bg-base-100 shadow-2xl">
                        <div class="card-body">
                            <h2 class="card-title">Available Teams</h2>
                            <div class="divider mt-0"></div>
                            <p
                                class="text-center"
                                v-if="
                                    !(
                                        'is_being_impersonated' in
                                        $page.props.user
                                    )
                                "
                            >
                                Select a Team that you'd like to Access or use
                                the Switch Team toggle at the top.
                            </p>
                            <p class="text-center" v-else>
                                Team Switching is not available in impersonation
                                mode.
                            </p>
                            <div
                                id="teamSelectTable"
                                class="h-80 overflow-auto mt-6"
                            >
                                <table
                                    class="table-compact h-80"
                                    v-if="
                                        !(
                                            'is_being_impersonated' in
                                            $page.props.user
                                        )
                                    "
                                >
                                    <tbody>
                                        <tr
                                            v-for="(team, id) in teams"
                                            class="hover"
                                        >
                                            <td class="">
                                                {{ team["team_name"] }}
                                            </td>
                                            <td class="">
                                                ({{ team["client_name"] }})
                                            </td>
                                            <td class="">
                                                <button
                                                    type="button"
                                                    class="btn btn-success"
                                                    @click="switchToTeam(id)"
                                                >
                                                    Go
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-2xl">
                        <div class="card-body">
                            <h2 class="card-title">Recent Announcements!</h2>
                            <div class="divider mt-0"></div>
                            <div
                                class="alert alert-info xl:grid-cols-4 flex flex-col"
                            >
                                <div class="flex flex-row">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        class="w-6 h-6 mx-2 stroke-current"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                    <label
                                        >New Version deployed! - v{{
                                            announcements[0].version
                                        }}</label
                                    >
                                </div>
                                <div
                                    id="announcementsTable"
                                    class="h-80 overflow-auto"
                                >
                                    <table class="table-compact">
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    note, x
                                                ) in announcements[0].notes"
                                                v-show="x < 7"
                                                class="hover"
                                            >
                                                <td class="">* {{ note }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex flex-row pt-6 ml-4">
                                    <button
                                        type="button"
                                        @click="announcementModal = true"
                                        class="pr-4 text-white hover:text-info"
                                    >
                                        <label
                                            class="uppercase"
                                            style="
                                                letter-spacing: 0.1em;
                                                font-weight: 600;
                                                cursor: pointer;
                                            "
                                            >See More</label
                                        >
                                    </button>
                                </div>
                                <daisy-modal
                                    title="Deployment Announcement"
                                    width="85%"
                                    overlayTheme="dark"
                                    modal-theme="dark"
                                    hideCloseButton
                                    ref="anModal"
                                    @confirm="announcementModal = false"
                                    @cancel="announcementModal = false"
                                >
                                    <div class="flex-1 flex-col">
                                        <div class="flex flex-row">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                class="w-6 h-6 mx-2 stroke-current"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                ></path>
                                            </svg>
                                            <label
                                                >New Version deployed! - v{{
                                                    announcements[0].version
                                                }}</label
                                            >
                                        </div>

                                        <br />
                                        <ul>
                                            <li
                                                v-for="(
                                                    note, x
                                                ) in announcements[0].notes"
                                            >
                                                * {{ note }}
                                            </li>
                                        </ul>
                                        <div class="flex flex-row pt-6 ml-4">
                                            <button
                                                type="button"
                                                @click="
                                                    announcementModal = false
                                                "
                                                class="btn btn-success pr-4 text-white hover:text-error"
                                            >
                                                <label
                                                    class="uppercase"
                                                    style="
                                                        letter-spacing: 0.1em;
                                                        font-weight: 600;
                                                        cursor: pointer;
                                                    "
                                                    >Okay!</label
                                                >
                                            </button>
                                        </div>
                                    </div>
                                </daisy-modal>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </jet-bar-container>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
import DaisyModal from "@/Components/DaisyModal";

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
import DashboardStats from "@/Pages/Dashboards/Partials/DashboardStats";
import DashboardHeader from "@/Pages/Dashboards/Partials/DashboardHeader";

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
        AppLayout,
        JetBarContainer,
        JetBarAlert,
        GymRevenueTable,
        JetBarBadge,
        JetBarIcon,
        FontAwesomeIcon,
        DashboardStats,
        DashboardHeader,
        DaisyModal,
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
