<template>
    <Card
        :options="{
            more: true,
        }"
    >
        <template #cardTitle>
            <div
                class="text-base-content text-xl border-b border-neutral-450 pr-16"
            >
                Conversion Rates and Rolling
            </div>
        </template>
        <div class="flex flex-col">
            <conversion-rate-chart />
            <div class="flex flex-row justify-end pb-2">
                <Button secondary size="xs">View</Button>
            </div>
        </div>
    </Card>
</template>
<script setup>
import Button from "@/Components/Button";
import Card from "@/Components/Card";

import ConversionRateChart from "./ConversionRateChart";
</script>
