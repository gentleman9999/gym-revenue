<template>
    <data-table
        class="h-80"
        :data="data"
        :columns="columns"
        borderType="secondary"
        :collapsed="true"
    />
</template>
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
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
    {
        name: "Chad Hodges",
        unit: "Unit #",
        membership: "Membership",
        revenue: "$0.00",
    },
];
</script>
