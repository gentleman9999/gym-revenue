<template>
    <kpi-card-wrapper class="lg:w-2/3">
        <kpi-card
            class="kpi-card w-full"
            header="Membership"
            :options="{
                collapse: true,
                favorite: true,
            }"
        >
            <div class="w-full">
                <data-table class="kpi-table" :columns="columns" :data="data" />
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<style scoped>
.kpi-table {
    @apply max-h-60 text-neutral-450;
}
@media only screen and (max-width: 768px) {
    .kpi-table {
        width: calc(100vw - 64px);
    }
}
</style>
<script setup>
import { h } from "vue";

import KpiCard from "@/Components/Card.vue";
import DataTable from "@/Components/DataTable";
import KpiCardWrapper from "./KpiCardWrapper.vue";

const columns = [
    {
        field: "type",
        label: "Type",
        class: "min-w-[165px]",
        renderer: (value) => {
            return h(
                "div",
                { class: "flex flex-row max-content items-center" },
                [h("img", { src: "/img/avatar.svg" }), h("span", value)]
            );
        },
    },
    { field: "billing", label: "Billing" },
    { field: "total_active", label: "Total members: Active" },
    { field: "total_non_active", label: "Total Membership:Non active" },
    { field: "time_frame", label: "Delinquent by:time frame" },
];
const data = [
    {
        id: 1,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 2,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 3,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 4,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 5,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 6,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 7,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 8,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 10,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 11,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
    {
        id: 12,
        type: "Account Type",
        billing: "GymRevenue",
        total_active: 3786,
        total_non_active: 1593,
        time_frame: 487,
    },
];
</script>
