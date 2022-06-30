<template>
    <campaign-calendar />
    <div class="mx-10 w-full">
        <h2>July 2022</h2>
        <future-campaign-summary />
    </div>
</template>
<script setup>
import CampaignCalendar from "./CampaignCalendar/CampaignCalendar.vue";
import FutureCampaignSummary from "./FutureCampaignSummary.vue";
</script>
<style scoped>
h2 {
    @apply font-bold text-3xl;
}
</style>
