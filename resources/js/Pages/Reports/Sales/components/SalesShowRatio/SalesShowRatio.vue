<template>
    <sales-card
        :options="{
            favorite: true,
            collapse: true,
            borderedTitle: true,
        }"
        class="h-full"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">Show Ratio</div>
        </template>
        <div class="flex flex-col">
            <show-ratio-chart :data="barMock" />
            <div class="text-center mt-3 mb-7">First Appointment Status</div>
            <Button secondary size="xs" class="self-end">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import SalesCard from "@/Components/Card.vue";
import Button from "@/Components/Button.vue";

import ShowRatioChart from "./ShowRatioChart.vue";

const barMock = [
    {
        name: "Show Ratio",
        data: [68.4, 29.5, 2.1, 44.3],
    },
];
</script>
