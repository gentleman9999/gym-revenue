<template>
    <LayoutHeader title="Mass Communications Dashboard"> </LayoutHeader>

    <teleport to="#premaincontent">
        <ToolBar
            :toggleDripBuilder="toggleDripBuilder"
            :toggleScheduleBuilder="toggleScheduleBuilder"
        />
    </teleport>

    <jet-bar-container>
        <div class="flex flex-wrap gap-2 justify-between">
            <h1 class="text-2xl">Mass Comms Dashboard</h1>
            <div class="flex gap-2">
                <button
                    @click="handleClickTabSchedule"
                    class="btn btn-outline btn-secondary border-secondary btn-sm !text-base-content !normal-case"
                    :class="{ '!btn-active': selectedTab === 'scheduled' }"
                >
                    Scheduled Campaigns
                </button>
                <button
                    @click="handleClickTabDrip"
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                    :class="{ '!btn-active': selectedTab === 'drip' }"
                >
                    Drip Campaigns
                </button>
            </div>
        </div>

        <CampaignListDisplay
            v-if="selectedTab === 'scheduled'"
            type="scheduled"
        />

        <CampaignListDisplay v-if="selectedTab === 'drip'" type="drip" />
    </jet-bar-container>

    <CampaignBuilder
        v-if="isEditingDrip"
        type="drip"
        @close="resetEditors"
        :campaign_id="selectedCampaignId"
    />
    <CampaignBuilder
        v-if="isEditingScheduled"
        type="scheduled"
        @close="resetEditors"
        :campaign_id="selectedCampaignId"
    />
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";

import ToolBar from "./components/ToolBar.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import CampaignBuilder from "./components/Creator/CampaignBuilder.vue";
import CampaignListDisplay from "./Partials/CampaignListDisplay.vue";

const props = defineProps({
    type: {
        type: String,
        default: "scheduled",
    },
});

/**
 * Campaign type display & handling
 */
// const selectedTab = ref(props.type); // @TODO figure out how to tie the URL to this
const selectedTab = ref("scheduled");

const handleClickTabSchedule = () => {
    if (selectedTab.value === "scheduled") return;
    selectedTab.value = "scheduled";
};

const handleClickTabDrip = () => {
    if (selectedTab.value === "drip") return;
    selectedTab.value = "drip";
};

/**
 * Campaign creation/updating handling
 */
const isEditingDrip = ref(false);
const isEditingScheduled = ref(false);

const toggleDripBuilder = () => (isEditingDrip.value = !isEditingDrip.value);
const toggleScheduleBuilder = () =>
    (isEditingScheduled.value = !isEditingScheduled.value);

const selectedCampaignId = ref("");

const resetEditors = () => {
    isEditingDrip.value = false;
    isEditingScheduled.value = false;
    selectedCampaignId.value = "";
};

/**
 * We pass the id to the campaign builder and it queries more detailed info about the campaign
 * the toolbar can open whichever one it wants and will always be a new campaign
 */
const handleOpenCampaignEditor = (id) => {
    selectedCampaignId.value = id;
    if (selectedTab.value === "drip") return toggleDripBuilder();
    return toggleScheduleBuilder();
};

// am i supposed to be able to edit a campaign? didn't see it anywhere

// const openCampaign = async ({ type, campaign }) => {
// console.log({ type, campaign });
// if (type === "ScheduledCampaign") {
//     const response = await axios.get(
//         route("mass-comms.scheduled-campaigns.get", campaign.id)
//     );
//     selectedCampaign.value = response.data;
// }
// if (type === "DripCampaign") {
//     const response = await axios.get(
//         route("mass-comms.drip-campaigns.get", campaign.id)
//     );
//     selectedCampaign.value = response.data;
// }
// selectedCampaignType.value = type;
// };
</script>
