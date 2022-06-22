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
                Conversion by Entry Source MTD
            </div>
        </template>
        <div class="flex flex-col">
            <conversion-by-entry-chart :data="mock" />
            <Button secondary size="xs" class="self-end mt-6">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import SalesCard from "@/Components/Card";
import Button from "@/Components/Button";

import ConversionByEntryChart from "./ConversionByEntryChart";
const mock = [
    {
        data: [73.3, 59.6, 58.3, 42.5, 13.8, 9.7, 5.5, 1.6, 0, 0],
    },
];
</script>
