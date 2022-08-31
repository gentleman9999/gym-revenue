<template>
    <kpi-card-wrapper class="lg:w-1/2">
        <kpi-card
            class="kpi-card"
            header="Penetration Percentage"
            :options="{
                collapse: false,
                favorite: false,
                filter: [
                    'Atlanta',
                    'Athens',
                    'Augusta',
                    'Albany City',
                    'Avlan',
                    'Bellville City',
                ],
                filterLabel: 'Location(s)',
            }"
        >
            <data-table :columns="columns" :data="data" class="kpi-table" />
        </kpi-card>
    </kpi-card-wrapper>
</template>
<style scoped>
.kpi-card {
    @apply w-full;
}

@media only screen and (max-width: 768px) {
    .kpi-table {
        width: calc(100vw - 96px);
    }
    .kpi-card {
        width: calc(100vw - 64px);
    }
}
</style>
<script setup>
import { h } from "vue";
import KpiCardWrapper from "../KpiCardWrapper.vue";
import KpiCard from "@/Components/Card.vue";
import DataTable from "@/Components/DataTable";

const columns = [
    {
        field: "location",
        label: "Location(s)",
        class: "min-w-[165px] text-secondary",
        renderer: (value) => {
            return h("div", { class: "text-left" }, [value]);
        },
    },
    {
        field: "daily",
        label: "Daily",
        class: "text-secondary",
    },
    {
        field: "monthly",
        label: "Monthly",
        class: "text-secondary",
    },
    {
        field: "quarterly",
        label: "Quarterly",
        class: "text-secondary",
    },
    {
        field: "overall",
        label: "Overall",
        class: "text-secondary",
    },
];
const data = [
    {
        id: 1,
        location: "Location 1",
        daily: 234,
        monthly: 3766,
        quarterly: 22436,
        overall: 22436,
    },
    {
        id: 2,
        location: "Location 2",
        daily: 234,
        monthly: 3766,
        quarterly: 22436,
        overall: 22436,
    },
    {
        id: 3,
        location: "Location 3",
        daily: 234,
        monthly: 3766,
        quarterly: 22436,
        overall: 22436,
    },
    {
        id: 4,
        location: "Location 4",
        daily: 234,
        monthly: 3766,
        quarterly: 22436,
        overall: 22436,
    },
    {
        id: 5,
        location: "Location 5",
        daily: 234,
        monthly: 3766,
        quarterly: 22436,
        overall: 22436,
    },
];
</script>
