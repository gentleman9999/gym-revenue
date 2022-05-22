<template>
    <app-layout>
        <template #header>
            <!-- @todo - move this to its own component when announcements scale out -->
            <div
                class="flex flex-row justify-center pb-4"
                v-if="announcements.length > 0 && showAnnouncement"
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
                                    announcements[0].version
                                }}</label
                            >
                        </div>

                        <br />
                        <ul>
                            <li
                                v-for="(note, x) in announcements[0].notes"
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
                    <announce-modal
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
                                <li v-for="(note, x) in announcements[0].notes">
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
                    </announce-modal>
                </div>
            </div>
            <div class="flex flex-row justify-between">
                <div>
                    <p>Dashboard - {{ teamName }}</p>
                </div>
                <div>
                    <p>{{ accountName }}</p>
                </div>
            </div>
        </template>

        <jet-bar-container>
            <!-- @todo - leave this here and make it contextual, dynamic, pusher-enabled? -->
            <!-- <jet-bar-alert text="This is an alert message" /> -->

            <jet-bar-stats-container>
                <jet-bar-stat-card
                    v-for="(widget, idx) in widgets"
                    :title="widget.title"
                    :number="widget.value"
                    :type="widget.type"
                >
                    <template v-slot:icon>
                        <jet-bar-icon :type="widget.icon" fill />
                    </template>
                </jet-bar-stat-card>

                <!--
                <jet-bar-stat-card title="Total Revenue Funneled" number="$ 0" type="success">
                    <template v-slot:icon>
                        <jet-bar-icon type="money" fill />
                    </template>
                </jet-bar-stat-card>

                <jet-bar-stat-card title="Total Profits" number="$ 0" type="info">
                    <template v-slot:icon>
                        <jet-bar-icon type="cart" fill />
                    </template>
                </jet-bar-stat-card>

                <jet-bar-stat-card title="Total MCU Films" number="26" type="danger">
                    <template v-slot:icon>
                        <jet-bar-icon type="message" fill />
                    </template>
                </jet-bar-stat-card>
                -->
            </jet-bar-stats-container>

            <gym-revenue-table
                :headers="['client', 'status', 'joined', '', '']"
                :resource="clients"
            >
                <tr class="hover" v-for="client in clients" :key="client.id">
                    <td>{{ client.name }}</td>
                    <td>
                        <jet-bar-badge
                            text="Active"
                            type="success"
                            v-if="client.active"
                        />
                        <jet-bar-badge text="Not Active" type="danger" v-else />
                    </td>
                    <td>{{ client.created_at }}</td>
                    <td>
                        <inertia-link href="#" class="">Edit</inertia-link>
                    </td>
                    <td>
                        <inertia-link href="#" class="hover:">
                            <jet-bar-icon type="trash" fill />
                        </inertia-link>
                    </td>
                </tr>
            </gym-revenue-table>
        </jet-bar-container>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";
import JetBarStatCard from "@/Components/JetBarStatCard";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
import AnnounceModal from "@/Components/SweetModal3/SweetModal";

export default {
    components: {
        AppLayout,
        JetBarContainer,
        JetBarAlert,
        JetBarStatsContainer,
        JetBarStatCard,
        GymRevenueTable,
        JetBarBadge,
        JetBarIcon,
        AnnounceModal,
    },
    props: ["teamName", "clients", "accountName", "widgets", "announcements"],
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
    methods: {},
    computed: {},
    mounted() {
        if (this.announcements.length > 0) {
            this.showAnnouncement = true;
        }
        console.log("GymRevenue Dashboard");
    },
};
</script>
