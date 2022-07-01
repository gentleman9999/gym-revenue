<template>
    <Card class="rounded">
        <div class="flex flex-col">
            <span class="text-secondary text-xl">Day 1</span>
            <data-table
                :data="callRecords_1"
                :columns="columns_1"
                borderType="secondary"
                :rowBordered="true"
                :interactive="true"
            ></data-table>
        </div>
        <div class="flex flex-col">
            <span class="text-secondary text-xl my-7">Day 2</span>
            <data-table
                :data="callRecords_2"
                :columns="columns_2"
                borderType="secondary"
                :rowBordered="true"
                :interactive="true"
            ></data-table>
        </div>
    </Card>
</template>
<script setup>
import { h } from "vue";
import Card from "@/Components/Card.vue";
import DataTable from "@/Components/DataTable";
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button.vue";
const callRecords_1 = [
    {
        id: 1,
        type: "Welcome Call",
        contact: "Kevin Buchanan",
        date: "04.22.22",
        avatar: "/img/avatars/calendar-avatar.png",
    },
    {
        id: 2,
        type: "Welcome Call",
        contact: "Nelle Howard",
        date: "04.22.22",
        avatar: "/img/avatars/calendar-avatar.png",
    },
    {
        id: 3,
        type: "Welcome Call",
        contact: "Roger Wright",
        date: "04.22.22",
        avatar: "/img/avatars/calendar-avatar.png",
    },
];

const callRecords_2 = [
    {
        id: 4,
        type: "Welcome Call",
        contact: "Kevin Buchanan",
        date: "04.22.22",
        avatar: "/img/avatars/calendar-avatar.png",
    },
    {
        id: 5,
        type: "Welcome Call",
        contact: "Nelle Howard",
        date: "04.22.22",
        avatar: "/img/avatars/calendar-avatar.png",
    },
    {
        id: 6,
        type: "Welcome Call",
        contact: "Roger Wright",
        date: "04.22.22",
        avatar: "/img/avatars/calendar-avatar.png",
    },
];

const columns_1 = [
    {
        field: "type",
        label: "Call Type",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full items-center justify-center" },
                value
            ),
    },
    {
        field: "contact",
        label: "Contact",
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
        field: "date",
        label: "Date",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full justify-center items-center" },
                value
            ),
    },
    {
        field: "avatar",
        label: "Employee",
        renderer: (value) => h("img", { src: value, class: "m-auto" }),
    },
    {
        field: "",
        label: "",
        noSeparator: true,
        renderer: (value) =>
            h(
                "div",
                { class: "flex items-center justify-center h-full" },
                h(Button, { size: "xs", secondary: true }, "Edit")
            ),
    },
];
const columns_2 = columns_1.map((item) => ({
    ...item,
    label: "",
}));
</script>
