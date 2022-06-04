<template>
    <card>
        <template #cardTitle>
            <div class="text-lg text-base-content">Actuals</div>
        </template>
        <div class="flex flex-col">
            <div class="flex flex-wrap text-sm">
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                diam nonumy eirmod tempor
            </div>
            <div>
                <data-table :columns="columns" :data="data" class="h-56" />
            </div>
        </div>
    </card>
</template>

<script setup>
import Card from "@/Components/Card";
import DataTable from "@/Components/DataTable";

const columns = [
    {
        field: "subject",
    },
    {
        field: "name",
    },
    {
        field: "notes",
    },
    {
        field: "count",
    },
];
const data = [
    {
        key: 1,
        subject: "Subject",
        name: "Name",
        notes: "Notes",
        count: 278,
    },
    {
        key: 2,
        subject: "Subject",
        name: "Name",
        notes: "Notes",
        count: 278,
    },
    {
        key: 3,
        subject: "Subject",
        name: "Name",
        notes: "Notes",
        count: 278,
    },
    {
        key: 4,
        subject: "Subject",
        name: "Name",
        notes: "Notes",
        count: 278,
    },
    {
        key: 5,
        subject: "Subject",
        name: "Name",
        notes: "Notes",
        count: 278,
    },
    {
        key: 6,
        subject: "Subject",
        name: "Name",
        notes: "Notes",
        count: 278,
    },
];
</script>
