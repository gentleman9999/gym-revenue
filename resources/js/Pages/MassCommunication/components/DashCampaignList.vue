<template>
    <div
        class="flex justify-between items-center bg-secondary py-2 px-4 rounded-t-lg mt-8 flex-wrap"
    >
        <p class="text-2xl font-bold capitalize">{{ selectedSource }}</p>
        <select
            v-model="selectedSource"
            class="p-0 min-h-0 h-auto px-8 rounded-md bg-secondary focus-within:outline-base-content focus-within:shadow-none focus:shadow-none capitalize"
        >
            <option
                class="capitalize"
                v-for="source in CAMPAIGN_FILTERS"
                :key="source"
            >
                {{ source }}
            </option>
        </select>
    </div>
    <div
        class="border-secondary border-2 bg-black rounded-b-lg lg:px-20 md:px-10 px-2 pb-4"
    >
        <div class="cl-head mt-8 pr-10">
            <table
                class="w-full table-fixed border-collapse border-spacing-0 w-max"
            >
                <tr class="text-sm">
                    <th colspan="4" class="text-left py-2">Campaign</th>
                    <th class="">Started</th>
                    <th class="">In Progress</th>
                    <th class="">Completed</th>
                    <th colspan="2"></th>
                    <th colspan="2"></th>
                </tr>
            </table>
        </div>
        <div class="cl-body max-h-80 overflow-y-scroll pr-8">
            <table class="w-full table-fixed border-collapse border-spacing-0">
                <tbody>
                    <CampaignListItem
                        v-for="c in filteredCampaigns"
                        :campaign="c"
                        :campaign-type="type"
                        @view="() => openCampaign(c)"
                        :key="c"
                    />

                    <tr
                        v-if="filteredCampaigns.length === 0"
                        class="opacity-70"
                    >
                        <td>No Campaigns Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import CampaignListItem from "./CampaignListItem.vue";

import { computed, ref } from "vue";
import {
    CAMPAIGN_FILTERS,
    CURRENT_CAMPAIGNS,
    DRAFT_CAMPAIGNS,
    ALL_CAMPAIGNS,
    FUTURE_CAMPAIGNS,
    COMPLETED_CAMPAIGNS,
    filterCampaigns,
} from "@/Pages/MassCommunication/components/Creator/helpers";

const emit = defineEmits(["open-campaign", "new-campaign"]);

const selectedSource = ref(ALL_CAMPAIGNS);

const props = defineProps({
    type: { required: true, type: String },
    campaigns: { required: true, type: Array },
});

const filteredCampaigns = computed(() =>
    filterCampaigns(props.campaigns, selectedSource.value)
);
const openCampaign = (campaign) =>
    emit("open-campaign", { type: props.type, campaign });
</script>
