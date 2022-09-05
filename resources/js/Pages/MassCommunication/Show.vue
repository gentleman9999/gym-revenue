<template>
    <LayoutHeader title="Mass Communications Dashboard">
        <ToolBar
            :toggleDripBuilder="toggleDripBuilder"
            :toggleScheduleBuilder="toggleScheduleBuilder"
        />
    </LayoutHeader>
    <jet-bar-container>
        <div class="flex flex-wrap gap-2 justify-between">
            <h1 class="text-2xl">Mass Comms Dashboard</h1>
            <div class="flex gap-2">
                <button
                    @click="
                        Inertia.get(route('mass-comms.dashboard', 'scheduled'))
                    "
                    class="btn btn-outline btn-secondary border-secondary btn-sm !text-base-content !normal-case"
                    :class="{ '!btn-active': type === 'ScheduledCampaign' }"
                >
                    Scheduled Campaigns
                </button>
                <button
                    @click="Inertia.get(route('mass-comms.dashboard', 'drip'))"
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                    :class="{ '!btn-active': type === 'DripCampaign' }"
                >
                    Drip Campaigns
                </button>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="campaign-wrapper">
                <div class="campaign-title">Future Campaigns</div>
                <div class="campaign-body flex-col">
                    <campaign-list
                        :campaigns="campaigns"
                        :type="type"
                        :filter="FUTURE_CAMPAIGNS"
                        @open-campaign="openCampaign"
                    />
                </div>
            </div>
            <div class="campaign-wrapper">
                <div class="campaign-title">Current Campaigns</div>
                <div class="campaign-body flex-col">
                    <campaign-list
                        :campaigns="campaigns"
                        :filter="CURRENT_CAMPAIGNS"
                        :type="type"
                    />
                </div>
            </div>
            <div class="campaign-wrapper">
                <div class="campaign-title">Recent campaigns</div>
                <div class="campaign-body flex-col">
                    <recent-campaign />
                </div>
            </div>
            <div class="text-secondary text-lg pt-6 pl-6 cursor-pointer">
                Load all activity
            </div>
        </div>
    </jet-bar-container>
    <CampaignBuilder
        v-if="dripV || selectedCampaignType === 'DripCampaign'"
        campaignType="drip"
        @close="handleDone"
        :topol-api-key="topolApiKey"
        :precampaign="selectedCampaign"
        @done="handleDone"
    />
    <CampaignBuilder
        v-if="scheduleV || selectedCampaignType === 'ScheduledCampaign'"
        campaignType="scheduled"
        @close="handleDone"
        :topol-api-key="topolApiKey"
        :precampaign="selectedCampaign"
        @done="handleDone"
    />
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import ToolBar from "./components/ToolBar.vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import CampaignList from "./components/CampaignList/CampaignList.vue";
import RecentCampaign from "./components/RecentCampaign/RecentCampaign.vue";
import CampaignBuilder from "./components/Creator/CampaignBuilder.vue";
import {
    CURRENT_CAMPAIGNS,
    FUTURE_CAMPAIGNS,
    ALL_CAMPAIGNS,
} from "@/Pages/MassCommunication/components/Creator/helpers";

const props = defineProps({
    topolApiKey: {
        type: String,
        required: true,
    },
    campaigns: {
        type: Array,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
});

const dripV = ref(false);
const scheduleV = ref(false);

const toggleDripBuilder = () => (dripV.value = !dripV.value);
const toggleScheduleBuilder = () => (scheduleV.value = !scheduleV.value);

const selectedCampaign = ref(null);
const selectedCampaignType = ref(null);
const openCampaign = async ({ type, campaign }) => {
    console.log({ type, campaign });
    if (type === "ScheduledCampaign") {
        const response = await axios.get(
            route("mass-comms.scheduled-campaigns.get", campaign.id)
        );
        selectedCampaign.value = response.data;
    }
    if (type === "DripCampaign") {
        const response = await axios.get(
            route("mass-comms.drip-campaigns.get", campaign.id)
        );
        selectedCampaign.value = response.data;
    }
    selectedCampaignType.value = type;
};
const handleDone = () => {
    selectedCampaign.value = null;
    selectedCampaignType.value = null;
    dripV.value = false;
    scheduleV.value = false;
    Inertia.reload();
};
</script>

<style scoped>
.campaign-wrapper {
    @apply flex flex-col pt-12;
}
.campaign-title {
    @apply pb-4 text-xl font-bold text-base-content;
}

.campaign-body {
    @apply flex border border-secondary rounded p-4 bg-neutral-900;
}
</style>

<style>
main {
    @apply !p-0;
}

#layout-header {
    @apply !p-0 !m-0 !max-w-none;
}
</style>
