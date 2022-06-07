<template>
    <kpi-card-wrapper>
        <kpi-card
            label="Category 1"
            class="kpi-card w-full min-w-max"
            header="Revenue Per Member Overall"
            :options="{
                collapse: true,
                favorite: true,
                more: true,
            }"
        >
            <div class="w-full">
                <revenue-chart />
            </div>
            <div class="card-footer">
                <button-tabs
                    :items="buttons"
                    :activeIndex="activeIndex"
                    :onChange="setActive"
                />

                <div class="flex flex-row pl-6 mt-4 justify-between">
                    <div class="space-x-2.5">
                        <span class="text-secondary text-base">Total:</span>
                        <span class="text-lg text-base-content">$4,895</span>
                    </div>
                    <div
                        class="flex items-center text-base-content bg-secondary rounded hover:text-secondary hover:bg-transparent px-2.5 py-1.25"
                    >
                        Detail
                    </div>
                </div>
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<script setup>
import { ref } from "vue";
import RevenueChart from "./RevenueChart";
import ButtonTabs from "@/Components/ButtonTabs.vue";
import KpiCard from "@/Components/Card.vue";
import KpiCardWrapper from "../KpiCardWrapper.vue";

const activeIndex = ref(0);
const setActive = (value) => {
    activeIndex.value = value;
};

const buttons = ["Membership", "Personal Training", "Retail Sales"];
</script>
