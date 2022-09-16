<template>
    <kpi-card-wrapper class="lg:w-1/2">
        <kpi-card class="w-full">
            <div class="flex flex-col">
                <div
                    class="flex flex-row justify-between border-b border-neutral mb-4"
                    v-for="item in summary"
                    :key="item.key"
                >
                    <span class="text-base text-secondary w-1/2">{{
                        item.label
                    }}</span>
                    <span class="text-2xl">{{ item.value }}</span>
                </div>
                <div class="flex flex-row justify-between">
                    <div>
                        <span class="text-base text-secondary"
                            >Cancellations (Units and $)</span
                        >
                        <br />
                        <span class="ml-6"
                            >Net change from new to cancellations</span
                        >
                    </div>
                    <span class="text-2xl">34/55%</span>
                </div>
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<script setup>
import KpiCardWrapper from "../KpiCardWrapper.vue";
import KpiCard from "@/Components/Card.vue";
const summary = [
    {
        key: "avg_sold",
        label: "Average packages and sessions sold",
        value: 34,
    },
    {
        key: "avg_trained",
        label: "Average sessions trained",
        value: 78,
    },
    {
        key: "avg_trainer_per_loc",
        label: "Average trainers per location with average clients per trainer",
        value: 9,
    },
    {
        key: "total_active",
        label: "Total active clients",
        value: 1290,
    },
    {
        key: "total_inactive",
        label: "Total delinquent clients",
        value: 256,
    },
];
</script>
