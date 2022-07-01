<template>
    <div class="flex flex-row justify-between">
        <div class="recent-campaign-title">July 2020</div>
        <campaign-filters />
    </div>
    <recent-campaign-summary />
    <conversion-data />
</template>
<style scoped>
.recent-campaign-title {
    @apply text-3xl font-bold;
}
</style>
<script setup>
import CampaignFilters from "./CampaignFilters.vue";
import RecentCampaignSummary from "./RecentCampaignSummary.vue";
import ConversionData from "./ConversionData/ConversionData.vue";
const categories = new Array(4).fill("Category");
</script>
