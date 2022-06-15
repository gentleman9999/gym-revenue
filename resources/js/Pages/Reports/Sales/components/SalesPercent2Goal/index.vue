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
                Percentage to Goal
            </div>
        </template>
        <div class="flex flex-col">
            <percent2-goal-chart :data="[80]" />
            <Button secondary size="xs" class="self-end mt-6">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import SalesCard from "@/Components/Card";
import Button from "@/Components/Button";

import Percent2GoalChart from "../Charts/Percent2GoalChart";
</script>
