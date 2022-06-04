<template>
    <kpi-card-wrapper>
        <kpi-card
            class="kpi-card w-full"
            label="Category 3"
            header="Personal Training"
            :options="{
                collapse: true,
                favorite: true,
                more: true,
            }"
        >
            <div class="font-bold text-base-content text-sm mb-3 mt-[-12px]">
                Recurring Services Revenue Per Member
            </div>
            <div class="w-full">
                <training-chart />
            </div>
            <div class="card-footer">
                <div class="flex flex-col mt-4 justify-between">
                    <div class="space-x-2.5">
                        <span class="text-secondary text-base"
                            >Overall PT Units:</span
                        >
                        <span class="text-lg text-base-content">9,347</span>
                    </div>
                    <div class="space-x-2.5">
                        <span class="text-secondary text-base"
                            >Overall PT $ Amount:</span
                        >
                        <span class="text-lg text-base-content">$156,098</span>
                    </div>
                </div>
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>

<script setup>
import { ref } from "vue";
import TrainingChart from "./TrainingChart";
import KpiCard from "@/Components/Card.vue";
import KpiCardWrapper from "../KpiCardWrapper.vue";
</script>
