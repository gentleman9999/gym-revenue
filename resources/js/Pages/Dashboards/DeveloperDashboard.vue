<template>
    <LayoutHeader title="Dashboard">
        <!-- @todo - move this to its own component when props.announcements scale out -->
        <div
            class="flex flex-row justify-center pb-4"
            v-if="props.announcements.length > 0 && showAnnouncement"
        >
            <div class="alert alert-info xl:grid-cols-4">
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
                                props.announcements[0].version
                            }}</label
                        >
                    </div>

                    <br />
                    <ul>
                        <li
                            v-for="(note, x) in props.announcements[0].notes"
                            v-show="x < 3"
                        >
                            * {{ note }}
                        </li>
                    </ul>

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
                        <button
                            type="button"
                            @click="showAnnouncement = false"
                            class="pr-4 text-white hover:text-error"
                        >
                            <label
                                class="uppercase"
                                style="
                                    letter-spacing: 0.1em;
                                    font-weight: 600;
                                    cursor: pointer;
                                "
                                >Close Me</label
                            >
                        </button>
                    </div>
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
                                    props.announcements[0].version
                                }}</label
                            >
                        </div>

                        <br />
                        <ul>
                            <li
                                v-for="(note, x) in props.announcements[0]
                                    .notes"
                            >
                                * {{ note }}
                            </li>
                        </ul>
                        <div class="flex flex-row pt-6 ml-4">
                            <button
                                type="button"
                                @click="announcementModal = false"
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
        <div class="flex flex-row justify-between">
            <div>
                <p>Dashboard - {{ props.teamName }}</p>
            </div>
            <div>
                <p>{{ props.accountName }}</p>
            </div>
        </div>
    </LayoutHeader>

    <jet-bar-container>
        <!-- @todo - leave this here and make it contextual, dynamic, pusher-enabled? -->
        <!-- <jet-bar-alert text="This is an alert message" /> -->

        <jet-bar-stats-container>
            <jet-bar-stat-card
                v-for="(widget, idx) in props.widgets"
                :title="widget.title"
                :number="widget.value"
                :type="widget.type"
            >
                <template v-slot:icon>
                    <jet-bar-icon :type="widget.icon" fill />
                </template>
            </jet-bar-stat-card>
        </jet-bar-stats-container>

        <div class="container max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="grid xl:grid-cols-2 gap-4">
                <div class="card bg-base-100 shadow-2xl">
                    <div class="card-body">
                        <h2 class="card-title">Available Teams</h2>
                        <div class="divider mt-0"></div>
                        <p
                            class="text-center"
                            v-if="
                                !('is_being_impersonated' in $page.props.user)
                            "
                        >
                            Select a Team that you'd like to Access or use the
                            Switch Team toggle at the top.
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
                                class="table-compact"
                                v-if="
                                    !(
                                        'is_being_impersonated' in
                                        $page.props.user
                                    )
                                "
                            >
                                <tbody>
                                    <tr
                                        v-for="team in props.teams"
                                        class="hover"
                                    >
                                        <td>
                                            {{ team["name"] }}
                                        </td>
                                        <td>
                                            <template v-if="team?.client?.name">
                                                ({{ team?.client?.name }})
                                            </template>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-success"
                                                @click="switchToTeam(team.id)"
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
                        <h2 class="card-title">Your Current Assignments</h2>
                        <div class="divider mt-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </jet-bar-container>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import JetBarAlert from "@/Components/JetBarAlert.vue";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer.vue";
import JetBarStatCard from "@/Components/JetBarStatCard.vue";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable.vue";
import JetBarBadge from "@/Components/JetBarBadge.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
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
import queries from "@/gql/queries";
import { useQuery } from "@vue/apollo-composable";

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

const { result } = useQuery(queries["dashboardQuery"]);
const props = computed(() => result.value?.props ?? {});

const showAnnouncement = ref(false);
const announcementModal = ref(false);
const anModal = ref(null);

watch([announcementModal], () => {
    if (announcementModal.value) {
        anModal.value.open();
    } else {
        anModal.value.close();
    }
});

const switchToTeam = (teamId) => {
    Inertia.put(route("current-team.update", teamId), {
        preserveState: false,
    });
};

onMounted(() => {
    if (props.value.announcements?.length > 0) {
        showAnnouncement.value = true;
    }
});
</script>
