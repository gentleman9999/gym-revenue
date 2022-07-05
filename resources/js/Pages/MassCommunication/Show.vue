<template>
    <LayoutHeader title="Mass Communications Dashboard" />
    <jet-bar-container>
        <div class="flex flex-col">
            <div class="campaign-wrapper">
                <div class="campaign-title">Future Campaigns</div>
                <div class="campaign-body flex-row">
                    <future-campaign />
                </div>
            </div>
            <div class="campaign-wrapper">
                <div class="campaign-title">Current Campaigns</div>
                <div class="campaign-body flex-col">
                    <div class="text-sm mb-4">
                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam nonumy eirmod tempor
                    </div>
                    <current-campaign />
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
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import FutureCampaign from "./components/FutureCampaign/FutureCampaign.vue";
import CurrentCampaign from "./components/CurrentCampaign/CurrentCampaign.vue";
import RecentCampaign from "./components/RecentCampaign/RecentCampaign.vue";
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
