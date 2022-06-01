<template>
    <app-layout>
        <template #header>
            <reports-title>
                <template #title>
                    <h1>Executive Overview</h1>
                    <div
                        class="flex flex-row text-base-content space-x-2 text-xs items-center"
                    >
                        <span class="cursor-pointer text-secondary"
                            >Monthly</span
                        >
                        <span class="cursor-pointer">Quartely</span>
                        <span class="cursor-pointer">Annual</span>
                        <span class="cursor-pointer">YOY Comparisons</span>
                    </div>
                </template>
            </reports-title>
        </template>
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
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import ReportsTitle from "../components/ReportsTitle";
import OverviewTable from "./components/OverviewTable";
import EmailStatsCard from "./components/EmailStatsCard";
import CpaCplClicks from "./components/CpaCplClicks";
import ConversionRateCard from "./components/ConversionRateCard";
</script>
