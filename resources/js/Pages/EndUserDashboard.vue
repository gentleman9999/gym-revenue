<template>
    <LayoutHeader title="Dashboard">
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
                </daisy-modal>
            </div>
        </div>
        <div class="flex flex-row justify-between">
            <div>Dashboard of {{ accountName }}</div>
        </div>
    </LayoutHeader>

    <jet-bar-container> END USER DASHBOARD </jet-bar-container>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import JetBarAlert from "@/Components/JetBarAlert.vue";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer.vue";
import JetBarStatCard from "@/Components/JetBarStatCard.vue";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable.vue";
import JetBarBadge from "@/Components/JetBarBadge.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import DaisyModal from "@/Components/DaisyModal.vue";

const props = defineProps({
    accountName: {
        type: String,
    },
    announcements: {
        type: Array,
    },
});
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

onMounted(() => {
    if (props.announcements.length > 0) {
        showAnnouncement.value = true;
    }
});
</script>
