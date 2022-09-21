<template>
    <sales-card-wrapper class="md:w-full">
        <sales-card
            class="w-full"
            :options="{
                favorite: true,
                collapse: true,
                borderedTitle: true,
            }"
        >
            <data-table
                :columns="columns"
                :data="data"
                class="mt-8 data-table"
            />
            <template #cardTitle>
                <div class="text-base-content text-bold text-lg">
                    Memberships
                </div>
            </template>
            <div class="text-center text-xl mt-9 mb-5">Cash Breakdown</div>
            <bill-table />
            <div class="flex flex-row space-x-4 justify-end my-3">
                <Button secondary size="xs">View</Button>
            </div>
        </sales-card>
    </sales-card-wrapper>
    <div class="flex flex-row justify-center mt-10 space-x-4">
        <Button size="xs" secondary>Print</Button>
        <Button size="xs" secondary>Close</Button>
    </div>
</template>
<style scoped>
@media only screen and (max-width: 768px) {
    .data-table {
        width: calc(100vw - 64px);
    }
}
</style>
<script setup>
import SalesCardWrapper from "../SalesCardWrapper.vue";
import SalesCard from "@/Components/Card.vue";
import Button from "@/Components/Button.vue";
import DataTable from "@/Components/DataTable";
import BillTable from "./BillTable.vue";

const columns = [
    {
        field: "club",
        label: "Club (1)",
    },
    {
        field: "conversions",
        label: "Conversions",
    },
    {
        field: "upgrades",
        label: "Upgrades",
    },
    {
        field: "downgrades",
        label: "Downgrades",
    },
    {
        field: "guest_passes",
        label: "Guest Passes",
    },
    {
        field: "down_payments",
        label: "Down Payments",
    },
];
const data = [
    {
        club: "Location 1",
        conversions: 10,
        upgrades: 13,
        downgrades: 4,
        guest_passes: 24,
        down_payments: 20,
    },
    {
        club: "Total",
        conversions: 10,
        upgrades: 13,
        downgrades: 4,
        guest_passes: 24,
        down_payments: 20,
    },
];
</script>
