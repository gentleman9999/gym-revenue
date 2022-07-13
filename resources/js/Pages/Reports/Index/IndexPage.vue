<template>
    <LayoutHeader title="Executive Overview">
        <reports-title>
            <template #title>
                <h1>Executive Overview</h1>
                <div
                    class="flex flex-row text-base-content space-x-2 text-xs items-center"
                >
                    <span class="cursor-pointer text-secondary">Monthly</span>
                    <span class="cursor-pointer">Quartely</span>
                    <span class="cursor-pointer">Annual</span>
                    <span class="cursor-pointer">YOY Comparisons</span>
                </div>
            </template>
        </reports-title>
    </LayoutHeader>
    <jet-bar-container>
        <div class="flex flex-col">
            <overview-table />
            <div class="flex flex-row mt-12 mb-4 justify-center">
                <div class="px-2">
                    <cpa-cpl-clicks />
                </div>
                <div class="px-2">
                    <email-stats-card />
                </div>
            </div>
            <div class="w-fit m-auto">
                <conversion-rate-card />
            </div>
        </div>
    </jet-bar-container>
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import ReportsTitle from "../components/ReportsTitle.vue";
import OverviewTable from "./components/OverviewTable.vue";
import EmailStatsCard from "./components/EmailStatsCard.vue";
import CpaCplClicks from "./components/CpaCplClicks.vue";
import ConversionRateCard from "./components/ConversionRateCard/ConversionRateCard.vue";
</script>
