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
            <div class="text-base-content text-lg text-bold">
                Draft Related Items
            </div>
        </template>
        <div class="flex flex-col">
            <draft-related-chart :data="barMock" />
            <Button secondary size="xs" class="self-end mt-6">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import SalesCard from "@/Components/Card.vue";
import Button from "@/Components/Button.vue";

import DraftRelatedChart from "./DraftRelatedChart.vue";

const barMock = [
    {
        name: "Net Profit",
        data: [44, 55, 57, 56, 61],
    },
];
</script>
