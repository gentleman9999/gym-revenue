<template>
    <Card>
        <data-table
            :data="callRecords"
            :columns="columns"
            borderType="secondary"
            :rowBordered="true"
            :interactive="true"
        ></data-table>
    </Card>
</template>
<script setup>
import { h } from "vue";
import Card from "@/Components/Card.vue";
import DataTable from "@/Components/DataTable";
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button.vue";
const callRecords = [
    {
        id: 1,
        campaign: "Welcome Call",
        start_date: "04.22.22",
        end_date: "05.22.22",
    },
    {
        id: 2,
        campaign: "Welcome Call",
        start_date: "04.22.22",
        end_date: "05.22.22",
    },
];

const columns = [
    {
        field: "campaign",
        label: "Call Type",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full items-center justify-center" },
                value
            ),
    },
    {
        field: "start_date",
        label: "Sent Date",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full justify-center items-center" },
                value
            ),
    },
    {
        field: "end_date",
        label: "End Date",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full justify-center items-center" },
                value
            ),
    },
    {
        field: "",
        label: "Status",
        renderer: (value) =>
            h(
                "div",
                { class: "flex items-center justify-center h-full" },
                h(SelectBox, {
                    size: "xs",
                    label: "Status",
                    items: [
                        "Done",
                        "To Do",
                        "Reschedule",
                        "Confirmed",
                        "Show",
                        "No Show",
                    ],
                    color: "secondary",
                })
            ),
    },
    {
        field: "",
        label: "",
        noSeparator: true,
        renderer: (value) =>
            h(
                "div",
                { class: "flex items-center justify-center h-full space-x-8" },
                [
                    h(
                        "div",
                        { class: "hover:underline cursor-pointer" },
                        "View"
                    ),
                    h(Button, { size: "xs", secondary: true }, "Edit"),
                ]
            ),
    },
];
</script>
