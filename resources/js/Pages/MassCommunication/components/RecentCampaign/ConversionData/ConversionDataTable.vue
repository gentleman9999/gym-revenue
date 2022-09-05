<template>
    <data-table
        class="conversion-table"
        :data="data"
        :columns="columns"
        borderType="secondary"
        :collapsed="true"
        noHeader
    >
        <template #thead>
            <thead class="text-secondary bg-neutral-900">
                <th></th>
                <th>Name</th>
                <th>Unit(s) Sold</th>
                <th>Membership(s) Sold</th>
                <th>Revenue</th>
            </thead>
        </template>
    </data-table>
</template>
<style>
.conversion-table {
    @apply h-80 overflow-x-auto;
    table {
        @apply w-max md:w-full;
    }
}
@media only screen and (max-width: 768px) {
    .conversion-table {
        width: calc(100vw - 96px);
    }
}
</style>
<script setup>
import { h } from "vue";
import DataTable from "@/Components/DataTable";
const columns = [
    {
        renderer: (value) => h("img", { src: "/img/avatars/chad.png" }),
    },
    {
        field: "name",
        label: "Name",
    },
    {
        field: "unit",
        label: "Unit(s) Sold",
    },
    {
        field: "membership",
        label: "Membership(s) Sold",
    },
    {
        field: "revenue",
        label: "Revenue",
    },
];

const data = [
    {
        id: 1,
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        id: 2,
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        id: 3,
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        id: 4,
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        id: 5,
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
];
</script>
