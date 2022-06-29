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
import Button from "@/Components/Button";
import SelectBox from "@/Components/SelectBox";
import CampaignFilters from "./CampaignFilters";
import RecentCampaignSummary from "./RecentCampaignSummary";
import ConversionData from "./ConversionData";
const categories = new Array(4).fill("Category");
</script>
