<template>
    <LayoutHeader :title="title" />
    <teleport to="#premaincontent">
        <ToolBar
            :toggleDripBuilder="toggleDripBuilder"
            :toggleScheduleBuilder="toggleScheduleBuilder"
        />
    </teleport>
    <jet-bar-container>
        <!-- dashboard 'nav' -->
        <div class="flex gap-2">
            <button
                @click="
                    Inertia.get(
                        route('mass-comms.campaigns.dashboard', 'scheduled')
                    )
                "
                class="btn btn-outline btn-secondary border-secondary btn-sm !text-base-content !normal-case"
                :class="{ '!btn-active': type === 'ScheduledCampaign' }"
            >
                Scheduled Campaigns
            </button>
            <button
                @click="
                    Inertia.get(route('mass-comms.campaigns.dashboard', 'drip'))
                "
                class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                :class="{ '!btn-active': type === 'DripCampaign' }"
            >
                Drip Campaigns
            </button>
        </div>

        <CampaignBuilder
            v-if="dripV || selectedCampaignType === 'DripCampaign'"
            campaignType="drip"
            @close="handleDone"
            :campaign="selectedCampaign"
            @done="handleDone"
        />
        <CampaignBuilder
            v-if="scheduleV || selectedCampaignType === 'ScheduledCampaign'"
            campaignType="scheduled"
            @close="handleDone"
            :campaign="selectedCampaign"
            @done="handleDone"
        />
        <div
            class="grid grid-rows-1 grid-cols-1 md:grid-rows-2 lg:grid-rows-1 lg:grid-cols-3 md:grid-flow-col gap-8 py-8"
        >
            <SalesShowRatio class="bg-black" />
            <SalesDraftRelated class="bg-black" />

            <div
                class="bg-black border-secondary border-[1px] rounded flex flex-col px-8 py-4 items-center"
            >
                <div
                    class="text-lg font-bold border-b-[1px] border-base-content border-opacity-30"
                >
                    Build a Campaign
                </div>
                <div
                    class="flex-grow flex flex-col items-center justify-center"
                >
                    <button
                        class="btn btn-outline btn-secondary btn-circle btn-lg my-8"
                        @click="
                            type === 'ScheduledCampaign'
                                ? toggleScheduleBuilder()
                                : toggleDripBuilder()
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="100%"
                            height="100%"
                            viewBox="0 0 46 49"
                            class="p-3"
                        >
                            <g
                                id="Group_4084"
                                data-name="Group 4084"
                                transform="translate(-22.406 -20.5)"
                            >
                                <line
                                    id="Line_261"
                                    data-name="Line 261"
                                    y2="47"
                                    transform="translate(45.406 21.5)"
                                    fill="none"
                                    stroke="#fff"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                />
                                <line
                                    id="Line_262"
                                    data-name="Line 262"
                                    x1="44"
                                    transform="translate(23.406 45.5)"
                                    fill="none"
                                    stroke="#fff"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                />
                            </g>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <DashCampaignList
            :type="type"
            @open-campaign="openCampaign"
            :campaigns="campaigns"
        />
    </jet-bar-container>
</template>

<script setup>
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import SalesDraftRelated from "@/Pages/Reports/Sales/components/SalesDraftRelated/SalesDraftRelated.vue";
import SalesShowRatio from "@/Pages/Reports/Sales/components/SalesShowRatio/SalesShowRatio.vue";
import ToolBar from "./components/ToolBar.vue";
import CampaignBuilder from "./components/Creator/CampaignBuilder.vue";
import DashCampaignList from "./components/DashCampaignList.vue";

const dripV = ref(false);
const scheduleV = ref(false);

const toggleDripBuilder = () => (dripV.value = !dripV.value);
const toggleScheduleBuilder = () => (scheduleV.value = !scheduleV.value);

const props = defineProps({
    campaigns: {
        type: Array,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
});

const selectedCampaign = ref(null);
const selectedCampaignType = ref(null);
const openCampaign = async ({ type, campaign }) => {
    console.log({ type, campaign });
    if (type === "ScheduledCampaign") {
        const response = await axios.get(
            route("mass-comms.scheduled-campaigns.get", campaign.id)
        );
        selectedCampaign.value = response.data;
    } else if (type === "DripCampaign") {
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

const title = computed(() =>
    props.type === "DripCampaign"
        ? "Drip Campaigns Dashboard"
        : "Scheduled Campaigns Dashboard"
);
</script>

<style scoped></style>

<style>
main {
    @apply !p-0;
}

#layout-header {
    @apply !p-0 !m-0 !max-w-none;
}
</style>
